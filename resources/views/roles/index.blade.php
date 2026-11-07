@extends('layouts.master')
@section('title') @lang('translation.roles-permissions') @endsection
@section('css')
<link href="{{ asset('build/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet">
@endsection
@section('content')
@component('components.breadcrumb')
@slot('li_1') @lang('translation.settings') @endslot
@slot('title') @lang('translation.roles-permissions') @endslot
@endcomponent

@include('includes.flash_message')

<div class="row">
    <div class="col-12">
        <div class="card">
            <div class="card-header d-flex align-items-center">
                <h5 class="card-title mb-0 flex-grow-1">Account Roles</h5>
                <button type="button" class="btn btn-primary" id="addRoleBtn">
                    <i class="ri-add-line align-bottom me-1"></i> Add Role
                </button>
            </div>
            <div class="card-body">
                <table class="table table-bordered align-middle">
                    <thead class="table-light">
                        <tr>
                            <th>Role Name</th>
                            <th>Permissions Count</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($roles as $role)
                        @php
                            $isSystemRole = is_null($role->branch_id);
                            $displayName = $isSystemRole ? ucfirst($role->name) : Str::before($role->name, ' (#');
                        @endphp
                        <tr>
                            <td>
                                <span class="fw-medium">{{ $displayName }}</span>
                                @if($isSystemRole)
                                    <span class="badge bg-secondary-subtle text-secondary ms-1">System</span>
                                @endif
                            </td>
                            <td><span class="badge bg-info-subtle text-info">{{ $role->permissions->count() }}</span></td>
                            <td>
                                <div class="d-flex gap-2">
                                    @if($isSystemRole)
                                        {{-- System roles only get a View button --}}
                                        <button class="btn btn-soft-primary btn-sm" onclick="viewRole({{ json_encode($role) }})">
                                            <i class="ri-eye-line align-bottom"></i> View Permissions
                                        </button>
                                    @else
                                        {{-- Custom roles get the full Edit button --}}
                                        <button class="btn btn-soft-info btn-sm" onclick="editRole({{ json_encode($role) }})">
                                            <i class="ri-edit-line align-bottom"></i> Edit
                                        </button>
                                        <button class="btn btn-soft-danger btn-sm" onclick="deleteRole({{ $role->id }}, '{{ $displayName }}')">
                                            <i class="ri-delete-bin-line align-bottom"></i>
                                        </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>

<div class="modal fade" id="roleModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="roleForm" method="POST" action="{{ route('roles.store') }}">
                @csrf
                <div id="methodField"></div>
                <div class="modal-header">
                    <h5 class="modal-title" id="modalTitle">Add Custom Role</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="card bg-light border-dashed mb-3">
                        <div class="card-body py-2">
                            <div class="form-check">
                                <input type="checkbox" id="global-select-all" class="form-check-input">
                                <label class="form-check-label fw-bold text-dark" for="global-select-all">
                                    Select All Permissions (Global)
                                </label>
                            </div>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Role Name <span class="text-danger">*</span></label>
                        <input type="text" name="name" id="roleNameInput" class="form-control" placeholder="e.g. Support Staff" required>
                        <small class="text-muted">Avoid system names like 'Manager' or 'Admin'.</small>
                    </div>

                    <label class="form-label">Assign Permissions</label>
                    <div class="row" id="permissions-container">
                        @foreach($allPermissions as $group => $perms)
                        <div class="col-md-4 mb-3 permission-group-container">
                            <div class="d-flex align-items-center mb-1">
                                <div class="form-check">
                                    <input class="form-check-input group-check" type="checkbox" id="group_{{ $group }}">
                                    <label class="form-check-label text-primary text-uppercase fs-12 fw-bold" for="group_{{ $group }}">
                                        {{ $group }}
                                    </label>
                                </div>
                            </div>
                            @foreach($perms as $perm)
                                @php
                                    // Check if the LOGGED IN user has this permission
                                    $userHasThis = in_array($perm->name, $userPerms);
                                @endphp
                                <div class="form-check mb-1 ms-2 perm-item" 
                                    data-perm-name="{{ $perm->name }}" 
                                    data-user-has="{{ $userHasThis ? 'true' : 'false' }}">
                                    
                                    <input class="form-check-input perm-check" type="checkbox" name="permissions[]" 
                                        value="{{ $perm->name }}" id="perm_{{ $perm->id }}">
                                    
                                    <label class="form-check-label fs-12 d-flex align-items-center" for="perm_{{ $perm->id }}">
                                        {{ Str::before($perm->name, ' ') }}
                                        
                                        @if(!$userHasThis)
                                            <span class="restricted-icon text-warning ms-1" style="display: none;" title="Restricted">
                                                <i class="ri-lock-2-line fs-10"></i>
                                            </span>
                                        @endif
                                    </label>
                                </div>
                            @endforeach
                        </div>
                        @endforeach
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                    <button type="submit" class="btn btn-primary">Save Role</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
@section('script')
<!-- list.js min js -->
<script src="{{asset('build/libs/list.js/list.min.js')}}"></script>
<script src="{{asset('build/libs/list.pagination.js/list.pagination.min.js')}}"></script>

