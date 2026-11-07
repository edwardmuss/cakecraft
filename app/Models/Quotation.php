<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Quotation extends Model
{
    protected $fillable = [
        'branch_id',
        'customer_id',
        'quote_number',
        'quote_date',
        'expiry_date',
        'subject',
        'subtotal',
        'tax_amount',
        'shipping_fee',
        'total_amount',
        'status',
        'terms_and_conditions',
        'notes',
        'created_by',
        'occasion_type',
        'occasion_date',
        'recipient_name',
        'fulfilment_type',
        'delivery_address',
    ];

    // The items inside the quote
    public function items()
    {
        return $this->hasMany(QuotationItem::class);
    }

    // The customer this quote belongs to
    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    // The user who created the quote
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Any invoices generated from this quote
    public function invoices()
    {
        return $this->hasMany(Invoice::class, 'quotation_id');
    }
}
