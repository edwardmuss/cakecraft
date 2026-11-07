<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class KcbTransaction extends Model
{
    use HasFactory;
    protected $fillable = [
        'transaction_reference',
        'request_id',
        'channel_code',
        'timestamp',
        'transaction_amount',
        'currency',
        'customer_reference',
        'customer_name',
        'customer_mobile_number',
        'balance',
        'narration',
        'credit_branch_identifier',
        'organization_short_code',
        'till_number'
    ];
}
