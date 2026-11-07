<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserDevice extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'branch_id', 'device_name', 'device_ip', 'device_agent', 'device_location', 'last_used_at'];

    // A device belongs to a user
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
