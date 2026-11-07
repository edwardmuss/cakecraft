@extends('layouts.master')
@section('title')
    @lang('translation.settings')
@endsection
@section('css')
    <link href="{{ asset('build/libs/sweetalert2/sweetalert2.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" type="text/css" />
    <!--datatable css-->
    <link href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css" rel="stylesheet" type="text/css" />
    <!--datatable responsive css-->
    <link href="https://cdn.datatables.net/responsive/2.2.9/css/responsive.bootstrap.min.css" rel="stylesheet" type="text/css" />
    <link href="https://cdn.datatables.net/buttons/2.2.2/css/buttons.dataTables.min.css" rel="stylesheet" type="text/css" />
@endsection
@section('content')
    <div class="position-relative mx-n4 mt-n4">
        <div class="profile-wid-bg profile-setting-img">
            <img src="@if ($branch->branch_cover_image != '') {{ asset('images/covers/' . $branch->branch_cover_image) }}@else {{ asset('images/covers/cover_image.jpg') }} @endif" class="profile-wid-img" alt="">
            <div class="overlay-content">
                <div class="text-end p-3">
                    <div class="p-0 ms-auto rounded-circle profile-photo-edit">
                        <form id="coverForm" enctype="multipart/form-data">
                            @csrf
                            <input id="profile-foreground-img-file-input" name="branch_cover_image" type="file" class="profile-foreground-img-file-input">
                            <label for="profile-foreground-img-file-input" class="profile-photo-edit btn btn-light">
                                <i class="ri-image-edit-line align-bottom me-1"></i> Change Cover
                            </label>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-xxl-3">
            <div class="card mt-n5">
                <div class="card-body p-4">
                    <div class="text-center">
                        <div class="profile-user position-relative d-inline-block mx-auto  mb-4">
                            <img src="@if ($branch->company_logo != '') {{ asset('images/avatars/' . $branch->company_logo) }}@else{{ asset('images/avatars/avatar-1.jpg') }} @endif"
                                class="rounded-circle avatar-xl img-thumbnail user-profile-image" alt="user-profile-image">
                            <div class="avatar-xs p-0 rounded-circle profile-photo-edit">
                                <form id="avatarForm" enctype="multipart/form-data">
                                    @csrf
                                    <input id="avatar-img-file-input" name="avatar" type="file" class="profile-img-file-input">
                                    <label for="avatar-img-file-input" class="profile-photo-edit avatar-xs">
                                        <span class="avatar-title rounded-circle bg-light text-body">
                                            <i class="ri-camera-fill"></i>
                                        </span>
                                    </label>
                                </form>
                            </div>
                        </div>
                        <h5 class="fs-16 mb-1">{{ $branch->branch_name}}</h5>
                        <p class="text-muted mb-0">{{ $branch->branch_email }}</p>
                        <p class="text-primay mb-0">Last Login: {{ $branch->last_seen }}</p>
                    </div>
                </div>
            </div>
            <!--end card--> 
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="flex-grow-1">
                            <h5 class="card-title mb-0">KRA PIN</h5>
                        </div>
                    </div>
                    <h3 class="text-primary mb-0">{{ $branch->kra_pin_number }}</h3>
                </div>
            </div>
            <!--end card-->
        </div>
        <!--end col-->
        <div class="col-xxl-9">
            <div class="card mt-xxl-n5">
                <div class="card-header">
                    <ul class="nav nav-tabs-custom rounded card-header-tabs border-bottom-0" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" data-bs-toggle="tab" href="#personalDetails" role="tab">
                                <i class="fas fa-home"></i>
                                Profile Details
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#users" role="tab">
                                <i class="far fa-envelope"></i>
                                Users
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#changePassword" role="tab">
                                <i class="far fa-user"></i>
                                Change Password
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#preferences" role="tab">
                                <i class="far fa-envelope"></i>
                                Preferences
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body p-4">
                    <div class="tab-content">
                        <div class="tab-pane active" id="personalDetails" role="tabpanel">
                            @include('includes.flash_message')
                            <form class=" gy-3 needs-validation" novalidate id="accountProfileForm" action="javascript:void(0);">
                                <div class="row">
                                    <!-- Company Name -->
                                    <div class="col-md-6 mb-3">
                                        <label for="branch_name" class="form-label">Company Name</label>
                                        <div class="form-icon">
                                            <input type="text" name="branch_name" id="branch_name" class="form-control form-control-icon" placeholder="Enter Company Name" value="{{ old('branch_name', $branch->branch_name) }}" required>
                                            <i class="ri-user-line"></i>
                                            <div class="invalid-feedback">Enter Company Name</div>
                                        </div>
                                    </div>
                                    <!-- Company Email -->
                                    <div class="col-md-6 mb-3">
                                        <label for="branch_email" class="form-label">Company Email</label>
                                        <div class="form-icon">
                                            <input type="email" name="branch_email" id="branch_email" class="form-control form-control-icon" placeholder="Company Email" value="{{ old('branch_email', $branch->branch_email) }}" required>
                                            <i class="ri-mail-unread-line"></i>
                                            <div class="invalid-feedback">Enter Company Email.</div>
                                        </div>
                                    </div>
                                    <!-- Company Phone -->
                                    <div class="col-md-6 mb-3">
                                        <label for="company_phone" class="form-label">Company Phone</label>
                                        <div class="form-icon">
                                            <input type="text" name="company_phone" id="company_phone" class="form-control form-control-icon" placeholder="Company Phone" value="{{ old('company_phone', $branch->company_phone) }}" required>
                                            <i class="ri-smartphone-line"></i>
                                            <div class="invalid-feedback">Enter Company Phone</div>
                                        </div>
                                    </div>
                                    <!-- Company Phone -->
                                    <div class="col-md-12 mb-3">
                                        <label for="website" class="form-label">Company Website</label>
                                        <div class="form-icon">
                                            <input type="url" name="website" id="website" class="form-control form-control-icon" placeholder="Company Website" value="{{ old('website', $branch->website) }}">
                                            <i class="ri-global-line"></i>
                                            <div class="invalid-feedback">Enter Company Website</div>
                                        </div>
                                    </div>
                                    <!-- About -->
                                    <div class="col-md-12 mb-3">
                                        <label for="about" class="form-label">About</label>
                                        <textarea class="form-control" id="about" name="about" rows="3">{{ old('about', $branch->about) }}</textarea>
                                    </div>
                                    <!-- Submit Button -->
                                    <div class="col-lg-12">
                                        <div class="hstack gap-2 justify-content-end">
                                            <button type="submit" class="btn btn-primary">Update</button>
                                        </div>
                                    </div>
                                </div>
                            </form>                            
                        </div>
                        <!--end tab-pane-->
                        <div class="tab-pane" id="users" role="tabpanel">
                            <div class="hstack gap-2 justify-content-end">
                                <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addUserModal">Add User</button>
                            </div>
                            <div id="newlink">
                                <div id="1">
                                    <div class="row">
                                        <table class="table">
                                            <thead>
                                                <th>Name</th>
                                                <th>Email</th>
                                                <th>Phone</th>
                                                <th>Active</th>
                                                <th>Action</th>
                                            </thead>
                                            <tbody id="usersTableBody">
                                                {{-- @include('pages.settings.partials.users_table', ['users' => $branch->users]) --}}
                                            </tbody>
                                        </table>
                                    </div>
                                    <!--end row-->
                                </div>
                            </div>
                            <!--end col-->
                        </div>
                        <!--end tab-pane-->
                        <div class="tab-pane" id="changePassword" role="tabpanel">
                            <form class="needs-validation" novalidate id="passwordForm">
                                <div class="row g-2">
                                    <div class="col-lg-4">
                                        <div>
                                            <label for="oldpasswordInput" class="form-label">Old Password*</label>
                                            <input type="password" class="form-control" id="oldpasswordInput" name="current_password" placeholder="Enter current password" required>
                                            <div class="invalid-feedback">
                                                Enter your current password
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div>
                                            <label for="newpasswordInput" class="form-label">New Password*</label>
                                            <input type="password" class="form-control" id="newpasswordInput" name="password" placeholder="Enter new password" required>
                                            <div class="invalid-feedback">
                                                Enter your new password
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-4">
                                        <div>
                                            <label for="confirmpasswordInput" class="form-label">Confirm Password*</label>
                                            <input type="password" class="form-control" id="confirmpasswordInput" name="password_confirmation" placeholder="Confirm password" required>
                                            <div class="invalid-feedback">
                                                Confirm your new password
                                            </div>
                                        </div>
                                    </div>
                                    <div class="col-lg-12">
                                        <div class="text-end">
                                            <button type="submit" class="btn btn-primary" id="changePasswordBtn">Change Password</button>
                                        </div>
                                    </div>
                                </div>
                            </form>
                            <div class="mt-4 mb-3 border-bottom pb-2">
                                <div class="float-end">
                                    {{-- <a href="javascript:void(0);" class="link-secondary">All Logout</a> --}}
                                </div>
                                <h5 class="card-title">Login History</h5>
                            </div>
                            @foreach ($latestDevices as $device )
                                <div class="d-flex align-items-center mb-3">
                                    <div class="flex-shrink-0 avatar-sm">
                                        <div class="avatar-title bg-light text-primary rounded-3 fs-18">
                                            <i class="ri-smartphone-line"></i>
                                        </div>
                                    </div>
                                    <div class="flex-grow-1 ms-3">
                                        <h6>{{ $device->device_name }} (by {{ $device->user->name }})</h6>
                                        <p class="text-muted mb-0">{{ $device->device_name }} ({{ $device->device_agent }}) {{ $device->device_ip }}, {{ $device->device_location }} - {{ \Carbon\Carbon::parse($device->last_used_at)->format('F d') }} at {{ \Carbon\Carbon::parse($device->last_used_at)->format('h:i:s A') }}</p>
                                    </div>
                                    <div>
                                        <a href="javascript:void(0);" class="link-secondary">Delete</a>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                        <!--end tab-pane-->
                        <div class="tab-pane" id="preferences" role="tabpanel">                            
                            <form action="{{ route('settings.update') }}" method="POST">
                                @csrf
                                <input type="hidden" id="accountId" name="branch_id" value="{{ $branch->id }}">
                                @include('components.preferences')
                                <button type="submit" class="btn btn-primary mt-3">Save Settings</button>
                            </form>
                        </div>
                        <!--end tab-pane-->                        
                    </div>
                </div>
            </div>
        </div>
        <!--end col-->
    </div>
    <!--end row-->
    <!-- Edit User Modal -->
