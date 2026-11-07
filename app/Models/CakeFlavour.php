<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class CakeFlavour extends Model
{
    protected $fillable = ['branch_id', 'name', 'description', 'sort_order', 'is_active'];
    protected $casts = ['is_active' => 'boolean'];
}
