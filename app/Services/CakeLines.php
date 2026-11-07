<?php

namespace App\Services;

use App\Models\Product;
use Illuminate\Http\Request;

/** Shared by quotations: the cake picker catalogue and server-side line pricing. */
class CakeLines
{
    public static function catalogue(int $branchId): array
    {
        return Product::with(['sizePrices.size', 'flavours'])->where('branch_id', $branchId)->where('is_active', true)
            ->orderBy('name')->get()->map(fn ($p) => [
                'id' => $p->id,
                'name' => $p->name,
                'price' => (float) $p->unit_price,
                'sizes' => $p->sizePrices->where('is_active', true)->map(fn ($s) => [
                    'id' => $s->cake_size_id, 'label' => $s->size->label, 'price' => (float) $s->price,
                ])->values(),
                'flavours' => $p->flavours->map(fn ($f) => [
                    'id' => $f->id, 'name' => $f->name, 'extra' => (float) $f->pivot->extra_price,
                ])->values(),
            ])->values()->all();
    }

    /** @return array{0: array, 1: float} [rows, subtotal] */
    public static function build(Request $request, int $branchId): array
    {
        $rows = [];
        $subtotal = 0;

        foreach ($request->items as $item) {
            $product = !empty($item['product_id'])
                ? Product::with(['sizePrices.size', 'flavours'])->where('branch_id', $branchId)->find($item['product_id'])
                : null;

            $price = $product ? (float) $product->unit_price : 0;
            $sizeId = null; $sizeLabel = null; $flavourName = null;

            if ($product && !empty($item['cake_size_id'])) {
                $sp = $product->sizePrices->firstWhere('cake_size_id', (int) $item['cake_size_id']);
                if ($sp) { $price = (float) $sp->price; $sizeId = $sp->cake_size_id; $sizeLabel = $sp->size->label; }
            }
            if ($product && !empty($item['flavour_id'])) {
                $fl = $product->flavours->firstWhere('id', (int) $item['flavour_id']);
                if ($fl) { $price += (float) $fl->pivot->extra_price; $flavourName = $fl->name; }
            }
            if (isset($item['unit_price']) && $item['unit_price'] !== '') {
                $price = (float) $item['unit_price'];
            }

            $name = $product->name ?? ($item['item_name'] ?? null);
            if (!$name) {
                abort(response()->json(['errors' => ['items' => ['Each line needs a cake or a description.']]], 422));
            }

            $line = $price * (int) $item['quantity'];
            $subtotal += $line;
            $rows[] = [
                'product_id' => $product?->id,
                'cake_size_id' => $sizeId,
                'size_label' => $sizeLabel,
                'flavour_name' => $flavourName,
                'cake_message' => $item['cake_message'] ?? null,
                'item_name' => $name,
                'quantity' => (int) $item['quantity'],
                'unit_price' => $price,
                'total' => $line,
            ];
        }

        return [$rows, $subtotal];
    }
}
