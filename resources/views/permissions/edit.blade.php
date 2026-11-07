@extends('layouts.master')
@section('title') @lang('translation.permissions') @endsection
@section('css')
<link href="{{ asset('build/libs/sweetalert2/sweetalert2.min.css') }}" rel="stylesheet">
@endsection
@section('content')
@component('components.breadcrumb')
@slot('li_1') @lang('translation.settings') @endslot
@slot('title') @lang('translation.permissions') @endslot
@endcomponent

<div class="row">
    <div class="card">
        <form action="{{ route('users.permissions.update', $user) }}" method="POST">
            @csrf
            <div class="card-header border-0 d-flex align-items-center">
                <h6 class="card-title mb-0 flex-grow-1">
                    <i class="ri-fingerprint-line align-middle me-1 lh-1"></i>
                    Editing Permissions for <span class="text-primary">{{ $user->name }}</span>
                </h6>
                <div class="flex-shrink-0">
                    <span class="badge bg-info-subtle text-info">Current Roles: {{ $user->getRoleNames()->implode(', ') }}</span>
                </div>
            </div>
            
            <div class="card-body border border-dashed border-end-0 border-start-0">
                @include('includes.flash_message')
                
                <div class="row mb-3">
                    <div class="col-md-12">
                        <div class="card bg-light border-dashed shadow-none">
                            <div class="card-body py-2">
                                <div class="form-check">
                                    <input type="checkbox" id="select-all" class="form-check-input">
                                    <label class="form-check-label fw-bold" for="select-all">
                                        Select All Assignable Permissions
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="permissions-container">
                    @php $count = 1; @endphp

                    @foreach ($allPermissions as $group => $groupPermissions)
                        @php
                            // FILTER: Only show permissions that User 8 actually possesses
                            $activePermissions = $groupPermissions->filter(function($p) use ($userPermissions) {
                                return in_array($p->name, $userPermissions);
                            });
                        @endphp

                        {{-- Only show the Group Header if there is at least one active permission in it --}}
                        @if($activePermissions->isNotEmpty())
                            <div class="row mb-3 permission-group-container">
                                <div class="col-md-12">
                                    <h5 class="mt-3 text-primary fw-bold">{{ $count }}. {{ ucwords($group) }}</h5>
                                </div>
                                
                                @foreach ($activePermissions as $permission)
                                    <div class="col-md-3">
                                        <div class="form-check mb-2">
                                            <input type="checkbox" 
                                                class="form-check-input" 
                                                checked 
                                                disabled> {{-- Always checked and disabled because this is a 'View' mode --}}
                                            
                                            <label class="form-check-label fs-12">
                                                {{ Str::before($permission->name, ' ') }}
                                                <span class="badge badge-soft-info ms-1">Active</span>
                                            </label>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                            <div class="border border-dashed border-end-0 border-start-0 my-3"></div>
                            @php $count++; @endphp
                        @endif
                    @endforeach

                    @if($userPermissions == [])
                        <div class="text-center p-5">
                            <i class="ri-shield-user-line display-4 text-muted"></i>
                            <p class="mt-2">This user has no direct or role-based permissions assigned.</p>
                        </div>
                    @endif
                </div>
            </div>
            
            <div class="card-footer">
                <div class="hstack gap-2 justify-content-start">
                    <button type="submit" class="btn btn-primary">Update User Permissions</button>
                    <a href="{{ route('roles.index') }}" class="btn btn-soft-secondary">Roles</a>
                </div>
            </div>
        </form>
    </div>
</div>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const selectAllCheckbox = document.getElementById('select-all');
        // Only target checkboxes that are NOT disabled (the ones the editor owns)
        const assignableCheckboxes = document.querySelectorAll('.permission-checkbox:not([disabled])');
        const allCheckboxes = document.querySelectorAll('.permission-checkbox');

        function updateSelectAllState() {
            const totalAssignable = assignableCheckboxes.length;
            const checkedAssignable = document.querySelectorAll('.permission-checkbox:checked:not([disabled])').length;

            if (checkedAssignable === 0) {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = false;
            } else if (checkedAssignable === totalAssignable) {
                selectAllCheckbox.checked = true;
                selectAllCheckbox.indeterminate = false;
            } else {
                selectAllCheckbox.checked = false;
                selectAllCheckbox.indeterminate = true;
            }
        }

        selectAllCheckbox.addEventListener('change', function () {
            assignableCheckboxes.forEach(function (checkbox) {
                checkbox.checked = selectAllCheckbox.checked;
            });
        });

        allCheckboxes.forEach(function (checkbox) {
            checkbox.addEventListener('change', updateSelectAllState);
        });

        // Initialize state
        updateSelectAllState();
    });
</script>
@endsection