<?php

namespace App\Exports;

use App\Models\Invoice;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;

class InvoicesExport implements FromQuery, WithHeadings, WithMapping
{
    use Exportable;

    protected $request;
    protected $branch_id;

    public function __construct(Request $request, $branch_id)
    {
        $this->request = $request;
        $this->branch_id = $branch_id;
    }

    public function query()
    {
        $query = Invoice::query()
            ->with('customer')
            ->where('branch_id', $this->branch_id);

        // Apply the same filters used in the index view
        if ($this->request->filled('status')) {
            $query->where('status', $this->request->status);
        }
        if ($this->request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $this->request->start_date);
        }
        if ($this->request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $this->request->end_date);
        }

        return $query->latest();
    }

    public function headings(): array
    {
        return [
            'Invoice Number',
            'Customer Name',
            'Phone',
            'Date Created',
            'Due Date',
            'Subtotal (KES)',
            'Tax (KES)',
            'Total Amount (KES)',
            'Amount Paid (KES)',
            'Balance (KES)',
            'Status'
        ];
    }

    public function map($invoice): array
    {
        return [
            $invoice->invoice_number,
            $invoice->customer->name ?? 'N/A',
            $invoice->customer->phone ?? 'N/A',
            $invoice->created_at->format('Y-m-d'),
            $invoice->due_date ? \Carbon\Carbon::parse($invoice->due_date)->format('Y-m-d') : 'N/A',
            $invoice->subtotal,
            $invoice->tax_amount,
            $invoice->total_amount,
            $invoice->amount_paid,
            $invoice->total_amount - $invoice->amount_paid,
            ucwords(str_replace('_', ' ', $invoice->status)),
        ];
    }
}
