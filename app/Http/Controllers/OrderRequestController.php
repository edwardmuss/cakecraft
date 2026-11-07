<?php

namespace App\Http\Controllers;

use App\Models\Customer;
use App\Models\Invoice;
use App\Models\OrderRequest;
use App\Models\Product;
use App\Services\OccasionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class OrderRequestController extends Controller
{
    public function index(Request $request)
    {
        abort_if(!Auth::user()->can('view invoices'), 403);
        $branchId = Auth::user()->branch_id;

        $query = OrderRequest::with('product')->where('branch_id', $branchId);
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }
        if ($request->filled('q')) {
            $q = $request->q;
            $query->where(fn ($w) => $w->where('name', 'like', "%$q%")->orWhere('phone', 'like', "%$q%")->orWhere('reference', 'like', "%$q%"));
        }

        $requests = $query->latest()->paginate(30)->withQueryString();
        $counts = OrderRequest::where('branch_id', $branchId)->selectRaw('status, count(*) c')->groupBy('status')->pluck('c', 'status');

        return view('order_requests.index', compact('requests', 'counts', 'request'));
    }

    public function updateStatus(Request $request, $id)
    {
        abort_if(!Auth::user()->can('update invoices'), 403);
        $request->validate(['status' => 'required|in:new,contacted,declined']);
        OrderRequest::where('branch_id', Auth::user()->branch_id)->findOrFail($id)->update(['status' => $request->status]);
        return back()->with('success', 'Request updated.');
    }

    /** Turn a storefront request into a real invoice. */
    public function convert($id, OccasionService $occasions)
    {
        abort_if(!Auth::user()->can('create invoices'), 403);
        $branch = Auth::user()->branch;
        $req = OrderRequest::where('branch_id', $branch->id)->findOrFail($id);

        if ($req->invoice_id) {
            return redirect()->route('invoices.show', $req->invoice_id);
        }

        $invoice = DB::transaction(function () use ($req, $branch, $occasions) {
            $customer = Customer::firstOrCreate(
                ['branch_id' => $branch->id, 'phone' => $req->phone],
                ['name' => $req->name, 'email' => $req->email]
            );

            $unit = $req->quantity > 0 ? $req->estimated_price / $req->quantity : $req->estimated_price;
            $subtotal = (float) $req->estimated_price;
            $tax = $subtotal * ((float) ($branch->vat_percentage ?? 0)) / 100;

            $next = (Invoice::where('branch_id', $branch->id)->max('id') ?? 0) + 1;

            $invoice = Invoice::create([
                'branch_id' => $branch->id,
                'customer_id' => $customer->id,
                'invoice_number' => 'INV-' . str_pad($next, 5, '0', STR_PAD_LEFT),
                'status' => 'processing',
                'subtotal' => $subtotal,
                'tax_amount' => $tax,
                'shipping_fee' => (float) $req->delivery_fee,
                'total_amount' => $subtotal + $tax + (float) $req->delivery_fee,
                'amount_paid' => 0,
                'due_date' => $req->needed_on,
                'occasion_type' => $req->occasion_type,
                'occasion_date' => $req->needed_on,
                'recipient_name' => $req->recipient_name,
                'fulfilment_type' => $req->fulfilment_type,
                'delivery_address' => $req->delivery_address,
                'source' => 'storefront',
                'notes' => trim("Web request {$req->reference}. " . ($req->notes ?? '')),
            ]);

            $invoice->items()->create([
                'product_id' => $req->product_id,
                'cake_size_id' => $req->cake_size_id,
                'item_name' => $req->cake_name,
                'size_label' => $req->size_label,
                'flavour_name' => $req->flavour_name,
                'cake_message' => $req->cake_message,
                'quantity' => $req->quantity,
                'unit_price' => $unit,
                'total' => $subtotal,
            ]);

            $req->update(['status' => 'converted', 'customer_id' => $customer->id, 'invoice_id' => $invoice->id]);
            $occasions->syncFromInvoice($invoice);

            return $invoice;
        });

        return redirect()->route('invoices.show', $invoice->id)->with('success', 'Request converted to an invoice. Review the amount and confirm with the customer.');
    }
}
