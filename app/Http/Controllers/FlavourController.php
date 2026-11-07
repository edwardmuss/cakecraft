<?php

namespace App\Http\Controllers;

use App\Models\CakeFlavour;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FlavourController extends Controller
{
    public function index()
    {
        abort_if(!Auth::user()->can('view products'), 403);
        $flavours = CakeFlavour::where('branch_id', Auth::user()->branch_id)->orderBy('sort_order')->orderBy('name')->get();
        return view('flavours.index', compact('flavours'));
    }

    public function store(Request $request)
    {
        abort_if(!Auth::user()->can('create products'), 403);
        CakeFlavour::create($this->validated($request) + ['branch_id' => Auth::user()->branch_id]);
        return back()->with('success', 'Flavour added.');
    }

    public function update(Request $request, $id)
    {
        abort_if(!Auth::user()->can('update products'), 403);
        CakeFlavour::where('branch_id', Auth::user()->branch_id)->findOrFail($id)->update($this->validated($request));
        return back()->with('success', 'Flavour updated.');
    }

    public function reorder(Request $request)
    {
        abort_if(!Auth::user()->can('update products'), 403);
        $ids = $request->validate(['ids' => 'required|array', 'ids.*' => 'integer'])['ids'];
        foreach (array_values($ids) as $position => $id) {
            CakeFlavour::where('branch_id', Auth::user()->branch_id)->where('id', $id)->update(['sort_order' => $position]);
        }
        return response()->json(['ok' => true]);
    }

    public function destroy($id)
    {
        abort_if(!Auth::user()->can('delete products'), 403);
        CakeFlavour::where('branch_id', Auth::user()->branch_id)->findOrFail($id)->delete();
        return back()->with('success', 'Flavour deleted.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'description' => 'nullable|string|max:500',
            'sort_order' => 'nullable|integer|min:0',
        ]);
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['is_active'] = $request->boolean('is_active');
        return $data;
    }
}
