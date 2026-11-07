<?php

namespace App\Http\Controllers\Api;

use App\Events\PaymentReceived;
use App\Http\Controllers\Controller;
use App\Models\Invoice;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
// use App\Events\PaymentReceived; 

class PaymentController extends Controller
{
    // --- GET ALL PAYMENTS (WITH FILTERS) ---
    public function index(Request $request)
    {
        if (!$request->user()->can('view financials')) {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }

        $branchId = $request->user()->branch_id;
        $filter = $request->get('filter', 'last_30_days');

        $query = Payment::with(['invoice.customer'])->where('branch_id', $branchId);

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                // Search Payment Ref or Method
                $q->where('reference', 'like', "%{$search}%")
                    ->orWhere('method', 'like', "%{$search}%")
                    // Deep Search: Invoice Number
                    ->orWhereHas('invoice', function ($invQuery) use ($search) {
                        $invQuery->where('invoice_number', 'like', "%{$search}%")
                            // Deeper Search: Customer Name
                            ->orWhereHas('customer', function ($custQuery) use ($search) {
                                $custQuery->where('name', 'like', "%{$search}%")
                                    ->orWhere('organization_name', 'like', "%{$search}%");
                            });
                    });
            });
        }

        if ($filter !== 'all') {
            $endDate = Carbon::now()->endOfDay();

            switch ($filter) {
                case 'today':
                    $startDate = Carbon::today()->startOfDay();
                    break;
                case 'this_week':
                    $startDate = Carbon::now()->startOfWeek();
                    break;
                case 'this_month':
                    $startDate = Carbon::now()->startOfMonth();
                    break;
                case 'this_year':
                    $startDate = Carbon::now()->startOfYear();
                    break;
                case 'custom':
                    $startDate = $request->start_date ? Carbon::parse($request->start_date)->startOfDay() : Carbon::today()->startOfDay();
                    $endDate = $request->end_date ? Carbon::parse($request->end_date)->endOfDay() : Carbon::now()->endOfDay();
                    break;
                case 'last_30_days':
                default:
                    $startDate = Carbon::today()->subDays(30)->startOfDay();
                    break;
            }

            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        $payments = $query->latest()->paginate(20);

        return response()->json($payments);
    }

    // --- RECORD NEW PAYMENT ---
    public function store(Request $request)
    {
        if (!$request->user()->can('record payments')) {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }

        $branchId = $request->user()->branch_id;

        $validator = Validator::make($request->all(), [
            'invoice_id' => [
                'required',
                Rule::exists('invoices', 'id')->where('branch_id', $branchId)
            ],
            'amount' => 'required|numeric|min:0.01',
            'method' => 'required|string',
            'reference' => [
                'required',
                'string',
                Rule::unique('payments')->where(function ($query) use ($request) {
                    return $query->where('method', $request->method);
                })
            ],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $invoice = Invoice::where('branch_id', $branchId)->findOrFail($request->invoice_id);
        $balance = $invoice->total_amount - $invoice->amount_paid;

        if ($request->amount > $balance) {
            return response()->json([
                'errors' => ['amount' => ["Payment amount cannot exceed the balance due of {$balance}"]]
            ], 422);
        }

        $payment = Payment::create([
            'branch_id' => $branchId,
            'invoice_id' => $invoice->id,
            'amount' => $request->amount,
            'method' => $request->method,
            'reference' => $request->reference,
            'status' => 'completed',
        ]);

        $newAmountPaid = $invoice->amount_paid + $request->amount;
        $newStatus = $invoice->status;

        // Logistics Protection: Don't downgrade status if already packed/dispatched/delivered
        if (!in_array($invoice->status, ['packed', 'dispatched', 'delivered'])) {
            if ($newAmountPaid >= $invoice->total_amount) {
                $newStatus = 'paid';
            } elseif ($newAmountPaid > 0) {
                $newStatus = 'partially_paid';
            }
        }

        $invoice->update([
            'amount_paid' => $newAmountPaid,
            'status' => $newStatus,
        ]);

        event(new PaymentReceived($payment));

        return response()->json(['success' => 'Payment recorded successfully!', 'payment' => $payment]);
    }

    // --- FETCH SINGLE PAYMENT ---
    public function show(Request $request, $id)
    {
        if (!$request->user()->can('view financials')) {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }

        $payment = Payment::with('invoice.customer')->where('branch_id', $request->user()->branch_id)->findOrFail($id);
        return response()->json($payment);
    }

    // --- UPDATE: MODIFY EXISTING PAYMENT ---
    public function update(Request $request, $id)
    {
        if (!$request->user()->can('record payments')) { // Adjust permission as needed
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }

        $branchId = $request->user()->branch_id;
        $payment = Payment::with('invoice')->where('branch_id', $branchId)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0.01',
            'method' => 'required|string',
            'reference' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $invoice = $payment->invoice;

        // Calculate the difference to adjust the invoice's amount_paid safely
        $difference = $request->amount - $payment->amount;
        $newAmountPaid = $invoice->amount_paid + $difference;

        if ($newAmountPaid > $invoice->total_amount) {
            return response()->json([
                'errors' => ['amount' => ["This edit would overpay the invoice by " . ($newAmountPaid - $invoice->total_amount)]]
            ], 422);
        }

        $payment->update([
            'amount' => $request->amount,
            'method' => $request->method,
            'reference' => $request->reference,
        ]);

        $newStatus = $invoice->status;

        // Logistics Protection
        if (!in_array($invoice->status, ['packed', 'dispatched', 'delivered'])) {
            if ($newAmountPaid >= $invoice->total_amount) {
                $newStatus = 'paid';
            } elseif ($newAmountPaid > 0) {
                $newStatus = 'partially_paid';
            } else {
                $newStatus = 'processing';
            }
        }

        $invoice->update([
            'amount_paid' => $newAmountPaid,
            'status' => $newStatus,
        ]);

        return response()->json(['success' => 'Payment updated successfully!', 'payment' => $payment]);
    }

    // --- REMOVE PAYMENT (Optional but good for full CRUD) ---
    public function destroy(Request $request, $id)
    {
        if (!$request->user()->can('record payments')) {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }

        $payment = Payment::with('invoice')->where('branch_id', $request->user()->branch_id)->findOrFail($id);
        $invoice = $payment->invoice;

        // Deduct from invoice
        $newAmountPaid = $invoice->amount_paid - $payment->amount;

        $newStatus = $invoice->status;
        if (!in_array($invoice->status, ['packed', 'dispatched', 'delivered'])) {
            $newStatus = $newAmountPaid > 0 ? 'partially_paid' : 'processing';
        }

        $invoice->update([
            'amount_paid' => $newAmountPaid,
            'status' => $newStatus,
        ]);

        $payment->delete();

        return response()->json(['success' => 'Payment deleted successfully.']);
    }

    // --- DOWNLOAD RECEIPT ---
    public function downloadReceipt(Request $request, $id)
    {
        if (!$request->user()->can('view financials')) {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }

        $payment = Payment::with(['invoice.customer', 'branch'])->findOrFail($id);

        if ($payment->branch_id !== $request->user()->branch_id) {
            return response()->json(['message' => 'Unauthorized access to this receipt.'], 403);
        }

        // Return the raw PDF bytes. Flutter's Dio can download this directly!
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('payments.receipt_pdf', compact('payment'));
        return $pdf->stream('Receipt_' . $payment->reference . '.pdf');
    }
}
