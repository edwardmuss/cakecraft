<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ExpenseCategory extends Model
{
    protected $fillable = [
        'branch_id',
        'name',
    ];

    /**
     * Branch that owns this expense category.
     */
    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    /**
     * Expenses under this category.
     */
    public function expenses()
    {
        return $this->hasMany(Expense::class);
    }
}