<div class="modal fade flip" id="editUserModal" tabindex="-1" aria-labelledby="editUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-primary-subtle p-3">
          <h5 class="modal-title" id="editUserModalLabel">Edit User</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="editUserForm">
            <input type="hidden" id="userId" name="id">
            <div class="mb-3">
              <label for="name" class="form-label">Name</label>
              <input type="text" class="form-control" id="editName" name="name" required>
            </div>
            <div class="mb-3">
              <label for="name" class="form-label">Phone</label>
              <input type="text" class="form-control" id="editPhone" name="phone" required>
            </div>
            <div class="mb-3">
              <label for="email" class="form-label">Email</label>
              <input type="email" class="form-control" id="editEmail" name="email" required>
            </div>
            <div class="mb-3">
              <label for="password" class="form-label">Password (leave blank to retain current password)</label>
              <input type="password" class="form-control" id="editPassword" name="password">
            </div>
            <div class="mb-3">
                <label for="editRoles" class="form-label">Roles</label>
                <select class="form-control select2" id="editRoles" name="roles[]" multiple="multiple">
                    @foreach($roles as $role)
                        {{-- role->name must match the 'name' in your JSON --}}
                        <option value="{{ $role->name }}">
                            {{ ucfirst(Str::before($role->name, ' (#')) }}
                        </option>
                    @endforeach
                </select>
            </div>
            <button type="button" class="btn btn-primary" onclick="updateUser()">Save changes</button>
          </form>
        </div>
      </div>
    </div>
  </div>
  <!-- Add User Modal -->
