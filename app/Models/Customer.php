<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    protected $fillable = [
        'branch_id', 'user_id', 'name', 'phone', 'email', 'organization_name',
        'billing_address', 'delivery_location',
    ];

    public function branch(): BelongsTo { return $this->belongsTo(Branch::class); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
    public function invoices(): HasMany { return $this->hasMany(Invoice::class); }
    public function notifications(): HasMany { return $this->hasMany(NotificationLog::class); }
    public function occasions(): HasMany { return $this->hasMany(CustomerOccasion::class); }
}
