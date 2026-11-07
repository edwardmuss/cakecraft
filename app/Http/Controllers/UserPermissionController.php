<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;

class UserPermissionController extends Controller
{
    public function edit(User $user)
    {
        $loggedInUser = Auth::user();

        // 1. OWNERSHIP & HIERARCHY SECURITY
        if ($user->branch_id !== $loggedInUser->branch_id) {
            abort(403, 'Unauthorized action.');
        }

        if (!$loggedInUser->hasRole('super-admin')) {
            // Managers/Customers can only edit users within their account scope
            if ($loggedInUser->id !== $user->id) {
                $allowedRoles = $loggedInUser->hasRole('manager') ? ['manager', 'customer'] : ['customer'];

                // Check if the target user has an allowed role
                if (!$user->hasAnyRole($allowedRoles)) {
                    abort(403, 'Unauthorized role access.');
                }
            }
        }

        // 2. GET USER PERMISSIONS (Via Roles)
        // getAllPermissions() returns both direct AND inherited permissions
        // getPermissionsViaRoles() returns ONLY those from roles (cleanest for your setup)
        $userPermissions = $user->getAllPermissions()->pluck('name')->toArray();

        // 3. SECURITY FILTER: The Manager should only be able to toggle 
        // permissions that they themselves possess.
        $loggedInUserPerms = $loggedInUser->getAllPermissions()->pluck('name')->toArray();

        // 4. GROUPING FOR BLADE
        // We fetch all system permissions but we'll use the 'userPerms' logic in Blade
        $allPermissions = Permission::all()->groupBy(function ($perm) {
            return explode(' ', $perm->name)[1] ?? 'other';
        });

        return view('permissions.edit', [
            'user'           => $user,
            'allPermissions' => $allPermissions,      // Every perm (for display)
            'userPermissions' => $userPermissions,     // What the target user HAS
            'loggedInUserPerms' => $loggedInUserPerms // What the editor is ALLOWED to give
        ]);
    }

    public function update(Request $request, User $user)
    {
        // Get the logged-in user
        $loggedInUser = Auth::user();

        // return "We are sorry, but this feature is currently disabled.";
        return redirect()->route('users.permissions.edit', $user)->with('fail', 'You are not allowed to edit permissions, go to roles instead.');

        if ($loggedInUser->id == $user->id && $loggedInUser->role != 'admin') {
            return redirect()->back()->with('fail', 'You are not allowed to edit your own permissions, login as a different user and try again');
        }

        $permissions = $request->input('permissions', []);
        $role = $request->input('role');

        // Sync user role
        // $user->syncRoles([$role]);

        // Sync user permissions
        $user->syncPermissions($permissions);

        // Refresh user roles and permissions
        $user->refresh();

        return redirect()->route('users.permissions.edit', $user)->with('success', 'Permissions updated successfully.');
    }

    private function getDefaultPermissionsForRole($roleName)
    {
        $defaultPermissions = [
            'admin' => Permission::all()->pluck('name')->toArray(),
            'manager' => Permission::whereNotIn('name', [
                'create accounts',
                'view accounts',
                'update accounts',
                'delete accounts',
                'create shortcodes',
                'view shortcodes',
                'update shortcodes',
                'delete shortcodes',
                'create adminsms',
                'view adminsms',
                'update adminsms',
                'delete adminsms',
                'create adminnotices',
                'view adminnotices',
                'update adminnotices',
                'delete adminnotices',
                'create adminnotifications',
                'view adminnotifications',
                'update adminnotifications',
                'delete adminnotifications'
            ])->pluck('name')->toArray(),
            'customer' => Permission::whereNotIn('name', [
                'create accounts',
                'view accounts',
                'update accounts',
                'delete accounts',
                'create shortcodes',
                'view shortcodes',
                'update shortcodes',
                'delete shortcodes',
                'create adminsms',
                'view adminsms',
                'update adminsms',
                'delete adminsms',
                'create adminnotices',
                'view adminnotices',
                'update adminnotices',
                'delete adminnotices',
                'create adminnotifications',
                'view adminnotifications',
                'update adminnotifications',
                'delete adminnotifications',
                'create topup',
                'view topup',
                'update topup',
                'delete topup',
                'create users',
                'view users',
                'update users',
                'delete users',
                'create subaccounts',
                'view subaccounts',
                'update subaccounts',
                'delete subaccounts'
            ])->pluck('name')->toArray()
        ];

        return $defaultPermissions[$roleName] ?? [];
    }
    public function getDefaultPermissions(Request $request)
    {
        $role = $request->query('role');
        $permissions = $this->getDefaultPermissionsForRole($role);

        return response()->json($permissions);
    }
}
