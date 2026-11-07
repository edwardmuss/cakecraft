<?php

namespace App\Http\Controllers;

use App\Models\Expense;
use App\Models\Supplier;
use App\Models\ExpenseCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ExpenseController extends Controller
{
    public function index(Request $request)
    {
        $branchId = Auth::user()->branch_id;
        $query = Expense::with(['supplier', 'category', 'creator'])->where('branch_id', $branchId);

        // Filter by Supplier
        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        // Filter by Category
        if ($request->filled('expense_category_id')) {
            $query->where('expense_category_id', $request->expense_category_id);
        }

        // Filter by Date Range
        if ($request->filled('start_date') && $request->filled('end_date')) {
            $query->whereBetween('expense_date', [$request->start_date, $request->end_date]);
        }

        // Text Search
        if ($request->filled('search')) {
            $query->where('reference_number', 'like', "%{$request->search}%");
        }

        $expenses = $query->orderBy('expense_date', 'desc')->paginate(100)->appends($request->query());

        $suppliers = Supplier::where('branch_id', $branchId)->orderBy('name')->get();
        $categories = ExpenseCategory::where('branch_id', $branchId)->orderBy('name')->get();

        return view('expenses.index', compact('expenses', 'suppliers', 'categories', 'request'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'expense_date' => 'required|date',
            'amount_before_tax' => 'required|numeric|min:0',
            'tax_amount' => 'required|numeric|min:0',
            'payment_method' => 'required|string',
        ]);

        $subtotal = floatval($request->amount_before_tax);
        $tax = floatval($request->tax_amount);

        Expense::create([
            'branch_id' => Auth::user()->branch_id,
            'supplier_id' => $request->supplier_id,
            'expense_category_id' => $request->expense_category_id,
            'expense_date' => $request->expense_date,
            'reference_number' => $request->reference_number,
            'amount_before_tax' => $subtotal,
            'tax_amount' => $tax,
            'total_amount' => $subtotal + $tax,
            'payment_method' => $request->payment_method,
            'notes' => $request->notes,
            'created_by' => Auth::user()->id,
        ]);

        return response()->json(['success' => 'Expense recorded successfully!']);
    }

    public function edit($id)
    {
        $expense = Expense::where('branch_id', Auth::user()->branch_id)->findOrFail($id);
        return response()->json($expense);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'expense_category_id' => 'required|exists:expense_categories,id',
            'expense_date' => 'required|date',
            'amount_before_tax' => 'required|numeric|min:0',
            'tax_amount' => 'required|numeric|min:0',
        ]);

        $expense = Expense::where('branch_id', Auth::user()->branch_id)->findOrFail($id);
        $subtotal = floatval($request->amount_before_tax);
        $tax = floatval($request->tax_amount);

        $expense->update([
            'supplier_id' => $request->supplier_id,
            'expense_category_id' => $request->expense_category_id,
            'expense_date' => $request->expense_date,
            'reference_number' => $request->reference_number,
            'amount_before_tax' => $subtotal,
            'tax_amount' => $tax,
            'total_amount' => $subtotal + $tax,
            'payment_method' => $request->payment_method,
            'notes' => $request->notes,
        ]);

        return response()->json(['success' => 'Expense updated successfully!']);
    }

    public function destroy($id)
    {
        $expense = Expense::where('branch_id', Auth::user()->branch_id)->findOrFail($id);
        $expense->delete();
        return response()->json(['success' => 'Expense deleted successfully!']);
    }

    public function export(Request $request)
    {
        $branchId = Auth::user()->branch_id;

        // Default to last 30 days if no dates are provided
        $startDate = $request->start_date ?? now()->subDays(30)->format('Y-m-d');
        $endDate = $request->end_date ?? now()->format('Y-m-d');

        $timestamp = now()->format('Y_m_d_His');
        $fileName = "Expenses_Report_{$timestamp}.xlsx";

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\ExpensesExport($branchId, $startDate, $endDate, $request->supplier_id, $request->expense_category_id),
            $fileName
        );
    }

    public function import(Request $request)
    {
        $request->validate(['file' => 'required|mimes:xlsx,csv']);
        \Maatwebsite\Excel\Facades\Excel::import(new \App\Imports\ExpensesImport, $request->file('file'));
        return response()->json(['success' => 'Expenses imported successfully! New categories and suppliers were created where needed.']);
    }

    public function template()
    {
        $headers = ['Date', 'Category', 'Supplier', 'Ref Number', 'Before Tax', 'Tax VAT', 'Payment Method', 'Notes'];
        $callback = function () use ($headers) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $headers);
            fputcsv($file, [date('Y-m-d'), 'Office Supplies', 'Textbook Centre', 'REC-123', '1000', '160', 'Cash', 'Bought printer paper']);
            fclose($file);
        };
        return response()->stream($callback, 200, [
            "Content-Type" => "text/csv",
            "Content-Disposition" => "attachment; filename=Expenses_Import_Template.csv",
        ]);
    }
}
