<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Payment;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StatementController extends Controller
{
    public function index(Request $request)
    {
        $branchId = Auth::user()->branch_id;
        $customers = Customer::where('branch_id', $branchId)->orderBy('name')->get();
        $statement = null;

        // If the user submitted the filter form, generate the statement
        if ($request->filled('customer_id') && $request->filled('start_date') && $request->filled('end_date')) {
            $statement = $this->generateStatementData($request->customer_id, $request->start_date, $request->end_date, $branchId);
        }

        return view('statements.index', compact('customers', 'statement'));
    }

    public function downloadPdf(Request $request)
    {
        $request->validate([
            'customer_id' => 'required',
            'start_date' => 'required|date',
            'end_date' => 'required|date',
        ]);

        $branchId = Auth::user()->branch_id;
        $branch = Auth::user()->branch;
        $statement = $this->generateStatementData($request->customer_id, $request->start_date, $request->end_date, $branchId);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('statements.pdf', compact('statement', 'branch'));

        $fileName = 'Statement_' . preg_replace('/[^A-Za-z0-9]/', '_', $statement['customer']->name) . '_' . now()->format('Ymd') . '.pdf';

        return $pdf->stream($fileName);
    }

    /**
     * Core Engine to calculate Opening Balances and interleave Transactions
     */
    private function generateStatementData($customerId, $startDate, $endDate, $branchId)
    {
        $customer = Customer::where('branch_id', $branchId)->findOrFail($customerId);
        $start = Carbon::parse($startDate)->startOfDay();
        $end = Carbon::parse($endDate)->endOfDay();

        // 1. Calculate Opening Balance (Everything BEFORE the start date)
        $priorInvoices = Invoice::where('customer_id', $customerId)
            ->where('status', '!=', 'cancelled')
            ->where('created_at', '<', $start)
            ->sum('total_amount');

        $priorPayments = Payment::whereHas('invoice', function ($q) use ($customerId) {
            $q->where('customer_id', $customerId);
        })
            ->where('status', 'completed')
            ->where('created_at', '<', $start)
            ->sum('amount');

        $openingBalance = $priorInvoices - $priorPayments;

        // 2. Fetch Period Invoices (Debits / Charges)
        $invoices = Invoice::where('customer_id', $customerId)
            ->where('status', '!=', 'cancelled')
            ->whereBetween('created_at', [$start, $end])
            ->get()
            ->map(function ($inv) {
                // Safely format the due date
                $dueDate = $inv->due_date ? \Carbon\Carbon::parse($inv->due_date)->format('d M Y') : 'N/A';

                return [
                    'date' => $inv->created_at, // MUST stay as created_at for accurate running balance
                    'type' => 'Invoice',
                    'reference' => $inv->invoice_number,

                    // Make the description highly specific to your business model!
                    'description' => "Communion: {$inv->communion_month} (Wk {$inv->communion_week}) | Due: {$dueDate}",

                    'debit' => $inv->total_amount,
                    'credit' => 0,
                ];
            });

        // 3. Fetch Period Payments (Credits / Receipts)
        $payments = Payment::whereHas('invoice', function ($q) use ($customerId) {
            $q->where('customer_id', $customerId);
        })
            ->where('status', 'completed')
            ->whereBetween('created_at', [$start, $end])
            ->get()
            ->map(function ($pay) {
                return [
                    'date' => $pay->created_at,
                    'type' => 'Payment',
                    'reference' => $pay->reference ?? 'PAY-' . $pay->id,
                    'description' => $pay->method . ' Payment (Ref: ' . ($pay->invoice->invoice_number ?? '') . ')',
                    'debit' => 0,
                    'credit' => $pay->amount, // Reduces what they owe
                ];
            });

        // 4. Merge, Sort chronologically, and calculate Running Balance
        $transactions = $invoices->concat($payments)->sortBy('date')->values();

        $runningBalance = $openingBalance;
        $totalDebit = 0;
        $totalCredit = 0;

        $processedTransactions = $transactions->map(function ($t) use (&$runningBalance, &$totalDebit, &$totalCredit) {
            $runningBalance += $t['debit'];
            $runningBalance -= $t['credit'];

            $totalDebit += $t['debit'];
            $totalCredit += $t['credit'];

            $t['balance'] = $runningBalance;
            return $t;
        });

        return [
            'customer' => $customer,
            'start_date' => $start,
            'end_date' => $end,
            'opening_balance' => $openingBalance,
            'transactions' => $processedTransactions,
            'closing_balance' => $runningBalance,
            'total_debit' => $totalDebit,
            'total_credit' => $totalCredit
        ];
    }
}
