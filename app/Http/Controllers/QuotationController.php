<?php

namespace App\Http\Controllers;

use App\Models\Quotation;
use App\Models\QuotationItem;
use App\Models\Invoice;
use App\Models\InvoiceItem;
use App\Models\Customer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Services\CakeLines;
use App\Services\OccasionService;

class QuotationController extends Controller
{
    public function index(Request $request)
    {
        $branchId = Auth::user()->branch_id;
        $query = Quotation::with(['customer'])->where('branch_id', $branchId);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('quote_number', 'like', "%{$search}%")
                    ->orWhereHas('customer', function ($subQ) use ($search) {
                        $subQ->where('name', 'like', "%{$search}%")
                            ->orWhere('organization_name', 'like', "%{$search}%");
                    });
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $quotations = $query->latest()->paginate(20)->appends($request->query());

        return view('quotations.index', compact('quotations'));
    }

    public function create()
    {
        $branch = Auth::user()->branch;
        $customers = Customer::where('branch_id', $branch->id)->orderBy('name')->get();
        $catalogue = CakeLines::catalogue($branch->id);
        $occasionTypes = OccasionService::TYPES;
        $quotation = null;

        return view('quotations.create', compact('customers', 'catalogue', 'branch', 'occasionTypes', 'quotation'));
    }

    private function rules(): array
    {
        return [
            'customer_id' => 'required|exists:customers,id',
            'quote_date' => 'required|date',
            'expiry_date' => 'nullable|date|after_or_equal:quote_date',
            'subject' => 'nullable|string|max:200',
            'occasion_type' => 'nullable|string|max:40',
            'occasion_date' => 'nullable|date',
            'recipient_name' => 'nullable|string|max:120',
            'fulfilment_type' => 'nullable|in:pickup,delivery',
            'delivery_address' => 'nullable|string|max:300',
            'shipping_fee' => 'nullable|numeric|min:0',
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

    private function fields(Request $request): array
    {
        return [
            'customer_id' => $request->customer_id,
            'quote_date' => $request->quote_date,
            'expiry_date' => $request->expiry_date ?: null,
            'subject' => $request->subject,
            'occasion_type' => $request->occasion_type ?: null,
            'occasion_date' => $request->occasion_date ?: null,
            'recipient_name' => $request->recipient_name ?: null,
            'fulfilment_type' => $request->fulfilment_type ?: 'pickup',
            'delivery_address' => $request->delivery_address ?: null,
            'terms_and_conditions' => $request->terms_and_conditions,
            'notes' => $request->notes,
        ];
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), $this->rules());
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $branch = Auth::user()->branch;
        [$rows, $subtotal] = CakeLines::build($request, $branch->id);
        $shippingFee = (float) ($request->shipping_fee ?? 0);
        $taxAmount = ($subtotal * (float) ($branch->vat_percentage ?? 0)) / 100;

        DB::beginTransaction();
        try {
            $latest = Quotation::where('branch_id', $branch->id)->lockForUpdate()->latest('id')->first();
            $quoteNumber = 'QT-' . str_pad(($latest ? $latest->id + 1 : 1), 6, '0', STR_PAD_LEFT);

            $quotation = Quotation::create($this->fields($request) + [
                'branch_id' => $branch->id,
                'quote_number' => $quoteNumber,
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'shipping_fee' => $shippingFee,
                'total_amount' => $subtotal + $taxAmount + $shippingFee,
                'status' => 'sent',
                'created_by' => Auth::id(),
            ]);

            foreach ($rows as $row) {
                $quotation->items()->create($row);
            }

            DB::commit();
            return response()->json(['success' => 'Quotation created successfully!', 'redirect' => route('quotations.show', $quotation->id)]);
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            return response()->json(['message' => 'Failed to create quotation.'], 500);
        }
    }

    public function show($id)
    {
        $quotation = Quotation::with(['customer', 'items', 'invoices', 'creator'])->where('branch_id', Auth::user()->branch_id)->findOrFail($id);
        return view('quotations.show', compact('quotation'));
    }

    public function edit($id)
    {
        $branchId = Auth::user()->branch_id;
        $quotation = Quotation::with('items')->where('branch_id', $branchId)->findOrFail($id);

        // Security: Prevent editing if it's already an invoice
        if ($quotation->status === 'invoiced') {
            return redirect()->route('quotations.show', $id)->with('error', 'Cannot edit a quotation that has already been invoiced.');
        }

        $branch = Auth::user()->branch;
        $customers = Customer::where('branch_id', $branchId)->orderBy('name')->get();
        $catalogue = CakeLines::catalogue($branchId);
        $occasionTypes = OccasionService::TYPES;

        return view('quotations.edit', compact('quotation', 'customers', 'catalogue', 'branch', 'occasionTypes'));
    }

    public function update(Request $request, $id)
    {
        $branchId = Auth::user()->branch_id;
        $quotation = Quotation::where('branch_id', $branchId)->findOrFail($id);

        if ($quotation->status === 'invoiced') {
            return response()->json(['message' => 'Cannot update an invoiced quotation.'], 422);
        }

        $validator = Validator::make($request->all(), $this->rules());
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $branch = Auth::user()->branch;
        [$rows, $subtotal] = CakeLines::build($request, $branch->id);
        $shippingFee = (float) ($request->shipping_fee ?? 0);
        $taxAmount = ($subtotal * (float) ($branch->vat_percentage ?? 0)) / 100;

        DB::beginTransaction();
        try {
            $quotation->update($this->fields($request) + [
                'subtotal' => $subtotal,
                'tax_amount' => $taxAmount,
                'shipping_fee' => $shippingFee,
                'total_amount' => $subtotal + $taxAmount + $shippingFee,
            ]);

            // Clear old items and recreate (cleanest way to handle removed/added rows)
            $quotation->items()->delete();
            foreach ($rows as $row) {
                $quotation->items()->create($row);
            }

            DB::commit();
            return response()->json(['success' => 'Quotation updated successfully!', 'redirect' => route('quotations.show', $quotation->id)]);
        } catch (\Exception $e) {
            DB::rollBack();
            report($e);
            return response()->json(['message' => 'Failed to update quotation.'], 500);
        }
    }

