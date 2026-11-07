<?php

namespace App\Models;

use Spatie\Permission\Models\Role as SpatieRole;
use Illuminate\Database\Eloquent\Builder;

class Role extends SpatieRole
{
    protected $fillable = ['name', 'guard_name', 'branch_id'];

    // Scope to only show system roles + the current account's roles
    public function scopeForAccount(Builder $query, $branchId)
    {
        return $query->whereNull('branch_id')
            ->orWhere('branch_id', $branchId);
    }
}
