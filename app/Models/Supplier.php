<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Supplier extends Model
{
    protected $fillable = [
        'branch_id',
        'name',
        'phone',
        'email',
        'kra_pin',
        'address',
    ];

    /**
     * Branch that owns this supplier.
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Expenses associated with this supplier.
     */
    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }
}
