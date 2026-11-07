<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

/**
 * A "Product" is a Cake: it has a size price grid, optional flavours and belongs to collections.
 */
class Product extends Model
{
    public const TYPES = [
        'signature' => 'Signature flavour cake',
        'custom'    => 'Custom / themed cake',
        'wedding'   => 'Wedding cake',
        'cupcake'   => 'Cupcakes',
        'mini'      => 'Mini cakes',
        'other'     => 'Other',
    ];

    protected $fillable = [
        'branch_id', 'name', 'slug', 'description', 'short_description', 'image', 'gallery',
        'unit_price', 'track_stock', 'current_stock', 'reorder_level', 'is_active',
        'product_type', 'is_featured', 'is_published', 'lead_time_days',
    ];

    protected $casts = [
        'gallery' => 'array',
        'is_active' => 'boolean',
        'is_featured' => 'boolean',
        'is_published' => 'boolean',
    ];

    protected static function booted(): void
    {
        static::saving(function (Product $p) {
            if (empty($p->slug)) {
                $base = Str::slug($p->name) ?: 'cake';
                $slug = $base; $i = 2;
                while (static::where('slug', $slug)->where('id', '!=', $p->id ?? 0)->exists()) {
                    $slug = $base . '-' . $i++;
                }
                $p->slug = $slug;
            }
        });
    }

    public function branch(): BelongsTo { return $this->belongsTo(Branch::class); }
    public function invoiceItems(): HasMany { return $this->hasMany(InvoiceItem::class); }
    public function movements() { return $this->hasMany(StockMovement::class); }

    public function collections(): BelongsToMany
    {
        return $this->belongsToMany(Collection::class, 'collection_product');
    }

    public function sizePrices(): HasMany
    {
        return $this->hasMany(ProductSize::class)->with('size');
    }

    public function flavours(): BelongsToMany
    {
        return $this->belongsToMany(CakeFlavour::class, 'product_flavours')->withPivot('extra_price');
    }

    public function scopeStorefront($q)
    {
        return $q->where('is_active', true)->where('is_published', true);
    }

    public function getMinPriceAttribute(): float
    {
        $min = $this->sizePrices->where('is_active', true)->min('price');
        return (float) ($min ?? $this->unit_price);
    }

    public function getImageUrlAttribute(): string
    {
        return $this->image ? asset('storage/' . $this->image) : asset('images/cake-placeholder.svg');
    }
}