<div class="modal fade flip" id="addUserModal" tabindex="-1" aria-labelledby="addUserModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-primary-subtle p-3">
          <h5 class="modal-title" id="addUserModalLabel">Add New User</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="addUserForm">
            <input type="hidden" id="accountId" name="branch_id" value="{{ $branch->id }}">
            <div class="mb-3">
              <label for="name" class="form-label">Name</label>
              <input type="text" class="form-control" id="addName" name="name" required>
            </div>
            <div class="mb-3">
              <label for="email" class="form-label">Email</label>
              <input type="email" class="form-control" id="addEmail" name="email" required>
            </div>
            <div class="mb-3">
              <label for="phone" class="form-label">Phone</label>
              <input type="text" class="form-control" id="addPhone" name="phone" required>
            </div>
            <div class="mb-3">
              <label for="password" class="form-label">Password</label>
              <input type="password" class="form-control" id="addPassword" name="password" required>
            </div>
            <div class="mb-3">
                <label for="addRoles" class="form-label">Roles</label>
                <select class="form-control select2" id="addRoles" name="roles[]" multiple required>
                    @foreach($roles as $role)
                        <option value="{{ $role->name }}">{{ ucfirst(Str::before($role->name, ' (#')) }}</option>
                    @endforeach
                </select>
            </div>
            <button type="button" class="btn btn-primary" onclick="addUser()">Add User</button>
          </form>
        </div>
      </div>
    </div>
  </div>
  
