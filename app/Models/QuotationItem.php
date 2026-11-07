<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QuotationItem extends Model
{
    protected $fillable = [
        'quotation_id',
        'product_id',
        'item_name',
        'quantity',
        'unit_price',
        'total',
        'cake_size_id',
        'size_label',
        'flavour_name',
        'cake_message',
    ];

    public function quotation()
    {
        return $this->belongsTo(Quotation::class);
    }
}
