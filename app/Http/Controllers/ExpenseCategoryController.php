<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseCategoryController extends Controller
{
    public function index()
    {
        $categories = ExpenseCategory::query()->where('branch_id', Auth::user()->branch_id)->orderBy('name')->paginate(25);
        return view('expenses.categories', compact('categories'));
    }

    public function store(Request $request)
    {
        $request->validate(['name' => 'required|string|max:255']);

        ExpenseCategory::create([
            'branch_id' => Auth::user()->branch_id,
            'name' => $request->name,
        ]);

        return response()->json(['success' => 'Category added successfully!']);
    }

    public function edit($id)
    {
        $category = ExpenseCategory::query()->where('branch_id', Auth::user()->branch_id)->findOrFail($id);
        return response()->json($category);
    }

    public function update(Request $request, $id)
    {
        $request->validate(['name' => 'required|string|max:255']);
        $category = ExpenseCategory::query()->where('branch_id', Auth::user()->branch_id)->findOrFail($id);
        $category->update(['name' => $request->name]);

        return response()->json(['success' => 'Category updated successfully!']);
    }

    public function destroy($id)
    {
        $category = ExpenseCategory::query()->where('branch_id', Auth::user()->branch_id)->findOrFail($id);

        // Prevent deletion if expenses are attached (Restricted by DB, but good to catch gracefully)
        if (Expense::query()->where('expense_category_id', $id)->exists()) {
            return response()->json(['message' => 'Cannot delete category. It is linked to existing expenses.'], 422);
        }

        $category->delete();
        return response()->json(['success' => 'Category deleted!']);
    }

    public function export()
    {
        return \Maatwebsite\Excel\Facades\Excel::download(new \App\Exports\ExpenseCategoriesExport, 'Expense_Categories.xlsx');
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,csv']);
        \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\ExpenseCategoriesImport, $request->file('file'));
        return response()->json(['success' => 'Categories imported successfully!']);
    }

    public function template()
    {
        $headers = ['Name'];
        $callback = function () use ($headers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            fputcsv($file, ['Transport & Logistics']);
            fclose($file);
        };
        return response()->stream($callback, 200, ["Content-Type" => "text/csv", "Content-Disposition" => "attachment; filename=Categories_Template.csv"]);
    }
}
