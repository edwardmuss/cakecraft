<?php

namespace App\Models;

use DateTimeInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Otp extends Model
{
    use HasFactory;

    // protected $table = 'otps';

    // Specify the fields that are mass assignable
    protected $fillable = [
        'branch_id',
        'phone',
        'otp',
        'character_type',
        'length',
        'expiry_time',
        'expires_at',
        'used',
    ];

    // Cast attributes to specific types
    protected $casts = [
        'used' => 'boolean',
        'expires_at' => 'datetime',
        'length' => 'integer',
        'expiry_time' => 'integer',
        'character_type' => 'string',
    ];

    // protected function serializeDate(DateTimeInterface $date)
    // {
    //     return $date->setTimezone(new \DateTimeZone(config('app.timezone')))
    //         ->format('Y-m-d H:i:s');
    // }
}
