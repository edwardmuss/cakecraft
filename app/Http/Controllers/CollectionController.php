<?php

namespace App\Http\Controllers;

use App\Models\Collection;
use App\Services\OccasionService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class CollectionController extends Controller
{
    public function index()
    {
        abort_if(!Auth::user()->can('view products'), 403);
        $collections = Collection::withCount('products')->where('branch_id', Auth::user()->branch_id)
            ->orderBy('sort_order')->orderBy('name')->get();
        $occasionTypes = OccasionService::TYPES;
        return view('collections.index', compact('collections', 'occasionTypes'));
    }

    public function store(Request $request)
    {
        abort_if(!Auth::user()->can('create products'), 403);
        $data = $this->validated($request);
        $data['branch_id'] = Auth::user()->branch_id;
        $data['slug'] = $this->uniqueSlug($data['name'], $data['branch_id']);
        if ($request->hasFile('image')) {
            $data['image'] = $request->file('image')->store('collections', 'public');
        }
        Collection::create($data);
        return back()->with('success', 'Collection created.');
    }

    public function update(Request $request, $id)
    {
        abort_if(!Auth::user()->can('update products'), 403);
        $collection = Collection::where('branch_id', Auth::user()->branch_id)->findOrFail($id);
        $data = $this->validated($request);
        if ($request->hasFile('image')) {
            if ($collection->image) Storage::disk('public')->delete($collection->image);
            $data['image'] = $request->file('image')->store('collections', 'public');
        }
        $collection->update($data);
        return back()->with('success', 'Collection updated.');
    }

    public function reorder(Request $request)
    {
        abort_if(!Auth::user()->can('update products'), 403);
        $ids = $request->validate(['ids' => 'required|array', 'ids.*' => 'integer'])['ids'];
        foreach (array_values($ids) as $position => $id) {
            Collection::where('branch_id', Auth::user()->branch_id)->where('id', $id)->update(['sort_order' => $position]);
        }
        return response()->json(['ok' => true]);
    }

    public function destroy($id)
    {
        abort_if(!Auth::user()->can('delete products'), 403);
        $collection = Collection::where('branch_id', Auth::user()->branch_id)->findOrFail($id);
        if ($collection->image) Storage::disk('public')->delete($collection->image);
        $collection->delete();
        return back()->with('success', 'Collection deleted.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'name' => 'required|string|max:120',
            'description' => 'nullable|string|max:1000',
            'occasion_type' => 'nullable|string|max:40',
            'sort_order' => 'nullable|integer|min:0',
            'image' => 'nullable|image|max:4096',
        ]);
        $data['is_recurring'] = $request->boolean('is_recurring');
        $data['is_active'] = $request->boolean('is_active');
        $data['sort_order'] = $data['sort_order'] ?? 0;
        unset($data['image']);
        return $data;
    }

    private function uniqueSlug(string $name, int $branchId): string
    {
        $base = Str::slug($name) ?: 'collection';
        $slug = $base; $i = 2;
        while (Collection::where('branch_id', $branchId)->where('slug', $slug)->exists()) {
            $slug = $base . '-' . $i++;
        }
        return $slug;
    }
}
