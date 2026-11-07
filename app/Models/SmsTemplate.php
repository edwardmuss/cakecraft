<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SmsTemplate extends Model
{
    protected $fillable = [
        'branch_id',
        'trigger_name',
        'message',
        'is_active',
    ];
}
