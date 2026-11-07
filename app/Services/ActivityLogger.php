<?php

namespace App\Services;

use App\Models\User;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\Auth;

class ActivityLogger
{
    public static function log($action, $description = null, $entityType = null, $entityId = null, $branchId = null)
    {
        $user = Auth::user();

        ActivityLog::create([
            'created_by' => $user ? $user->id : null,
            'branch_id' => $user ? $user->branch_id : $branchId,
            'action' => $action,
            'entity_type' => $entityType,
            'entity_id' => $entityId,
            'description' => $description,
            'ip_address' => request()->ip(),
            'user_agent' => request()->userAgent(),
        ]);
    }
}
