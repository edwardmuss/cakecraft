<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OrderRequest extends Model
{
    protected $fillable = [
        'branch_id', 'customer_id', 'invoice_id', 'reference', 'name', 'phone', 'email',
        'product_id', 'cake_name', 'cake_size_id', 'size_label', 'cake_flavour_id', 'flavour_name',
        'quantity', 'estimated_price', 'occasion_type', 'recipient_name', 'needed_on', 'cake_message',
        'fulfilment_type', 'delivery_address', 'notes', 'status',
        'delivery_zone_id', 'delivery_zone_name', 'delivery_fee',
    ];
    protected $casts = ['needed_on' => 'date'];

    public function product(): BelongsTo { return $this->belongsTo(Product::class); }
    public function customer(): BelongsTo { return $this->belongsTo(Customer::class); }
    public function invoice(): BelongsTo { return $this->belongsTo(Invoice::class); }
}
