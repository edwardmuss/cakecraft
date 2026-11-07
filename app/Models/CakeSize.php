<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CakeSize extends Model
{
    protected $fillable = ['branch_id', 'label', 'weight_kg', 'serves', 'sort_order', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];
}
