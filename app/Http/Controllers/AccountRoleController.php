<?php

namespace App\Http\Controllers;

use App\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class AccountRoleController extends Controller
{
    public function index()
    {
        $user = Auth::user();
        $branchId = $user->branch_id;

        // Fetch Roles based on hierarchy 
        $query = Role::with('permissions');
        if ($user->hasRole('super-admin')) {
            $query->whereNull('branch_id')->orWhereNotNull('branch_id');
        } elseif ($user->hasRole('manager')) {
            $query->where(function ($q) use ($branchId) {
                $q->whereIn('name', ['manager', 'customer'])
                    ->orWhere('branch_id', $branchId);
            });
        } else {
            $query->where(function ($q) use ($branchId) {
                $q->where('name', 'customer')
                    ->orWhere('branch_id', $branchId);
            });
        }
        $roles = $query->get();

        // Fetch ALL permissions grouped by category (Used for the 'View' mode)
        $allPermissions = Permission::all()->groupBy(function ($perm) {
            return explode(' ', $perm->name)[1] ?? 'other';
        });

        // Fetch ONLY the permission names the current user possesses (Used for the 'Edit' mode)
        $userPerms = $user->getAllPermissions()->pluck('name')->toArray();

        // Pass 'roles', 'allPermissions', and 'userPerms' to match the Blade logic
        return view('roles.index', compact('roles', 'allPermissions', 'userPerms'));
    }

    public function store(Request $request)
    {
        try {
            $this->validateRole($request);

            $userPerms = Auth::user()->getAllPermissions()->pluck('name')->toArray();
            $safePermissions = array_intersect($request->permissions ?? [], $userPerms);

            $role = Role::create([
                'name' => $request->name . " (#" . Auth::user()->branch_id . ")",
                'branch_id' => Auth::user()->branch_id,
                'guard_name' => 'web'
            ]);

            $role->syncPermissions($safePermissions);

            return redirect()->back()->with('success', 'Custom role created successfully.');
        } catch (\Spatie\Permission\Exceptions\RoleAlreadyExists $e) {
            return redirect()->back()->withErrors(['name' => 'This role name is already in use.'])->withInput();
        }
    }

    public function update(Request $request, Role $role)
    {
        // HARD SECURITY: System roles (branch_id is null) CANNOT be updated
        if (is_null($role->branch_id)) {
            return redirect()->back()->with('fail', 'System roles are read-only and cannot be modified.');
        }

        // OWNERSHIP SECURITY: Ensure they own this custom role
        if ($role->branch_id !== Auth::user()->branch_id) {
            abort(403, 'Unauthorized action.');
        }

        $this->validateRole($request, $role->id);

        // Backend Security: Filter injected permissions
        $userPerms = Auth::user()->getAllPermissions()->pluck('name')->toArray();
        $safePermissions = array_intersect($request->permissions ?? [], $userPerms);

        $branchId = Auth::user()->branch_id;
        $role->update([
            'name' => $request->name . " (#$branchId)"
        ]);

        $role->syncPermissions($safePermissions);

        return redirect()->back()->with('success', 'Custom role updated successfully.');
    }

    public function destroy(Role $role)
    {
        if (is_null($role->branch_id)) {
            return response()->json(['success' => false, 'message' => 'System roles cannot be deleted.'], 403);
        }

        if ($role->branch_id !== Auth::user()->branch_id) {
            return response()->json(['success' => false, 'message' => 'Unauthorized.'], 403);
        }

        if ($role->users()->count() > 0) {
            return response()->json([
                'success' => false,
                'message' => 'This role is currently assigned to ' . $role->users()->count() . ' users. Reassign them before deleting.'
            ], 422);
        }

        $role->delete();
        return response()->json(['success' => true, 'message' => 'Role deleted successfully.']);
    }

    private function validateRole(Request $request, $ignoreId = null)
    {
        $restricted = ['admin', 'manager', 'customer', 'system', 'super-admin'];
        $inputName = Str::lower($request->name);

        // 1. Check reserved system names
        if (in_array($inputName, $restricted)) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'name' => ['The name "' . $request->name . '" is reserved for system use.']
            ]);
        }

        // 2. Construct the full scoped name
        $branchId = Auth::user()->branch_id;
        $fullName = $request->name . " (#$branchId)";

        // 3. Check for existence (ignoring current ID if updating)
        $exists = Role::where('name', $fullName)
            ->where('guard_name', 'web')
            ->when($ignoreId, function ($query) use ($ignoreId) {
                return $query->where('id', '!=', $ignoreId);
            })
            ->exists();

        if ($exists) {
            throw \Illuminate\Validation\ValidationException::withMessages([
                'name' => ['A role with the name "' . $request->name . '" already exists for this account.']
            ]);
        }

        // 4. Standard string validation
        $request->validate([
            'name' => 'required|string|max:100',
            'permissions' => 'array'
        ]);
    }
}