<!-- Sweet Alerts js -->
<script src="{{asset('build/libs/sweetalert2/sweetalert2.min.js')}}"></script>
<script src="{{ asset('build/js/app.js') }}"></script>

<script>
    const roleModalElement = document.getElementById('roleModal');
    const roleModal = new bootstrap.Modal(roleModalElement);
    const form = document.getElementById('roleForm');
    const nameInput = document.getElementById('roleNameInput');
    const submitBtn = form.querySelector('button[type="submit"]');
    
    // Select All Elements
    const globalSelectAll = document.getElementById('global-select-all');
    const groupChecks = document.querySelectorAll('.group-check');
    const permChecks = document.querySelectorAll('.perm-check');

    /**
     * View System Role (Read-Only)
     */
    function viewRole(role) {
        prepareModal(role, true);
        document.getElementById('modalTitle').innerText = "System Role: " + role.name;
        roleModal.show();
    }

    /**
     * Edit Custom Role
     */
    function editRole(role) {
        prepareModal(role, false);
        document.getElementById('modalTitle').innerText = "Edit Role: " + role.name.split(' (#')[0];
        document.getElementById('methodField').innerHTML = '<input type="hidden" name="_method" value="PUT">';
        form.action = `/roles/${role.id}`;
        roleModal.show();
    }

    /**
     * Add Role Trigger
     */
    document.getElementById('addRoleBtn').addEventListener('click', function() {
        form.reset();
        form.action = "{{ route('roles.store') }}";
        document.getElementById('methodField').innerHTML = ''; 
        document.getElementById('modalTitle').innerText = "Add Custom Role";
        
        // Create a dummy role object with no permissions to reuse the filtering logic
        const dummyRole = {
            name: '',
            branch_id: {{ Auth::user()->branch_id }},
            permissions: []
        };

        // This will handle hiding all perms where data-user-has="false"
        prepareModal(dummyRole, false);
        
        roleModal.show();
    });

    // Delete Role
    function deleteRole(id, name) {
        Swal.fire({
            title: "Are you sure?",
            text: `You are about to delete the "${name}" role. This action cannot be undone!`,
            icon: "warning",
            showCancelButton: true,
            confirmButtonClass: "btn btn-primary w-xs me-2 mt-2",
            cancelButtonClass: "btn btn-danger w-xs mt-2",
            confirmButtonText: "Yes, delete it!",
            showCloseButton: true,
        }).then(function (result) {
            if (result.value) {
                // Perform AJAX delete
                fetch(`/roles/${id}`, {
                    method: 'DELETE',
                    headers: {
                        'X-CSRF-TOKEN': '{{ csrf_token() }}',
                        'Content-Type': 'application/json',
                        'Accept': 'application/json'
                    }
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        Swal.fire({
                            title: 'Deleted!',
                            text: data.message,
                            icon: 'success',
                            confirmButtonClass: 'btn btn-primary w-xs mt-2',
                            buttonsStyling: false
                        }).then(() => {
                            location.reload(); // Refresh to update table
                        });
                    } else {
                        Swal.fire({
                            title: 'Error!',
                            text: data.message,
                            icon: 'error',
                            confirmButtonClass: 'btn btn-primary w-xs mt-2',
                            buttonsStyling: false
                        });
                    }
                })
                .catch(error => {
                    Swal.fire("Error!", "Something went wrong on the server.", "error");
                });
            }
        });
    }

    /**
     * Logic for Checkbox Synchronization
     */
    document.addEventListener('DOMContentLoaded', function() {
        // Global Select All (Top-Down)
        globalSelectAll.addEventListener('change', function() {
            const isChecked = this.checked;
            permChecks.forEach(cb => { if(!cb.disabled) cb.checked = isChecked; });
            groupChecks.forEach(gc => { 
                if(!gc.disabled) {
                    gc.checked = isChecked;
                    gc.indeterminate = false;
                }
            });
        });

        // Group Select All (Mid-Down)
        groupChecks.forEach(groupBtn => {
            groupBtn.addEventListener('change', function() {
                const container = this.closest('.permission-group-container');
                const checkboxes = container.querySelectorAll('.perm-check');
                checkboxes.forEach(cb => { if(!cb.disabled) cb.checked = this.checked; });
                updateGlobalState();
            });
        });

        // Individual Permission Changes (Bottom-Up)
        permChecks.forEach(permBtn => {
            permBtn.addEventListener('change', function() {
                updateGroupState(this.closest('.permission-group-container'));
                updateGlobalState();
            });
        });
    });

    function updateGroupState(container) {
        const groupCheck = container.querySelector('.group-check');
        const checkboxes = container.querySelectorAll('.perm-check');
        const checkedCount = Array.from(checkboxes).filter(cb => cb.checked).length;

        if (checkedCount === 0) {
            groupCheck.checked = false;
            groupCheck.indeterminate = false;
        } else if (checkedCount === checkboxes.length) {
            groupCheck.checked = true;
            groupCheck.indeterminate = false;
        } else {
            groupCheck.checked = false;
            groupCheck.indeterminate = true;
        }
    }

    function updateGlobalState() {
        const total = Array.from(permChecks).filter(cb => !cb.disabled).length;
        const checked = Array.from(permChecks).filter(cb => cb.checked).length;

        if (checked === 0) {
            globalSelectAll.checked = false;
            globalSelectAll.indeterminate = false;
        } else if (checked === total && total > 0) {
            globalSelectAll.checked = true;
            globalSelectAll.indeterminate = false;
        } else {
            globalSelectAll.checked = false;
            globalSelectAll.indeterminate = true;
        }
    }

    function resetCheckboxes() {
        globalSelectAll.checked = false;
        globalSelectAll.indeterminate = false;
        globalSelectAll.disabled = false;
        groupChecks.forEach(gc => {
            gc.checked = false;
            gc.indeterminate = false;
            gc.disabled = false;
        });
        permChecks.forEach(pc => {
            pc.checked = false;
            pc.disabled = false;
        });
    }

    /**
     * Helper to setup modal state
     */
    function prepareModal(role, isReadOnly) {
        form.reset();
        // Strip the account ID suffix for the input field
        nameInput.value = role.branch_id ? role.name.split(' (#')[0] : role.name;
        toggleInputs(isReadOnly);

        const assignedNames = role.permissions.map(p => p.name);
        
        document.querySelectorAll('.perm-item').forEach(item => {
            const checkbox = item.querySelector('.perm-check');
            const icon = item.querySelector('.restricted-icon');
            const userHasPerm = item.getAttribute('data-user-has') === 'true';
            const isAssigned = assignedNames.includes(checkbox.value);

            // VISIBILITY LOGIC:
            if (isReadOnly) {
                // VIEW MODE: Show if role has it OR user has it
                item.style.display = (isAssigned || userHasPerm) ? 'block' : 'none';
            } else {
                // EDIT/ADD MODE: Show ONLY what the logged-in user actually has
                item.style.display = userHasPerm ? 'block' : 'none';
            }

            // STATE LOGIC
            checkbox.checked = isAssigned;
            
            // LOCK LOGIC: Disable if read-only OR if the user doesn't own this permission
            checkbox.disabled = isReadOnly || !userHasPerm;

            // ICON LOGIC: Show lock if visible (in View mode) but not touchable
            if (icon) {
                icon.style.display = (!userHasPerm && isAssigned) ? 'inline-block' : 'none';
            }
        });

        // CRITICAL: Refresh the headers after setting individual item visibility
        refreshAllHeaderStates(isReadOnly);
    }

    function refreshAllHeaderStates(isReadOnly) {
        document.querySelectorAll('.permission-group-container').forEach(container => {
            // 1. Hide the entire group if zero permissions inside are visible
            const visibleItems = Array.from(container.querySelectorAll('.perm-item'))
                                    .filter(item => item.style.display !== 'none');
            
            container.style.display = visibleItems.length > 0 ? 'block' : 'none';

            // 2. Update the "Select All" checkbox for this group
            updateGroupState(container);

            // 3. Disable Group Header if it's read-only OR if the user doesn't own ALL perms in this group
            const groupCheck = container.querySelector('.group-check');
            const hasRestrictedInGroup = container.querySelectorAll('.perm-item[data-user-has="false"]').length > 0;
            
            // If editing, we disable the group check if the user is missing any perms in that group 
            // to prevent them from "Selecting All" and accidentally trying to add perms they don't have
            groupCheck.disabled = isReadOnly; 
        });
        
        globalSelectAll.disabled = isReadOnly;
        updateGlobalState();
    }

    function toggleInputs(isReadOnly) {
        nameInput.readOnly = isReadOnly;
        submitBtn.style.display = isReadOnly ? 'none' : 'block';
    }
</script>
@endsection
