<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Invoice extends Model
{
    protected $fillable = [
        'branch_id',
        'customer_id',
        'quotation_id',
        'invoice_number',
        'status',
        'subtotal',
        'tax_amount',
        'shipping_fee',
        'total_amount',
        'amount_paid',
        'due_date',
        'reminder_count',
        'notes',
        'delivered_at',
        'occasion_type',
        'occasion_date',
        'recipient_name',
        'fulfilment_type',
        'delivery_address',
        'source',
    ];

    protected $casts = [
        'occasion_date' => 'date',
    ];

    public function branch(): BelongsTo
    {
        return $this->belongsTo(Branch::class);
    }

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function items(): HasMany
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function notifications(): HasMany
    {
        return $this->hasMany(NotificationLog::class);
    }

    public function quotation()
    {
        return $this->belongsTo(Quotation::class, 'quotation_id');
    }
}
