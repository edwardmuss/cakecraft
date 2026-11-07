<?php

namespace App\Http\Controllers\Api;

use App\Events\InvoiceCreated;
use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Models\Invoice;
use App\Models\Product;
use App\Models\StockMovement;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class InvoiceController extends Controller
{
    public function index(Request $request)
    {
        if (!$request->user()->can('view invoices')) {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }

        $query = Invoice::with(['customer', 'items', 'payments'])->where('branch_id', $request->user()->branch_id);

        // --- Adaptive Search ---
        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('invoice_number', 'like', "%{$search}%")
                    ->orWhere('status', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($customerQuery) use ($search) {
                        $customerQuery->where('name', 'like', "%{$search}%")
                            ->orWhere('organization_name', 'like', "%{$search}%");
                    });
            });
        }

        $invoices = $query->latest()->paginate(20); // 20 per page is good for heavy mobile lists

        return response()->json($invoices);
    }

    public function show(Request $request, $id)
    {
        if (!$request->user()->can('view invoices')) {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }

        $invoice = Invoice::with(['customer', 'items', 'payments'])->where('branch_id', $request->user()->branch_id)->findOrFail($id);
        return response()->json($invoice);
    }

    public function store(Request $request)
    {
        if (!$request->user()->can('create invoices')) {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|exists:customers,id',
            'due_date' => 'nullable|date',
            'communion_month' => 'required|string|size:7', // expects "YYYY-MM"
            'communion_week'  => 'required|integer|min:1|max:5',
            'notes' => 'nullable|string',
            'shipping_fee' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
            'items.*.tray_count' => 'nullable|integer',
            'items.*.tray_type' => 'nullable|string|in:rec,dis',
        ]);

        if ($validator->fails()) return response()->json(['errors' => $validator->errors()], 422);

        $branch = $request->user()->branch;
        $subtotal = 0;
        $invoiceItemsData = [];

        foreach ($request->items as $item) {
            $product = Product::find($item['product_id']);
            $lineTotal = $product->unit_price * $item['quantity'];
            $subtotal += $lineTotal;

            if ($product->track_stock && $item['quantity'] > $product->current_stock) {
                return response()->json(['errors' => ['items' => ["Not enough stock for {$product->name}"]]], 422);
            }

            $invoiceItemsData[] = [
                'product_id' => $product->id,
                'item_name' => $product->name,
                'quantity' => $item['quantity'],
                'tray_count' => $item['tray_count'] ?? 0,
                'tray_type' => $item['tray_type'] ?? null,
                'unit_price' => $product->unit_price,
                'total' => $lineTotal,
            ];
        }

        $shippingFee = $request->shipping_fee ?? 0;
        $taxAmount = ($subtotal * ($branch->vat_percentage ?? 0)) / 100;
        $totalAmount = $subtotal + $taxAmount + $shippingFee;

        $lastInvoice = Invoice::where('branch_id', $branch->id)->latest('id')->first();
        $nextId = $lastInvoice ? $lastInvoice->id + 1 : 1;
        $invoiceNumber = 'INV-' . str_pad($nextId, 5, '0', STR_PAD_LEFT);

        $invoice = Invoice::create([
            'branch_id' => $branch->id,
            'customer_id' => $request->customer_id,
            'invoice_number' => $invoiceNumber,
            'status' => 'processing',
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'shipping_fee' => $shippingFee,
            'total_amount' => $totalAmount,
            'amount_paid' => 0,
            'due_date' => $request->due_date,
            'communion_month' => $request->communion_month,
            'communion_week'  => $request->communion_week,
            'notes' => $request->notes,
        ]);

        foreach ($invoiceItemsData as $itemData) {
            $invoice->items()->create($itemData);
            $product = Product::find($itemData['product_id']);

            if ($product && $product->track_stock) {
                $newStock = $product->current_stock - $itemData['quantity'];
                $product->update(['current_stock' => $newStock]);

                StockMovement::create([
                    'branch_id' => $branch->id,
                    'product_id' => $product->id,
                    'user_id' => $request->user()->id,
                    'type' => 'sale',
                    'quantity' => $itemData['quantity'],
                    'balance_after' => $newStock,
                    'reference' => 'Invoice ' . $invoiceNumber,
                ]);
            }
        }

        event(new InvoiceCreated($invoice));

        return response()->json(['success' => true, 'message' => 'Invoice created successfully!']);
    }

    public function update(Request $request, $id)
    {
        if (!$request->user()->can('update invoices')) {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'customer_id' => 'required|exists:customers,id',
            'due_date' => 'nullable|date',
            'communion_month' => 'required|string|size:7', // expects "YYYY-MM"
            'communion_week'  => 'required|integer|min:1|max:5',
            'notes' => 'nullable|string',
            'status' => 'nullable|string',
            'shipping_fee' => 'nullable|numeric|min:0',
            'items' => 'required|array|min:1',
            'items.*.product_id' => 'required|exists:products,id',
            'items.*.quantity' => 'required|integer|min:1',
        ]);

        if ($validator->fails()) return response()->json(['errors' => $validator->errors()], 422);

        $branch = $request->user()->branch;
        $invoice = Invoice::with('items')->where('branch_id', $branch->id)->findOrFail($id);

        $subtotal = 0;
        $invoiceItemsData = [];

        foreach ($request->items as $item) {
            $product = Product::find($item['product_id']);
            $lineTotal = $product->unit_price * $item['quantity'];
            $subtotal += $lineTotal;

            $invoiceItemsData[] = [
                'product_id' => $product->id,
                'item_name' => $product->name,
                'quantity' => $item['quantity'],
                'tray_count' => $item['tray_count'] ?? 0,
                'tray_type' => $item['tray_type'] ?? null,
                'unit_price' => $product->unit_price,
                'total' => $lineTotal,
            ];
        }

        $shippingFee = $request->shipping_fee ?? 0;
        $taxAmount = ($subtotal * ($branch->vat_percentage ?? 0)) / 100;
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

        $invoice->update([
            'customer_id' => $request->customer_id,
            'subtotal' => $subtotal,
            'tax_amount' => $taxAmount,
            'shipping_fee' => $shippingFee,
            'total_amount' => $totalAmount,
            'status' => $newStatus,
            'due_date' => $request->due_date,
            'communion_month' => $request->communion_month,
            'communion_week'  => $request->communion_week,
            'notes' => $request->notes,
        ]);

        // Return old stock
        foreach ($invoice->items as $oldItem) {
            $prod = Product::find($oldItem->product_id);
            if ($prod && $prod->track_stock) $prod->increment('current_stock', $oldItem->quantity);
        }
        $invoice->items()->delete();

        // Deduct new stock
        foreach ($invoiceItemsData as $itemData) {
            $invoice->items()->create($itemData);
            $product = Product::find($itemData['product_id']);
            if ($product && $product->track_stock) {
                $newStock = $product->current_stock - $itemData['quantity'];
                $product->update(['current_stock' => $newStock]);

                StockMovement::create([
                    'branch_id' => $branch->id,
                    'product_id' => $product->id,
                    'user_id' => $request->user()->id,
                    'type' => 'sale',
                    'quantity' => $itemData['quantity'],
                    'balance_after' => $newStock,
                    'reference' => 'Invoice Edit #' . $invoice->invoice_number,
                ]);
            }
        }

        return response()->json(['success' => true, 'message' => 'Invoice updated successfully!']);
    }

    // Reuse your existing logic for delete, but return JSON
    public function destroy(Request $request, $id)
    {
        if (!$request->user()->can('delete invoices')) {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }

        $invoice = Invoice::with('items')->where('branch_id', $request->user()->branch_id)->findOrFail($id);

        if ($invoice->amount_paid > 0 || $invoice->payments()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete an invoice that has recorded payments.'
            ], 400);
        }

        // Return stock and delete items (same logic as your web controller)
        foreach ($invoice->items as $item) {
            $product = \App\Models\Product::find($item->product_id);
            if ($product && $product->track_stock) {
                $product->increment('current_stock', $item->quantity);
            }
        }

        $invoice->items()->delete();
        $invoice->delete();

        return response()->json(['success' => true, 'message' => 'Invoice deleted successfully!']);
    }

    public function dependencies(Request $request)
    {
        $branchId = $request->user()->branch_id;

        return response()->json([
            'customers' => Customer::where('branch_id', $branchId)->orderBy('organization_name', 'asc')->get(),
            'products' => Product::where('branch_id', $branchId)->where('is_active', true)->get(),
            'branch' => $request->user()->branch, // To get vat_percentage
        ]);
    }

    public function sendReminder(Request $request, $id, \App\Services\SMSService $smsService)
    {
        if (!$request->user()->can('view financials')) {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }

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

    public function markAsDelivered(Request $request, $id)
    {
        if (!$request->user()->can('manage deliveries')) {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }

        $request->validate([
            'returned_trays' => 'nullable|integer|min:0'
        ]);

        $invoice = Invoice::where('branch_id', $request->user()->branch_id)->findOrFail($id);

        if ($invoice->status !== 'delivered') {
            $invoice->update([
                'status' => 'delivered',
                'delivered_at' => now(),
                'returned_trays' => $request->returned_trays ?? 0,
            ]);

            event(new \App\Events\InvoiceDelivered($invoice));
        }

        return response()->json(['success' => true, 'message' => 'Order marked as delivered and customer notified!']);
    }

    public function updateStatus(Request $request, $id)
    {
        if (!$request->user()->can('pack orders') && !$request->user()->can('manage deliveries')) {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }

        $request->validate(['status' => 'required|string']);

        $invoice = Invoice::where('branch_id', $request->user()->branch_id)->findOrFail($id);

        if ($invoice->status !== 'delivered' && $invoice->status !== 'cancelled') {
            $invoice->update(['status' => $request->status]);
        }

        return response()->json(['success' => true, 'message' => 'Status updated successfully.']);
    }

    public function downloadPdf(Request $request, $id)
    {
        // RBAC Check
        if (!$request->user()->can('view financials')) {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }

        $invoice = Invoice::with(['customer', 'items', 'branch'])->findOrFail($id);

        // Branch Security Check
        if ($invoice->branch_id !== $request->user()->branch_id) {
            return response()->json(['message' => 'Unauthorized access.'], 403);
        }

        // Generate PDF
        $pdf = Pdf::loadView('invoices.pdf', compact('invoice'));

        // Return as a forced download so Flutter's Dio can write it to the file path
        return $pdf->stream($invoice->invoice_number . '.pdf');
    }
}
