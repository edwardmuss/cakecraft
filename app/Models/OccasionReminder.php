<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class OccasionReminder extends Model
{
    protected $fillable = ['customer_occasion_id', 'year', 'message', 'status', 'sent_by', 'sent_at'];
    protected $casts = ['sent_at' => 'datetime'];

    public function occasion(): BelongsTo { return $this->belongsTo(CustomerOccasion::class, 'customer_occasion_id'); }
}
