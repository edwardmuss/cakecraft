<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class ProductController extends Controller
{
    public function index(Request $request)
    {
        $query = Product::where('branch_id', $request->user()->branch_id);

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%");
            });
        }

        $products = $query->latest()->paginate(50);

        return response()->json($products);
    }

    public function store(Request $request)
    {
        if (!$request->user()->can('create products')) {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }

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
            'branch_id' => $request->user()->branch_id,
            'name' => $request->name,
            'description' => $request->description,
            'unit_price' => $request->unit_price,
            'is_active' => $request->boolean('is_active', true), // Default to true if not provided
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Product added successfully!',
            'data' => $product
        ]);
    }

    public function update(Request $request, $id)
    {
        if (!$request->user()->can('update products')) {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'unit_price' => 'required|numeric|min:0',
            'is_active' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $product = Product::where('branch_id', $request->user()->branch_id)->findOrFail($id);

        $product->update([
            'name' => $request->name,
            'description' => $request->description,
            'unit_price' => $request->unit_price,
            'is_active' => $request->boolean('is_active'),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Product updated successfully!',
            'data' => $product
        ]);
    }

    public function destroy(Request $request, $id)
    {
        if (!$request->user()->can('delete products')) {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }

        $product = Product::where('branch_id', $request->user()->branch_id)->findOrFail($id);

        if ($product->invoiceItems()->exists()) {
            return response()->json([
                'errors' => [
                    'general' => ['Cannot delete this product because it is attached to existing invoices. Please toggle it to "Inactive" instead.']
                ]
            ], 422);
        }

        $product->delete();

        return response()->json([
            'success' => true,
            'message' => 'Product deleted successfully!'
        ]);
    }
}
