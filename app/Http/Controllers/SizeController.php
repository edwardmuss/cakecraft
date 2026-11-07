<?php

namespace App\Http\Controllers;

use App\Models\CakeSize;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SizeController extends Controller
{
    public function index()
    {
        abort_if(!Auth::user()->can('view products'), 403);
        $sizes = CakeSize::where('branch_id', Auth::user()->branch_id)->orderBy('sort_order')->orderBy('weight_kg')->get();
        return view('sizes.index', compact('sizes'));
    }

    public function store(Request $request)
    {
        abort_if(!Auth::user()->can('create products'), 403);
        CakeSize::create($this->validated($request) + ['branch_id' => Auth::user()->branch_id]);
        return back()->with('success', 'Size added.');
    }

    public function update(Request $request, $id)
    {
        abort_if(!Auth::user()->can('update products'), 403);
        CakeSize::where('branch_id', Auth::user()->branch_id)->findOrFail($id)->update($this->validated($request));
        return back()->with('success', 'Size updated.');
    }

    public function reorder(Request $request)
    {
        abort_if(!Auth::user()->can('update products'), 403);
        $ids = $request->validate(['ids' => 'required|array', 'ids.*' => 'integer'])['ids'];
        foreach (array_values($ids) as $position => $id) {
            CakeSize::where('branch_id', Auth::user()->branch_id)->where('id', $id)->update(['sort_order' => $position]);
        }
        return response()->json(['ok' => true]);
    }

    public function destroy($id)
    {
        abort_if(!Auth::user()->can('delete products'), 403);
        CakeSize::where('branch_id', Auth::user()->branch_id)->findOrFail($id)->delete();
        return back()->with('success', 'Size deleted.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'label' => 'required|string|max:60',
            'weight_kg' => 'nullable|numeric|min:0',
            'serves' => 'nullable|string|max:40',
            'sort_order' => 'nullable|integer|min:0',
        ]);
        $data['sort_order'] = $data['sort_order'] ?? 0;
        $data['is_active'] = $request->boolean('is_active');
        return $data;
    }
}
