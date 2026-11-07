<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class BranchController extends Controller
{
    public function index()
    {
        $branches = Branch::latest()->get();
        return view('branches.index', compact('branches'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'branch_name' => 'required|string|max:255',
            'branch_email' => 'required|email|unique:branches,branch_email',
            'company_phone' => 'required|string|unique:branches,company_phone',
            'kra_pin_number' => 'nullable|string|unique:branches,kra_pin_number',
            'vat_percentage' => 'nullable|numeric|min:0|max:100',
            'about' => 'nullable|string',
            'website' => 'nullable|string',
            'isActive' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $branch = Branch::create($request->all());
        return response()->json(['success' => 'Branch created successfully!']);
    }

    public function edit($id)
    {
        return response()->json(Branch::findOrFail($id));
    }

    public function update(Request $request, $id)
    {
        $branch = Branch::findOrFail($id);

        $validator = Validator::make($request->all(), [
            'branch_name' => 'required|string|max:255',
            'branch_email' => 'required|email|unique:branches,branch_email,' . $branch->id,
            'company_phone' => 'required|string|unique:branches,company_phone,' . $branch->id,
            'kra_pin_number' => 'nullable|string|unique:branches,kra_pin_number,' . $branch->id,
            'vat_percentage' => 'nullable|numeric|min:0|max:100',
            'about' => 'nullable|string',
            'website' => 'nullable|string',
            'isActive' => 'boolean',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        // Handle checkbox since unchecked won't send a value
        $data = $request->all();
        $data['isActive'] = $request->has('isActive') ? 1 : 0;

        $branch->update($data);
        return response()->json(['success' => 'Branch updated successfully!']);
    }

    public function destroy($id)
    {
        $branch = Branch::findOrFail($id);

        // Guard against deleting a branch with invoices/users
        if ($branch->invoices()->exists() || $branch->users()->exists()) {
            return response()->json(['errors' => ['general' => ['Cannot delete branch. It has active users or invoices. Lock or disable it instead.']]], 422);
        }

        $branch->delete();
        return response()->json(['success' => 'Branch deleted successfully!']);
    }
}
