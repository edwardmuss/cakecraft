<?php

namespace App\Http\Controllers;

use App\Events\InvoiceCreated;
use App\Events\InvoiceDelivered;
use App\Exports\InvoicesExport;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\StockMovement;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Laravel\Reverb\Loggers\Log;
use Maatwebsite\Excel\Facades\Excel;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        abort_if(!Auth::user()->can('view invoices'), 403, 'Unauthorized action.');

        $query = Invoice::with(['customer'])->where('branch_id', Auth::user()->branch_id);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('occasion_date')) {
            $query->whereDate('occasion_date', $request->occasion_date);
        }
        if ($request->filled('start_date')) {
            $query->whereDate('created_at', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('created_at', '<=', $request->end_date);
        }

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('invoice_number', 'like', "%{$searchTerm}%")
                    ->orWhereHas('customer', function ($subQ) use ($searchTerm) {
                        $subQ->where('name', 'like', "%{$searchTerm}%")
                            ->orWhere('organization_name', 'like', "%{$searchTerm}%")
                            ->orWhere('phone', 'like', "%{$searchTerm}%");
                    });
            });
        }

        $invoices = $query->latest()->paginate(50); // 50 per page is good for heavy mobile lists

        return view('invoices.index', compact('invoices', 'request'));
    }

    public function export(Request $request)
    {
        abort_if(!Auth::user()->can('view financials'), 403, 'Unauthorized action.');

        $branch_id = Auth::user()->branch_id;
        $timestamp = now()->format('Y_m_d_His');

        return Excel::download(new InvoicesExport($request, $branch_id), "invoices_{$timestamp}.xlsx");
    }

    public function create()
    {
        abort_if(!Auth::user()->can('create invoices'), 403, 'Unauthorized action.');

        $branch = Auth::user()->branch;
        $customers = Customer::where('branch_id', $branch->id)->orderBy('name', 'asc')->get();
        $catalogue = $this->catalogue($branch->id);
        $occasionTypes = \App\Services\OccasionService::TYPES;
        $invoice = null;

        return view('invoices.create', compact('customers', 'catalogue', 'branch', 'occasionTypes', 'invoice'));
    }

    /** Cakes with their size prices and flavours, for the invoice line picker. */
    private function catalogue(int $branchId): array
    {
        return Product::with(['sizePrices', 'flavours'])->where('branch_id', $branchId)->where('is_active', true)
            ->orderBy('name')->get()->map(fn ($p) => [
                'id' => $p->id,
                'name' => $p->name,
                'price' => (float) $p->unit_price,
                'sizes' => $p->sizePrices->where('is_active', true)->map(fn ($s) => [
                    'id' => $s->cake_size_id, 'label' => $s->size->label, 'price' => (float) $s->price,
                ])->values(),
                'flavours' => $p->flavours->map(fn ($f) => [
                    'id' => $f->id, 'name' => $f->name, 'extra' => (float) $f->pivot->extra_price,
                ])->values(),
            ])->values()->all();
    }

    private function rules(): array
    {
        return [
            'customer_id' => 'required|exists:customers,id',
            'due_date' => 'nullable|date',
            'occasion_type' => 'nullable|string|max:40',
            'occasion_date' => 'nullable|date',
            'recipient_name' => 'nullable|string|max:120',
            'fulfilment_type' => 'nullable|in:pickup,delivery',
            'delivery_address' => 'nullable|string|max:300',
            'shipping_fee' => 'nullable|numeric|min:0',
            'notes' => 'nullable|string',
            'status' => 'nullable|string',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'nullable|exists:products,id',
            'items.*.item_name' => 'nullable|string|max:200',
            'items.*.cake_size_id' => 'nullable|integer',
            'items.*.flavour_id' => 'nullable|integer',
            'items.*.cake_message' => 'nullable|string|max:150',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.unit_price' => 'nullable|numeric|min:0',
        ];
    }

    /**
     * Build invoice line rows. Price comes from the size grid (+ flavour surcharge)
     * unless staff typed a custom price for this cake.
     */
    private function buildItems(Request $request, int $branchId): array
    {
        $rows = [];
        $subtotal = 0;

        foreach ($request->items as $item) {
            $product = !empty($item['product_id']) ? Product::with(['sizePrices.size', 'flavours'])->where('branch_id', $branchId)->find($item['product_id']) : null;

            $price = $product ? (float) $product->unit_price : 0;
            $sizeId = null; $sizeLabel = null; $flavourName = null;

            if ($product && !empty($item['cake_size_id'])) {
                $sp = $product->sizePrices->firstWhere('cake_size_id', (int) $item['cake_size_id']);
                if ($sp) { $price = (float) $sp->price; $sizeId = $sp->cake_size_id; $sizeLabel = $sp->size->label; }
            }
            if ($product && !empty($item['flavour_id'])) {
                $fl = $product->flavours->firstWhere('id', (int) $item['flavour_id']);
                if ($fl) { $price += (float) $fl->pivot->extra_price; $flavourName = $fl->name; }
            }
            if (isset($item['unit_price']) && $item['unit_price'] !== '') {
                $price = (float) $item['unit_price'];
            }

            $name = $product->name ?? ($item['item_name'] ?? null);
            if (!$name) {
                abort(response()->json(['errors' => ['items' => ['Each line needs a cake or a description.']]], 422));
            }

            $line = $price * (int) $item['quantity'];
            $subtotal += $line;
            $rows[] = [
                'product_id' => $product?->id,
                'cake_size_id' => $sizeId,
                'size_label' => $sizeLabel,
                'flavour_name' => $flavourName,
                'cake_message' => $item['cake_message'] ?? null,
                'item_name' => $name,
                'quantity' => (int) $item['quantity'],
                'unit_price' => $price,
                'total' => $line,
            ];
        }

        return [$rows, $subtotal];
    }

    private function invoiceFields(Request $request): array
    {
        return [
            'customer_id' => $request->customer_id,
            'due_date' => $request->due_date,
            'occasion_type' => $request->occasion_type ?: null,
            'occasion_date' => $request->occasion_date ?: null,
            'recipient_name' => $request->recipient_name ?: null,
            'fulfilment_type' => $request->fulfilment_type ?: 'pickup',
            'delivery_address' => $request->delivery_address ?: null,
            'notes' => $request->notes,
        ];
    }

    public function store(Request $request)
    {
        abort_if(!Auth::user()->can('create invoices'), 403, 'Unauthorized action.');

        $validator = Validator::make($request->all(), $this->rules());
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $branch = Auth::user()->branch;
        [$rows, $subtotal] = $this->buildItems($request, $branch->id);

        $shippingFee = (float) ($request->shipping_fee ?? 0);
        $taxAmount = ($subtotal * (float) ($branch->vat_percentage ?? 0)) / 100;
        $totalAmount = $subtotal + $taxAmount + $shippingFee;

        $lastInvoice = Invoice::where('branch_id', $branch->id)->latest('id')->first();
        $invoiceNumber = 'INV-' . str_pad(($lastInvoice ? $lastInvoice->id + 1 : 1), 5, '0', STR_PAD_LEFT);

        $invoice = Invoice::create($this->invoiceFields($request) + [
            'branch_id' => $branch->id,
            'invoice_number' => $invoiceNumber,
            'status' => 'processing',
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'shipping_fee' => $shippingFee,
            'total_amount' => $totalAmount,
            'amount_paid' => 0,
            'source' => 'admin',
        ]);

        foreach ($rows as $row) {
            $invoice->items()->create($row);

            $product = $row['product_id'] ? Product::find($row['product_id']) : null;
            if ($product && $product->track_stock) {
                $newStock = $product->current_stock - $row['quantity'];
                $product->update(['current_stock' => $newStock]);
                StockMovement::create([
                    'branch_id' => $branch->id, 'product_id' => $product->id, 'user_id' => Auth::id(),
                    'type' => 'sale', 'quantity' => $row['quantity'], 'balance_after' => $newStock,
                    'reference' => 'Invoice ' . $invoiceNumber,
                ]);
            }
        }

        app(\App\Services\OccasionService::class)->syncFromInvoice($invoice);

        event(new InvoiceCreated($invoice));

        return response()->json([
            'success' => 'Invoice created successfully!',
            'redirect_url' => route('invoices.index'),
        ]);
    }

    public function markAsDelivered(Request $request, $id)
    {
        abort_if(!Auth::user()->can('manage deliveries'), 403, 'Unauthorized action.');

        $invoice = Invoice::where('branch_id', Auth::user()->branch_id)->findOrFail($id);

        if ($invoice->status !== 'delivered') {
            $invoice->update([
                'status' => 'delivered',
                'delivered_at' => now(),
            ]);

            event(new InvoiceDelivered($invoice));
        }

        return response()->json(['success' => 'Order marked as delivered and customer notified!']);
    }

    public function updateStatus(Request $request, $id)
    {
        // Must be a packer or delivery driver to quick-update statuses
        abort_if(!Auth::user()->can('pack orders') && !Auth::user()->can('manage deliveries'), 403, 'Unauthorized action.');

        $request->validate(['status' => 'required|string']);

        $invoice = Invoice::where('branch_id', Auth::user()->branch_id)->findOrFail($id);

        if ($invoice->status !== 'delivered' && $invoice->status !== 'cancelled') {
            $invoice->update(['status' => $request->status]);
        }

        return response()->json(['success' => 'Status updated successfully.']);
    }

    // ----------------------------------------------------
    // Method 1: For internal staff (checks RBAC & Branch)
    // ----------------------------------------------------
    public function downloadPdf($id)
    {
        abort_if(!Auth::user()->can('view financials'), 403, 'Unauthorized action.');

        $invoice = Invoice::with(['customer', 'items', 'branch'])->findOrFail($id);

        if ($invoice->branch_id !== Auth::user()->branch_id) {
            abort(403);
        }

        $pdf = Pdf::loadView('invoices.pdf', compact('invoice'));

        return $pdf->stream($invoice->invoice_number . '.pdf');
    }

    // ----------------------------------------------------
    // Method 2: For external customers (Relies on 'signed' middleware)
    // ----------------------------------------------------
    public function publicPdf(Request $request, $id)
    {
        // No Auth::user() checks here! The URL signature is our security.
        $invoice = Invoice::with(['customer', 'items', 'branch'])->findOrFail($id);

        $pdf = Pdf::loadView('invoices.pdf', compact('invoice'));

        return $pdf->stream($invoice->invoice_number . '.pdf');
    }

    public function publicPay($id)
    {
        // No Auth::user() required! URL signature guarantees authenticity.
        $invoice = Invoice::with(['customer', 'items', 'branch'])->findOrFail($id);

        // If already fully paid, maybe redirect to the PDF receipt instead
        if ($invoice->total_amount - $invoice->amount_paid <= 0) {
            return redirect()->route('invoices.public_pdf', $id)->with('success', 'This invoice is already fully paid.');
        }

        return view('invoices.public_pay', compact('invoice'));
    }

    public function show($id)
    {
        abort_if(!Auth::user()->can('view invoices'), 403, 'Unauthorized action.');

        $branch = Auth::user()->branch;
        $invoice = Invoice::with(['customer', 'items', 'payments'])->where('branch_id', $branch->id)->findOrFail($id);

        return view('invoices.show', compact('invoice', 'branch'));
    }

    public function edit($id)
    {
        abort_if(!Auth::user()->can('update invoices'), 403, 'Unauthorized action.');

        $branch = Auth::user()->branch;
        $invoice = Invoice::with('items')->where('branch_id', $branch->id)->findOrFail($id);
        $customers = Customer::where('branch_id', $branch->id)->orderBy('name', 'asc')->get();
        $catalogue = $this->catalogue($branch->id);
        $occasionTypes = \App\Services\OccasionService::TYPES;

        return view('invoices.edit', compact('invoice', 'customers', 'catalogue', 'branch', 'occasionTypes'));
    }

    public function update(Request $request, $id)
    {
        abort_if(!Auth::user()->can('update invoices'), 403, 'Unauthorized action.');

        $validator = Validator::make($request->all(), $this->rules());
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $branch = Auth::user()->branch;
        $invoice = Invoice::with('items')->where('branch_id', $branch->id)->findOrFail($id);

        [$rows, $subtotal] = $this->buildItems($request, $branch->id);

        $shippingFee = (float) ($request->shipping_fee ?? 0);
        $taxAmount = ($subtotal * (float) ($branch->vat_percentage ?? 0)) / 100;
        $totalAmount = $subtotal + $taxAmount + $shippingFee;

        $newStatus = $request->status ?? $invoice->status;
        if (in_array($newStatus, ['processing', 'partially_paid', 'paid'])) {
            if ($invoice->amount_paid >= $totalAmount && $totalAmount > 0) {
                $newStatus = 'paid';
            } elseif ($invoice->amount_paid > 0) {
                $newStatus = 'partially_paid';
            } else {
                $newStatus = 'processing';
            }
        }

        $invoice->update($this->invoiceFields($request) + [
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'shipping_fee' => $shippingFee,
            'total_amount' => $totalAmount,
            'status' => $newStatus,
        ]);

        // Return old stock, replace lines, deduct new stock
        foreach ($invoice->items as $old) {
            $prod = $old->product_id ? Product::find($old->product_id) : null;
            if ($prod && $prod->track_stock) $prod->increment('current_stock', $old->quantity);
        }
        $invoice->items()->delete();

        foreach ($rows as $row) {
            $invoice->items()->create($row);
            $product = $row['product_id'] ? Product::find($row['product_id']) : null;
            if ($product && $product->track_stock) {
                $newStock = $product->current_stock - $row['quantity'];
                $product->update(['current_stock' => $newStock]);
                StockMovement::create([
                    'branch_id' => $branch->id, 'product_id' => $product->id, 'user_id' => Auth::id(),
                    'type' => 'sale', 'quantity' => $row['quantity'], 'balance_after' => $newStock,
                    'reference' => 'Invoice Edit #' . $invoice->invoice_number,
                ]);
            }
        }

        app(\App\Services\OccasionService::class)->syncFromInvoice($invoice->fresh());

        return response()->json([
            'success' => 'Invoice updated successfully!',
            'redirect_url' => route('invoices.show', $invoice->id),
        ]);
    }

    public function destroy($id)
    {
        abort_if(!Auth::user()->can('delete invoices'), 403, 'Unauthorized action.');

        $invoice = Invoice::with('items')->where('branch_id', Auth::user()->branch_id)->findOrFail($id);

        // 1. Prevent deletion if payments exist!
        if ($invoice->amount_paid > 0 || $invoice->payments()->exists()) {
            return response()->json([
                'message' => 'Cannot delete an invoice that has recorded payments. Please cancel it or remove the payments first.'
            ], 400);
        }

        // 2. Return stock to inventory before deleting
        foreach ($invoice->items as $item) {
            $product = Product::find($item->product_id);

            if ($product && $product->track_stock) {
                $newStock = $product->current_stock + $item->quantity;
                $product->update(['current_stock' => $newStock]);

                // Log the stock being returned
                StockMovement::create([
                    'branch_id' => $invoice->branch_id,
                    'product_id' => $product->id,
                    'user_id' => Auth::id(),
                    'type' => 'adjustment',
                    'quantity' => $item->quantity,
                    'balance_after' => $newStock,
                    'reference' => 'Deleted Invoice #' . $invoice->invoice_number,
                ]);
            }
        }

        // 3. Delete the line items, then the invoice
        $invoice->items()->delete();
        $invoice->delete();

        return response()->json(['success' => 'Invoice deleted successfully and stock returned!']);
    }

    public function sendReminder($id, \App\Services\SMSService $smsService)
    {
        abort_if(!Auth::user()->can('view financials'), 403, 'Unauthorized action.');

        $invoice = Invoice::with(['customer', 'branch'])->where('branch_id', Auth::user()->branch_id)->findOrFail($id);
        $balance = $invoice->total_amount - $invoice->amount_paid;

        if ($balance <= 0) {
            return response()->json(['success' => false, 'message' => 'This invoice is already fully paid.']);
        }

        $customer = $invoice->customer;
        $branch = $invoice->branch;

        if (!$customer->phone) {
            return response()->json(['success' => false, 'message' => 'Customer does not have a phone number on file.']);
        }

        // Determine which template to use based on days overdue
        $daysOverdue = \Carbon\Carbon::parse($invoice->due_date)->diffInDays(today());
        $triggerName = $daysOverdue >= 7 ? 'aging_reminder_7_days' : 'aging_reminder_1_day';

        $template = \App\Models\SmsTemplate::where('branch_id', $branch->id)
            ->where('trigger_name', $triggerName)
            ->where('is_active', true)
            ->first();

        // Generate Secure Links
        $invoiceLink = \Illuminate\Support\Facades\URL::temporarySignedRoute(
            'invoices.public_pdf',
            now()->addDays(7),
            ['id' => $invoice->id]
        );
        $paymentLink = \Illuminate\Support\Facades\URL::temporarySignedRoute(
            'invoices.public_pay',
            now()->addDays(7),
            ['id' => $invoice->id]
        );

        // Format Order Details (e.g., "150x Prepack Cups, 2x Wafers")
        $orderDetails = $invoice->items->map(function ($item) {
            return $item->quantity . 'Pcs ' . $item->item_name;
        })->implode(', ');

        $rawMessage = $template && $template->message
            ? $template->message
            : "Hello {CustomerName}, this is a reminder that Invoice #{InvoiceNumber} has an outstanding balance of KES {Balance}. Please click here to pay: {PaymentLink}";

        $message = str_replace(
            ['{CustomerName}', '{Organization}', '{InvoiceNumber}', '{TotalAmount}', '{Balance}', '{BranchName}', '{InvoiceLink}', '{PaymentLink}', '{OrderDetails}'],
            [$customer->name, $customer->organization_name ?? $branch->branch_name, $invoice->invoice_number, number_format($invoice->total_amount, 2), number_format($balance, 2), $branch->branch_name, $invoiceLink, $paymentLink, $orderDetails],
            $rawMessage
        );

        Log::info("Sending reminder for Invoice #{$invoice->invoice_number} to {$customer->phone}. Message: {$message}");

        try {
            $response = $smsService->sendSMS($customer->phone, $message);
            $isSuccessful = isset($response['status']) && $response['status'] == 'success';

            \App\Models\NotificationLog::create([
                'customer_id' => $customer->id,
                'invoice_id' => $invoice->id,
                'trigger_type' => 'manual_reminder',
                'message' => $message,
                'is_successful' => $isSuccessful,
            ]);

            $invoice->increment('reminder_count');

            if ($isSuccessful) {
                return response()->json(['success' => true, 'message' => 'Reminder sent successfully!', 'reminder_count' => $invoice->reminder_count]);
            } else {
                return response()->json(['success' => false, 'message' => 'Failed to send SMS via gateway.']);
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error("Failed to send manual reminder for Invoice #{$invoice->invoice_number}: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'System error while sending reminder.']);
        }
    }
}
