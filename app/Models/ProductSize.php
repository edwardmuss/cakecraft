<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProductSize extends Model
{
    protected $fillable = ['product_id', 'cake_size_id', 'price', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];

    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
    public function size(): BelongsTo { return $this->belongsTo(CakeSize::class, 'cake_size_id'); }
}
