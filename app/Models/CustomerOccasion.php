<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CustomerOccasion extends Model
{
    protected $fillable = [
        'branch_id', 'customer_id', 'last_invoice_id', 'occasion_type', 'recipient_name',
        'month', 'day', 'last_occasion_date', 'last_cake', 'reminders_enabled',
    ];
    protected $casts = ['last_occasion_date' => 'date', 'reminders_enabled' => 'boolean'];

    public function customer(): BelongsTo { return $this->belongsTo(Customer::class); }
    public function lastInvoice(): BelongsTo { return $this->belongsTo(Invoice::class, 'last_invoice_id'); }
    public function reminders(): HasMany { return $this->hasMany(OccasionReminder::class); }

    /** Next date this occasion falls on (today counts). Feb 29 falls back to Feb 28 in non-leap years. */
    public function nextDate(?Carbon $from = null): Carbon
    {
        $from = ($from ?? Carbon::today())->copy()->startOfDay();
        $date = $this->dateInYear($from->year);
        if ($date->lt($from)) {
            $date = $this->dateInYear($from->year + 1);
        }
        return $date;
    }

    public function dateInYear(int $year): Carbon
    {
        $day = $this->day;
        if ($this->month == 2 && $day == 29 && !Carbon::create($year, 1, 1)->isLeapYear()) {
            $day = 28;
        }
        return Carbon::create($year, $this->month, $day)->startOfDay();
    }

    public function reminderSentFor(int $year): bool
    {
        return $this->reminders()->where('year', $year)->where('status', 'sent')->exists();
    }
}
