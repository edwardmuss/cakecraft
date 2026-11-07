<?php

namespace App\Models;

use App\Models\Shortcode;
use App\Models\WhatsappWidget;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Branch extends Model
{
    use HasFactory;

    protected $fillable = [
        'branch_name',
        'branch_email',
        'company_phone',
        'kra_pin_number',
        'vat_percentage',
        'about',
        'payment_details',
        'website',
        'company_logo',
        'branch_cover_image',
        'locked',
        'last_ip',
        'isActive',
        'last_seen',
    ];

    protected $casts = [
        'vat_percentage' => 'double',
        'locked' => 'boolean',
        'isActive' => 'boolean',
        'last_seen' => 'datetime',
    ];

    public function getStatusAttribute()
    {
        if (!$this->isActive || $this->locked) {
            return 'inactive';
        }

        if ($this->last_seen && $this->last_seen->lt(now()->subDays(60))) {
            return 'dormant';
        }

        return 'active';
    }

    public function settings()
    {
        return $this->hasMany(Setting::class);
    }

    public function getSetting($key)
    {
        return $this->settings()->where('key', $key)->first()->value ?? null;
    }

    public function setSetting($key, $value)
    {
        $this->settings()->updateOrCreate(
            ['key' => $key],
            ['value' => $value]
        );
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function customers(): HasMany
    {
        return $this->hasMany(Customer::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function invoices(): HasMany
    {
        return $this->hasMany(Invoice::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function devices()
    {
        return $this->hasManyThrough(UserDevice::class, User::class);
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }
}
