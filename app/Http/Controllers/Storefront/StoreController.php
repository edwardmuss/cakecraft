<?php

namespace App\Http\Controllers\Storefront;

use App\Http\Controllers\Controller;
use App\Models\Branch;
use App\Models\CakeSize;
use App\Models\Collection;
use App\Models\OrderRequest;
use App\Models\Product;
use App\Services\OccasionService;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class StoreController extends Controller
{
    private function branchId(): int
    {
        return (int) (config('cakecraft.branch_id') ?: Branch::query()->orderBy('id')->value('id'));
    }

    private function collections()
    {
        return Collection::where('branch_id', $this->branchId())->where('is_active', true)
            ->orderBy('sort_order')->orderBy('name')->get();
    }

    public function home()
    {
        $b = $this->branchId();
        $collections = $this->collections()->loadCount(['products' => fn ($q) => $q->storefront()]);
        $featured = Product::storefront()->with('sizePrices')->where('branch_id', $b)->where('is_featured', true)->latest()->take(8)->get();
        $latest = Product::storefront()->with('sizePrices')->where('branch_id', $b)->latest()->take(12)->get();
        $signature = Product::storefront()->with('sizePrices')->where('branch_id', $b)->where('product_type', 'signature')->take(9)->get();

        return view('storefront.home', compact('collections', 'featured', 'latest', 'signature'));
    }

    public function collection(Request $request, string $slug)
    {
        $b = $this->branchId();
        $collection = Collection::where('branch_id', $b)->where('slug', $slug)->where('is_active', true)->firstOrFail();
        $cakes = $collection->products()->storefront()->with('sizePrices')->latest()->paginate(18);
        return view('storefront.collection', ['collection' => $collection, 'cakes' => $cakes, 'collections' => $this->collections()]);
    }

    public function shop(Request $request)
    {
        $b = $this->branchId();
        $q = Product::storefront()->with('sizePrices')->where('branch_id', $b);
        if ($request->filled('q')) $q->where('name', 'like', '%' . $request->q . '%');
        if ($request->filled('type')) $q->where('product_type', $request->type);
        $cakes = $q->latest()->paginate(18)->withQueryString();
        return view('storefront.shop', ['cakes' => $cakes, 'collections' => $this->collections(), 'request' => $request]);
    }

    /** Full price book: every signature flavour with a price per size. */
    public function menu()
    {
        $b = $this->branchId();
        $sizes = CakeSize::where('branch_id', $b)->where('is_active', true)->orderBy('sort_order')->orderBy('weight_kg')->get();
        $cakes = Product::storefront()->with('sizePrices')->where('branch_id', $b)
            ->whereIn('product_type', ['signature', 'cupcake', 'mini'])->orderBy('product_type')->orderBy('name')->get();
        return view('storefront.menu', compact('sizes', 'cakes'));
    }

    public function cake(string $slug)
    {
        $b = $this->branchId();
        $cake = Product::storefront()->with(['sizePrices', 'flavours', 'collections'])
            ->where('branch_id', $b)->where('slug', $slug)->firstOrFail();

        $related = Product::storefront()->with('sizePrices')->where('branch_id', $b)->where('id', '!=', $cake->id)
            ->whereHas('collections', fn ($q) => $q->whereIn('collections.id', $cake->collections->pluck('id')))
            ->take(4)->get();

        $occasionTypes = OccasionService::TYPES;
        $zones = \App\Models\DeliveryZone::where('branch_id', $b)->where('is_active', true)->orderBy('sort_order')->orderBy('name')->get(['id', 'name', 'fee']);
        return view('storefront.cake', compact('cake', 'related', 'occasionTypes', 'zones'));
    }

    public function order(Request $request)
    {
        $b = $this->branchId();
        $data = $request->validate([
            'product_id' => 'required|integer',
            'cake_size_id' => 'nullable|integer',
            'cake_flavour_id' => 'nullable|integer',
            'quantity' => 'required|integer|min:1|max:50',
            'name' => 'required|string|max:120',
            'phone' => ['required', 'string', 'max:20', 'regex:/^[0-9+\s\-]{9,16}$/'],
            'email' => 'nullable|email|max:120',
            'occasion_type' => 'nullable|string|max:40',
            'recipient_name' => 'nullable|string|max:120',
            'needed_on' => 'required|date|after_or_equal:today',
            'cake_message' => 'nullable|string|max:120',
            'fulfilment_type' => 'required|in:pickup,delivery',
            'delivery_address' => 'required_if:fulfilment_type,delivery|nullable|string|max:300',
            'delivery_zone_id' => 'nullable|integer',
            'notes' => 'nullable|string|max:800',
        ]);

        $cake = Product::storefront()->with(['sizePrices', 'flavours'])->where('branch_id', $b)->findOrFail($data['product_id']);

        // Price is always computed on the server, never trusted from the browser
        $price = (float) $cake->unit_price;
        $sizeLabel = null;
        if (!empty($data['cake_size_id'])) {
            $sp = $cake->sizePrices->firstWhere('cake_size_id', (int) $data['cake_size_id']);
            abort_if(!$sp, 422, 'Invalid size.');
            $price = (float) $sp->price;
            $sizeLabel = $sp->size->label;
        }
        $flavourName = null;
        if (!empty($data['cake_flavour_id'])) {
            $fl = $cake->flavours->firstWhere('id', (int) $data['cake_flavour_id']);
            abort_if(!$fl, 422, 'Invalid flavour.');
            $price += (float) $fl->pivot->extra_price;
            $flavourName = $fl->name;
        }

        if ($cake->sizePrices->isNotEmpty() && !$sizeLabel) {
            return back()->withInput()->withErrors(['cake_size_id' => 'Please choose a size.']);
        }

        $minDate = now()->addDays($cake->lead_time_days)->startOfDay();
        if (\Carbon\Carbon::parse($data['needed_on'])->lt($minDate)) {
            return back()->withInput()->withErrors(['needed_on' => "This cake needs at least {$cake->lead_time_days} day(s) notice."]);
        }

        // Delivery fee comes from the zone table, never from the browser
        $zone = null;
        if ($data['fulfilment_type'] === 'delivery') {
            $zones = \App\Models\DeliveryZone::where('branch_id', $b)->where('is_active', true)->get();
            if ($zones->isNotEmpty()) {
                $zone = $zones->firstWhere('id', (int) ($data['delivery_zone_id'] ?? 0));
                if (!$zone) {
                    return back()->withInput()->withErrors(['delivery_zone_id' => 'Please choose your delivery area.']);
                }
            }
        }

        $order = OrderRequest::create([
            'branch_id' => $b,
            'reference' => 'WEB-' . strtoupper(Str::random(6)),
            'name' => $data['name'],
            'phone' => preg_replace('/[\s\-]/', '', $data['phone']),
            'email' => $data['email'] ?? null,
            'product_id' => $cake->id,
            'cake_name' => $cake->name,
            'cake_size_id' => $data['cake_size_id'] ?? null,
            'size_label' => $sizeLabel,
            'cake_flavour_id' => $data['cake_flavour_id'] ?? null,
            'flavour_name' => $flavourName,
            'quantity' => $data['quantity'],
            'estimated_price' => $price * $data['quantity'],
            'occasion_type' => $data['occasion_type'] ?? null,
            'recipient_name' => $data['recipient_name'] ?? null,
            'needed_on' => $data['needed_on'],
            'cake_message' => $data['cake_message'] ?? null,
            'fulfilment_type' => $data['fulfilment_type'],
            'delivery_address' => $data['delivery_address'] ?? null,
            'notes' => $data['notes'] ?? null,
            'delivery_zone_id' => $zone?->id,
            'delivery_zone_name' => $zone?->name,
            'delivery_fee' => $zone ? (float) $zone->fee : 0,
        ]);

        $this->confirmBySms($order);

        return redirect()->route('store.thanks', $order->reference);
    }

    /** Tell the customer we got their request. Failure must never block the order. */
    private function confirmBySms(OrderRequest $order): void
    {
        if (OccasionService::setting($order->branch_id, 'order_confirm_sms', '1') !== '1') {
            return;
        }

        try {
            $business = OccasionService::setting($order->branch_id, 'business_name', \App\Support\Brand::name());
            $what = trim($order->cake_name . ($order->size_label ? ' (' . $order->size_label . ')' : ''));
            $message = "Hi {$order->name}, we have received your order request {$order->reference} for {$what}. "
                . "We will contact you shortly to confirm and share payment details. - {$business}";
            app(\App\Services\SMSService::class)->sendSMS($order->phone, $message);
        } catch (\Throwable $e) {
            report($e);
        }
    }

    public function thanks(string $reference)
    {
        $order = OrderRequest::where('reference', $reference)->where('created_at', '>=', now()->subDay())->firstOrFail();
        return view('storefront.thanks', compact('order'));
    }
}