    // ==========================================
    // THE MAGIC: CONVERT TO INVOICE
    // ==========================================
    public function convertToInvoice($id)
    {
        // Notice we are pulling products through items now
        $quotation = Quotation::with('items')->where('branch_id', Auth::user()->branch_id)->findOrFail($id);

        if ($quotation->status === 'invoiced') {
            return back()->with('error', 'This quotation has already been converted to an invoice.');
        }

        // PRE-CHECK: Verify stock availability BEFORE starting the transaction
        foreach ($quotation->items as $item) {
            if ($item->product_id) {
                $product = \App\Models\Product::find($item->product_id);
                if ($product && $product->track_stock && $item->quantity > $product->current_stock) {
                    return back()->with('error', "Cannot convert quote: Not enough stock for {$product->name}. (Available: {$product->current_stock}, Required: {$item->quantity})");
                }
            }
        }

        DB::beginTransaction();
        try {
            // 1. Generate new Invoice Number
            $latestInvoice = Invoice::where('branch_id', $quotation->branch_id)->lockForUpdate()->latest('id')->first();
            $nextInvId = $latestInvoice ? $latestInvoice->id + 1 : 1;
            $invoiceNumber = 'INV-' . str_pad($nextInvId, 5, '0', STR_PAD_LEFT);

            // 2. Create the Invoice based on Quotation data
            $invoice = Invoice::create([
                'branch_id' => $quotation->branch_id,
                'customer_id' => $quotation->customer_id,
                'quotation_id' => $quotation->id, // The essential link!
                'invoice_number' => $invoiceNumber,
                'status' => 'processing',
                'subtotal' => $quotation->subtotal,
                'tax_amount' => $quotation->tax_amount,
                'shipping_fee' => $quotation->shipping_fee,
                'total_amount' => $quotation->total_amount,
                'amount_paid' => 0,

                // Aligning with standard invoice fields
                'due_date' => $quotation->expiry_date ?? now()->addDays(7)->format('Y-m-d'),
                'occasion_type' => $quotation->occasion_type,
                'occasion_date' => $quotation->occasion_date,
                'recipient_name' => $quotation->recipient_name,
                'fulfilment_type' => $quotation->fulfilment_type ?: 'pickup',
                'delivery_address' => $quotation->delivery_address,
                'source' => 'admin',
                'notes' => $quotation->notes,
            ]);

            // 3. Duplicate Items and Handle Stock Deductions
            foreach ($quotation->items as $qItem) {

                // Create Invoice Item
                $invoice->items()->create([
                    'product_id' => $qItem->product_id, // Retain the product_id link
                    'item_name' => $qItem->item_name,
                    'quantity' => $qItem->quantity,
                    'cake_size_id' => $qItem->cake_size_id,
                    'size_label' => $qItem->size_label,
                    'flavour_name' => $qItem->flavour_name,
                    'cake_message' => $qItem->cake_message,
                    'unit_price' => $qItem->unit_price,
                    'total' => $qItem->total,
                ]);

                // Update Stock and Log Movement
                if ($qItem->product_id) {
                    $product = \App\Models\Product::find($qItem->product_id);

                    if ($product && $product->track_stock) {
                        $oldStock = $product->current_stock;
                        $newStock = $oldStock - $qItem->quantity;

                        $product->update(['current_stock' => $newStock]);

                        \App\Models\StockMovement::create([
                            'branch_id' => $quotation->branch_id,
                            'product_id' => $product->id,
                            'user_id' => Auth::id(),
                            'type' => 'sale',
                            'quantity' => $qItem->quantity,
                            'balance_after' => $newStock,
                            'reference' => 'Converted from Quote ' . $quotation->quote_number . ' to Invoice ' . $invoiceNumber,
                        ]);
                    }
                }
            }

            // 4. Update Quotation Status
            $quotation->update(['status' => 'invoiced']);

            DB::commit();

            app(OccasionService::class)->syncFromInvoice($invoice->load('items'));

            // 5. Fire the exact same event as standard invoice creation
            event(new \App\Events\InvoiceCreated($invoice));

            return redirect()->route('invoices.show', $invoice->id)->with('success', 'Quotation successfully converted to Invoice!');
        } catch (\Exception $e) {
            DB::rollBack();
            return back()->with('error', 'An error occurred while converting to invoice. ' . $e->getMessage());
        }
    }

    public function downloadPdf($id)
    {
        $branchId = Auth::user()->branch_id;
        $quotation = Quotation::with(['customer', 'items', 'creator'])
            ->where('branch_id', $branchId)
            ->findOrFail($id);

        $branch = Auth::user()->branch; // Pass the branch to get company logo/details

        // Load the view and generate the PDF
        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('quotations.pdf', compact('quotation', 'branch'));

        // Stream outputs it in the browser so they can view or download it
        return $pdf->stream('Quotation_' . $quotation->quote_number . '.pdf');
    }

    public function destroy($id)
    {
        $quotation = Quotation::where('branch_id', Auth::user()->branch_id)->findOrFail($id);

        if ($quotation->status === 'invoiced') {
            return response()->json(['message' => 'Cannot delete a quotation that has already been invoiced.'], 422);
        }

        $quotation->delete();
        return response()->json(['success' => 'Quotation deleted successfully.']);
    }
}
