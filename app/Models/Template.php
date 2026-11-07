<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Template extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'content',
        'branch_id',
    ];

    /**
     * Get the account that owns the template.
     */
    public function account()
    {
        return $this->belongsTo(Account::class);
    }
}
