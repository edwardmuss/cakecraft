<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupplierController extends Controller
{
    public function index(Request $request)
    {
        $query = Supplier::query()->where('branch_id', Auth::user()->branch_id);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%");
            });
        }

        $suppliers = $query->orderBy('name')->paginate(25)->appends($request->query());
        return view('expenses.suppliers', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        Supplier::create(array_merge($request->all(), ['branch_id' => Auth::user()->branch_id]));
        return response()->json(['success' => 'Supplier added successfully!']);
    }

    public function edit($id)
    {
        $supplier = Supplier::query()->where('branch_id', Auth::user()->branch_id)->findOrFail($id);
        return response()->json($supplier);
    }

    public function update(Request $request, $id)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $supplier = Supplier::query()->where('branch_id', Auth::user()->branch_id)->findOrFail($id);
        $supplier->update($request->all());

        return response()->json(['success' => 'Supplier updated successfully!']);
    }

    public function destroy($id)
    {
        $supplier = Supplier::query()->where('branch_id', Auth::user()->branch_id)->findOrFail($id);
        $supplier->delete(); // Expenses will be set to NULL for supplier_id due to migration
        return response()->json(['success' => 'Supplier deleted!']);
    }

    public function export()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\SuppliersExport, 'Suppliers.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,csv']);
        \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\SuppliersImport, $request->file('file'));
        return response()->json(['success' => 'Suppliers imported successfully!']);
    }

    public function template()
    {
        $headers = ['Name', 'Phone', 'Email', 'KRA PIN', 'Address'];
        $callback = function () use ($headers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            fputcsv($file, ['Vendor XYZ', '0712345678', 'vendor@xyz.com', 'A123456789Z', 'Nairobi']);
            fclose($file);
        };
        return response()->stream($callback, 200, ["Content-Type" => "text/csv", "Content-Disposition" => "attachment; filename=Suppliers_Template.csv"]);
    }
}
