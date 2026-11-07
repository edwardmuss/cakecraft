<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Services\SMSService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReceivablesController extends Controller
{
    public function index(Request $request)
    {
        abort_if(!Auth::user()->can('view financials'), 403);

        $today = now()->startOfDay();
        $search = trim((string) $request->query('search', ''));

        $invoices = Invoice::with('customer:id,name,phone')
            ->where('branch_id', Auth::user()->branch_id)
            ->where('status', '!=', 'cancelled')
            ->whereColumn('amount_paid', '<', 'total_amount')
            ->when($search !== '', fn ($q) => $q->whereHas('customer', fn ($c) => $c->where('name', 'like', "%{$search}%")->orWhere('phone', 'like', "%{$search}%")))
            ->orderBy('due_date')->orderBy('id')
            ->get();

        $rows = $invoices->groupBy('customer_id')->map(function ($group) use ($today) {
            $customer = $group->first()->customer;
            $list = $group->map(function ($i) use ($today) {
                $balance = max(0, (float) $i->total_amount - (float) $i->amount_paid);
                $due = $i->due_date ? \Carbon\Carbon::parse($i->due_date)->startOfDay() : null;
                return (object) [
                    'id' => $i->id,
                    'number' => $i->invoice_number,
                    'date' => $i->created_at,
                    'due_date' => $due,
                    'total' => (float) $i->total_amount,
                    'paid' => (float) $i->amount_paid,
                    'balance' => $balance,
                    'overdue_days' => ($due && $due->lt($today)) ? $due->diffInDays($today) : 0,
                ];
            });
            return (object) [
                'customer_id' => $group->first()->customer_id,
                'name' => $customer->name ?? 'Unknown customer',
                'phone' => $customer->phone ?? '',
                'invoices' => $list,
                'count' => $list->count(),
                'billed' => $list->sum('total'),
                'paid' => $list->sum('paid'),
                'balance' => $list->sum('balance'),
                'overdue' => $list->where('overdue_days', '>', 0)->sum('balance'),
                'age' => [
                    'current' => $list->where('overdue_days', 0)->sum('balance'),
                    'd30' => $list->whereBetween('overdue_days', [1, 30])->sum('balance'),
                    'd60' => $list->whereBetween('overdue_days', [31, 60])->sum('balance'),
                    'd90' => $list->whereBetween('overdue_days', [61, 90])->sum('balance'),
                    'd90p' => $list->where('overdue_days', '>', 90)->sum('balance'),
                ],
            ];
        })->sortByDesc('balance')->values();

        if ($request->query('export') === 'csv') {
            return response()->streamDownload(function () use ($rows) {
                $out = fopen('php://output', 'w');
                fputcsv($out, ['Customer', 'Phone', 'Open invoices', 'Billed', 'Paid', 'Outstanding', 'Current', '1-30 days', '31-60 days', '61-90 days', '90+ days']);
                foreach ($rows as $r) {
                    fputcsv($out, [$r->name, $r->phone, $r->count, $r->billed, $r->paid, $r->balance, $r->age['current'], $r->age['d30'], $r->age['d60'], $r->age['d90'], $r->age['d90p']]);
                }
                fclose($out);
            }, 'receivables-' . now()->format('Y-m-d') . '.csv', ['Content-Type' => 'text/csv']);
        }

        $totals = (object) [
            'customers' => $rows->count(),
            'invoices' => $rows->sum('count'),
            'balance' => $rows->sum('balance'),
            'overdue' => $rows->sum('overdue'),
            'age' => collect(['current', 'd30', 'd60', 'd90', 'd90p'])->mapWithKeys(fn ($k) => [$k => $rows->sum(fn ($r) => $r->age[$k])])->all(),
        ];

        return view('reports.receivables', compact('rows', 'totals', 'search'));
    }

    /** SMS a customer about what they owe. The message is written/edited by staff in the browser. */
    public function remind(Request $request, $customerId)
    {
        abort_if(!Auth::user()->can('view financials'), 403);

        $data = $request->validate(['message' => 'required|string|min:10|max:480']);
        $branchId = Auth::user()->branch_id;
        $customer = Customer::where('branch_id', $branchId)->findOrFail($customerId);

        if (!$customer->phone) {
            return response()->json(['message' => 'This customer has no phone number.'], 422);
        }

        $unpaid = Invoice::where('branch_id', $branchId)->where('customer_id', $customer->id)
            ->where('status', '!=', 'cancelled')->whereColumn('amount_paid', '<', 'total_amount');
        if (!(clone $unpaid)->exists()) {
            return response()->json(['message' => 'This customer has nothing outstanding.'], 422);
        }

        try {
            $resp = app(SMSService::class)->sendSMS($customer->phone, $data['message']);
            if (is_array($resp) && (($resp['success'] ?? true) === false || isset($resp['error']))) {
                return response()->json(['message' => 'The SMS gateway rejected the message.'], 502);
            }
        } catch (\Throwable $e) {
            report($e);
            return response()->json(['message' => 'Could not reach the SMS gateway.'], 502);
        }

        $unpaid->increment('reminder_count');

        return response()->json(['success' => 'Reminder sent to ' . $customer->name . '.']);
    }
}
