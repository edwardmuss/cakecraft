<?php

namespace App\Http\Controllers;

use App\Models\Product;
use App\Models\StockMovement;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index()
    {
        $products = Product::where('branch_id', Auth::user()->branch_id)->latest()->get();
        return view('products.index', compact('products'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'unit_price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $product = Product::create([
            'branch_id' => Auth::user()->branch_id,
            'name' => $request->name,
            'description' => $request->description,
            'unit_price' => $request->unit_price,
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        return response()->json(['success' => 'Product added successfully!', 'product' => $product]);
    }

    public function edit($id)
    {
        $product = Product::where('branch_id', Auth::user()->branch_id)->findOrFail($id);
        return response()->json($product);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'unit_price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $product = Product::where('branch_id', Auth::user()->branch_id)->findOrFail($id);

        $product->update([
            'name' => $request->name,
            'description' => $request->description,
            'unit_price' => $request->unit_price,
            'is_active' => $request->has('is_active') ? true : false,
        ]);

        return response()->json(['success' => 'Product updated successfully!', 'product' => $product]);
    }

    public function destroy($id)
    {
        $product = Product::where('branch_id', Auth::user()->branch_id)->findOrFail($id);

        // Optional: Check if product is attached to invoices before deleting to prevent breaking history,
        // or rely on the 'set null' constraint we defined in the migration.
        // Check if the product is attached to any historical invoices
        if ($product->invoiceItems()->exists()) {
            return response()->json([
                'errors' => [
                    'general' => ['Cannot delete this product because it is attached to existing invoices. Please toggle it to "Inactive" instead to hide it from future sales.']
                ]
            ], 422);
        }

        $product->delete();

        return response()->json(['success' => 'Product deleted successfully!']);
    }

    public function adjustStock(Request $request, $id)
    {
        $request->validate([
            'quantity' => 'required|integer',
            'type' => 'required|in:addition,deduction',
            'reference' => 'nullable|string|max:255'
        ]);

        $product = Product::where('branch_id', Auth::user()->branch_id)->findOrFail($id);

        if (!$product->track_stock) {
            return response()->json(['errors' => ['general' => ['This product does not track stock.']]], 422);
        }

        $qty = $request->quantity;
        $newStock = $request->type === 'addition'
            ? $product->current_stock + $qty
            : $product->current_stock - $qty;

        $product->update(['current_stock' => $newStock]);

        StockMovement::create([
            'branch_id' => Auth::user()->branch_id,
            'product_id' => $product->id,
            'user_id' => Auth::id(),
            'type' => $request->type,
            'quantity' => $qty,
            'balance_after' => $newStock,
            'reference' => $request->reference ?? 'Manual Adjustment',
        ]);

        return response()->json(['success' => 'Stock updated successfully!', 'new_stock' => $newStock]);
    }

    public function export()
    {
        $branchId = Auth::user()->branch_id;
        $timestamp = now()->format('Y_m_d_His');
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\ProductsExport($branchId), "products_{$timestamp}.xlsx");
    }
}
