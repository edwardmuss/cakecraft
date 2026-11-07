<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class DeliveryZone extends Model
{
    protected $fillable = ['branch_id', 'name', 'fee', 'sort_order', 'is_active'];
    protected $casts = ['is_active' => 'boolean', 'fee' => 'float'];
}
