<?php

namespace App\Http\Controllers;

use App\Models\CakeFlavour;
use App\Models\CakeSize;
use App\Models\Collection;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CakeController extends Controller
{
    public function index(Request $request)
    {
        abort_if(!Auth::user()->can('view products'), 403);
        $branchId = Auth::user()->branch_id;

        $query = Product::with(['collections', 'sizePrices'])->where('branch_id', $branchId);

        if ($request->filled('q')) {
            $query->where('name', 'like', '%' . $request->q . '%');
        }
        if ($request->filled('collection')) {
            $query->whereHas('collections', fn ($q) => $q->where('collections.id', $request->collection));
        }
        if ($request->filled('type')) {
            $query->where('product_type', $request->type);
        }
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $cakes = $query->latest()->paginate(24)->withQueryString();
        $collections = Collection::where('branch_id', $branchId)->orderBy('name')->get();
        $types = Product::TYPES;

        return view('cakes.index', compact('cakes', 'collections', 'types', 'request'));
    }

    public function create()
    {
        abort_if(!Auth::user()->can('create products'), 403);
        return view('cakes.form', $this->formData(new Product(['is_active' => true, 'is_published' => true, 'product_type' => 'custom', 'lead_time_days' => 2])));
    }

    public function store(Request $request)
    {
        abort_if(!Auth::user()->can('create products'), 403);
        $cake = new Product(['branch_id' => Auth::user()->branch_id]);
        $this->persist($request, $cake);
        return redirect()->route('cakes.index')->with('success', 'Cake created.');
    }

    public function edit($id)
    {
        abort_if(!Auth::user()->can('update products'), 403);
        $cake = Product::with(['collections', 'sizePrices', 'flavours'])->where('branch_id', Auth::user()->branch_id)->findOrFail($id);
        return view('cakes.form', $this->formData($cake));
    }

    public function update(Request $request, $id)
    {
        abort_if(!Auth::user()->can('update products'), 403);
        $cake = Product::where('branch_id', Auth::user()->branch_id)->findOrFail($id);
        $this->persist($request, $cake);
        return redirect()->route('cakes.index')->with('success', 'Cake updated.');
    }

    public function destroy($id)
    {
        abort_if(!Auth::user()->can('delete products'), 403);
        $cake = Product::where('branch_id', Auth::user()->branch_id)->findOrFail($id);
        if ($cake->image) Storage::disk('public')->delete($cake->image);
        $cake->delete();
        return redirect()->route('cakes.index')->with('success', 'Cake deleted.');
    }

    public function toggle($id)
    {
        abort_if(!Auth::user()->can('update products'), 403);
        $cake = Product::where('branch_id', Auth::user()->branch_id)->findOrFail($id);
        $cake->update(['is_published' => !$cake->is_published]);
        return back()->with('success', $cake->is_published ? 'Cake is now visible on the website.' : 'Cake hidden from the website.');
    }

    private function formData(Product $cake): array
    {
        $branchId = Auth::user()->branch_id;
        return [
            'cake' => $cake,
            'collections' => Collection::where('branch_id', $branchId)->orderBy('name')->get(),
            'flavours' => CakeFlavour::where('branch_id', $branchId)->where('is_active', true)->orderBy('sort_order')->orderBy('name')->get(),
            'sizes' => CakeSize::where('branch_id', $branchId)->where('is_active', true)->orderBy('sort_order')->orderBy('weight_kg')->get(),
            'types' => Product::TYPES,
        ];
    }

    private function persist(Request $request, Product $cake): void
    {
        $data = $request->validate([
            'name' => 'required|string|max:160',
            'short_description' => 'nullable|string|max:200',
            'description' => 'nullable|string',
            'product_type' => 'required|string|in:' . implode(',', array_keys(Product::TYPES)),
            'lead_time_days' => 'nullable|integer|min:0|max:60',
            'image' => 'nullable|image|max:6144',
            'collection_ids' => 'nullable|array',
            'collection_ids.*' => 'integer',
            'prices' => 'nullable|array',            // [cake_size_id => price]
            'prices.*' => 'nullable|numeric|min:0',
            'flavour_ids' => 'nullable|array',
            'flavour_ids.*' => 'integer',
            'flavour_extra' => 'nullable|array',     // [cake_flavour_id => extra]
            'flavour_extra.*' => 'nullable|numeric|min:0',
            'fixed_price' => 'nullable|numeric|min:0',
        ]);

        $branchId = Auth::user()->branch_id;

        DB::transaction(function () use ($request, $cake, $data, $branchId) {
            $prices = collect($data['prices'] ?? [])->filter(fn ($p) => $p !== null && $p !== '');
            $fill = [
                'name' => $data['name'],
                'short_description' => $data['short_description'] ?? null,
                'description' => $data['description'] ?? null,
                'product_type' => $data['product_type'],
                'lead_time_days' => $data['lead_time_days'] ?? 2,
                'is_active' => $request->boolean('is_active'),
                'is_published' => $request->boolean('is_published'),
                'is_featured' => $request->boolean('is_featured'),
                'unit_price' => $prices->min() ?? ($data['fixed_price'] ?? 0),
            ];

            if ($request->hasFile('image')) {
                if ($cake->image) Storage::disk('public')->delete($cake->image);
                $fill['image'] = $request->file('image')->store('cakes', 'public');
            }

            $cake->fill($fill)->save();

            $cake->collections()->sync(
                Collection::where('branch_id', $branchId)->whereIn('id', $data['collection_ids'] ?? [])->pluck('id')
            );

            // Size price grid
            $validSizes = CakeSize::where('branch_id', $branchId)->pluck('id')->all();
            $cake->sizePrices()->delete();
            foreach ($prices as $sizeId => $price) {
                if (in_array((int) $sizeId, $validSizes, true)) {
                    $cake->sizePrices()->create(['cake_size_id' => $sizeId, 'price' => $price, 'is_active' => true]);
                }
            }

            // Flavours with optional surcharge
            $validFlavours = CakeFlavour::where('branch_id', $branchId)->pluck('id')->all();
            $sync = [];
            foreach ($data['flavour_ids'] ?? [] as $fid) {
                if (in_array((int) $fid, $validFlavours, true)) {
                    $sync[$fid] = ['extra_price' => $data['flavour_extra'][$fid] ?? 0];
                }
            }
            $cake->flavours()->sync($sync);
        });
    }
}