@endsection
@section('script')
    <script src="https://code.jquery.com/jquery-3.7.1.min.js" integrity="sha256-/JqT3SQfawRcv/BIHPThkBvs0OEvtFFmqPF/lYI/Cxo=" crossorigin="anonymous"></script>
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/toastify-js"></script>
    <!--select2 cdn-->
    <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>

    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script src="https://cdn.datatables.net/responsive/2.2.9/js/dataTables.responsive.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/dataTables.buttons.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.print.min.js"></script>
    <script src="https://cdn.datatables.net/buttons/2.2.2/js/buttons.html5.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/vfs_fonts.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/pdfmake/0.1.53/pdfmake.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jszip/3.1.3/jszip.min.js"></script>
    <script src="{{ asset('build/js/pages/datatables.init.js') }}"></script>

    <script src="{{ asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('build/js/pages/profile-setting.init.js') }}"></script>
    <script src="{{ asset('build/js/pages/form-validation.init.js') }}"></script>
    <script src="{{ asset('build/js/pages/notifications.init.js') }}"></script>
    <script src="{{ asset('build/js/pages/select2.init.js') }}"></script>
    <script src="{{ asset('build/js/app.js') }}"></script>
    <script>
        // Fetch User on Intialize
        fetchUsers();

        document.getElementById('passwordForm').addEventListener('submit', function (e) {
            e.preventDefault();

            const currentPassword = document.getElementById('oldpasswordInput').value;
            const newPassword = document.getElementById('newpasswordInput').value;
            const confirmPassword = document.getElementById('confirmpasswordInput').value;

            axios.post("{{ route('updatePassword', Auth::user()->id) }}", {
                current_password: currentPassword,
                password: newPassword,
                password_confirmation: confirmPassword
            })
            .then(function (response) {
                const { isSuccess, Message } = response.data;
                const bgClass = isSuccess ? 'bg-success' : 'bg-danger';
                showToast(Message, 6000, true, 'top', 'center', bgClass, true);

                // Clear the form inputs if success
                if (isSuccess) {
                    document.getElementById('passwordForm').reset();
                }
            })
            .catch(function (error) {
                if (error.response && error.response.status === 422) {
                    // Laravel validation error
                    const errors = error.response.data.errors;

                    // Loop through all validation errors and display them via toast
                    for (const key in errors) {
                        if (errors.hasOwnProperty(key)) {
                            errors[key].forEach(message => {
                                showToast(message, 6000, true, 'top', 'center', 'bg-danger', true);
                            });
                        }
                    }
                } else {
                    // General error
                    showToast('An error occurred. Please try again.', 6000, true, 'top', 'center', 'bg-danger', true);
                }
            });
        });

        // update account details
        document.getElementById('accountProfileForm').addEventListener('submit', function (e) {
            e.preventDefault();
            let formData = new FormData(this);

            axios.post("{{ route('updateAccountDetails', $branch->id) }}", formData)
                .then(function (response) {
                    if (response.data.isSuccess) {
                        showToast(response.data.Message, 6000, true, 'top', 'center', 'bg-success', true);
                    } else {
                        showToast(response.data.Message, 6000, true, 'top', 'center', 'bg-danger', true);
                    }
                })
                .catch(function (error) {
                    if (error.response.data.errors) {
                        let errors = error.response.data.errors;
                        for (let key in errors) {
                            showToast(errors[key][0], 6000, true, 'top', 'center', 'bg-danger', true);
                        }
                    } else {
                        showToast("Something went wrong!", 6000, true, 'top', 'center', 'bg-danger', true);
                    }
                });
        });

        // Toggle active state event
        $(document).on('change', '.toggle-active', function () {
            let userId = $(this).data('id');
            let isActive = $(this).is(':checked');

            axios.post(`/update-user-status/${userId}`, { isActive })
                .then(response => {
                    const { isSuccess, message } = response.data;
                    const bgClass = isSuccess ? 'bg-success' : 'bg-success';
                    showToast(message, 6000, true, 'top', 'center', bgClass, true);
                })
                .catch(error => {
                    if (error.response.status === 401 || error.response.status === 419) {
                        showToast('Session Expired', 3000, true, 'top', 'center', 'bg-danger', true );
                        // Redirect to the login page
                        const currentUrl = window.location.href;
                        setTimeout(function(){
                            window.location.href = '/login';
                        }, 3000); 
                    }
                    showToast('Failed to update user status!', 5000, true, 'top', 'center', 'bg-danger', true);
                });
        });

        function showEditModal(user) {
            // Fill basic fields
            document.getElementById('userId').value = user.id;
            document.getElementById('editName').value = user.name;
            document.getElementById('editPhone').value = user.phone;
            document.getElementById('editEmail').value = user.email;
            document.getElementById('editPassword').value = '';

            // Extract the names from the 'roles' array in your JSON
            const userRoleNames = (user.roles || []).map(role => role.name);

            // Select2 specific prefill logic
            const $roleSelect = $('#editRoles');
            
            // Clear previous selections first
            $roleSelect.val(null).trigger('change'); 
            
            // Set new values and trigger change so Select2 updates the tags
            $roleSelect.val(userRoleNames).trigger('change');

            // Show the modal
            new bootstrap.Modal(document.getElementById('editUserModal')).show();
        }

        // Critical fix for Select2 inside Bootstrap Modals
        $(document).ready(function() {
            $('.select2').each(function() {
                $(this).select2({
                    dropdownParent: $(this).closest('.modal')
                });
            });
        });

        function updateUser() {
            const id = document.getElementById('userId').value;
            const formData = new FormData(document.getElementById('editUserForm'));

            axios.post(`/update-user-details/${id}`, formData)
                .then(response => {
                    fetchUsers();
                    $('#editUserModal').modal('hide');
                    showToast(response.data.message, 5000, true, 'top', 'center', 'bg-success', true);
                    
                    // Clear the form so passwords don't linger for the next time the modal opens
                    document.getElementById('editUserForm').reset();
                })
                .catch(error => {
                    if (error.response) {
                        if (error.response.status === 422) {
                            // Catch Laravel Validation Errors (e.g., Email already exists, Password too short)
                            let errors = error.response.data.errors;
                            for (const key in errors) {
                                if (errors.hasOwnProperty(key)) {
                                    errors[key].forEach(message => {
                                        showToast(message, 6000, true, 'top', 'center', 'bg-danger', true);
                                    });
                                }
                            }
                        } else if (error.response.status === 401 || error.response.status === 419) {
                            // Catch Session Timeouts
                            showToast('Session Expired', 3000, true, 'top', 'center', 'bg-danger', true);
                            setTimeout(function(){
                                window.location.href = '/login';
                            }, 3000); 
                        } else {
                            // Fallback for 500 errors or custom exceptions
                            let msg = error.response.data.message || "Something went wrong!";
                            showToast(msg, 5000, true, 'top', 'center', 'bg-danger', true);
                        }
                    } else {
                        showToast("Network error. Please check your connection.", 5000, true, 'top', 'center', 'bg-danger', true);
                    }
                    console.error(error);
                });
        }

        function addUser() {
            const formData = new FormData(document.getElementById('addUserForm'));

            axios.post("{{ route('addUser') }}", formData)
                .then(response => {
                    fetchUsers();
                    $('#addUserModal').modal('hide');
                    showToast(response.data.message, 5000, true, 'top', 'center', 'bg-success', true);
                    
                    // Clear the form after successful creation
                    document.getElementById('addUserForm').reset();
                    $('#addRoles').val(null).trigger('change'); // Reset Select2 dropdown
                })
                .catch(error => {
                    if (error.response) {
                        if (error.response.status === 422) {
                            // Catch Laravel Validation Errors (e.g., Email already exists)
                            let errors = error.response.data.errors;
                            for (const key in errors) {
                                if (errors.hasOwnProperty(key)) {
                                    errors[key].forEach(message => {
                                        showToast(message, 6000, true, 'top', 'center', 'bg-danger', true);
                                    });
                                }
                            }
                        } else if (error.response.status === 401 || error.response.status === 419) {
                            // Catch Session Timeouts
                            showToast('Session Expired', 3000, true, 'top', 'center', 'bg-danger', true);
                            setTimeout(function(){
                                window.location.href = '/login';
                            }, 3000); 
                        } else if (error.response.data && error.response.data.errors && error.response.data.errors.general) {
                            // Catch your custom 500 DB Transaction rollback error
                            showToast(error.response.data.errors.general[0], 6000, true, 'top', 'center', 'bg-danger', true);
                        } else {
                            // Fallback
                            showToast("Something went wrong!", 5000, true, 'top', 'center', 'bg-danger', true);
                        }
                    } else {
                        showToast("Network error. Please check your connection.", 5000, true, 'top', 'center', 'bg-danger', true);
                    }
                    console.error(error);
                });
        }

        // Event listener for deleting user
        $(document).on('click', '.delete-user', function (e) {
            e.preventDefault();
            
            // Use parseInt to ensure strict equality (===) works safely
            let userId = parseInt($(this).data('id'));
            let currentUserId = parseInt('{{ Auth::id() }}');
            
            if(currentUserId === userId){
                Swal.fire('Action Denied!', 'You cannot delete your own account.', 'error');
                return;
            }

            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            }).then((result) => {
                if (result.isConfirmed) {
                    axios.post(`/delete-user/${userId}`)
                        .then(response => {
                            fetchUsers(); // Refetch users after deletion
                            Swal.fire('Deleted!', response.data.message, 'success');
                        })
                        .catch(error => {
                            // Dynamically pull the error message from the backend controller
                            let msg = error.response && error.response.data.message 
                                        ? error.response.data.message 
                                        : 'Failed to delete user.';
                            
                            Swal.fire('Error!', msg, 'error');
                        });
                }
            });
        });

        // Function to fetch and reload users
        function fetchUsers() {
        axios.get("{{ route('fetchUsers') }}")
            .then(response => {
                document.getElementById('usersTableBody').innerHTML = response.data;
                // showToast('Users fetched successfully!', 5000, true, 'top', 'center', 'bg-success', true);
            })
            .catch(error => {
                // showToast('Error fetching users!', 5000, true, 'top', 'center', 'bg-danger', true);
            });
        }

    </script>
@endsection
