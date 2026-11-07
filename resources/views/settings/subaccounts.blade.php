@extends('layouts.master')
@section('title')
    @lang('translation.settings')
@endsection
@section('css')
    <link href="{{ asset('build/libs/sweetalert2/sweetalert2.min.css')}}" rel="stylesheet" type="text/css" />
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" type="text/css" />
@endsection
@section('content')
    <div class="position-relative mx-n4 mt-n4">
        <div class="profile-wid-bg profile-setting-img">
            <img src="{{ asset('build/images/profile-bg.jpg') }}" class="profile-wid-img" alt="">
            <div class="overlay-content">
                <div class="text-end p-3">
                    <div class="p-0 ms-auto rounded-circle profile-photo-edit">
                        <input id="profile-foreground-img-file-input" type="file" class="profile-foreground-img-file-input">
                        <label for="profile-foreground-img-file-input" class="profile-photo-edit btn btn-light">
                            <i class="ri-image-edit-line align-bottom me-1"></i> Change Cover
                        </label>
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
                            <img src="@if ($subaccount->avatar != '') {{ asset('images/' . $subaccount->avatar) }}@else{{ asset('build/images/users/avatar-1.jpg') }} @endif"
                                class="rounded-circle avatar-xl img-thumbnail user-profile-image" alt="user-profile-image">
                            <div class="avatar-xs p-0 rounded-circle profile-photo-edit">
                                <input id="profile-img-file-input" type="file" class="profile-img-file-input">
                                <label for="profile-img-file-input" class="profile-photo-edit avatar-xs">
                                    <span class="avatar-title rounded-circle bg-light text-body">
                                        <i class="ri-camera-fill"></i>
                                    </span>
                                </label>
                            </div>
                        </div>
                        <h5 class="fs-16 mb-1">{{ $subaccount->branch_name}}</h5>
                        <p class="text-muted mb-0">{{ $subaccount->branch_email }}</p>
                        <p class="text-primay mb-0">Last Login: {{ $subaccount->last_seen }}</p>
                    </div>
                </div>
            </div>
            <!--end card-->
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="flex-grow-1">
                            <h5 class="card-title mb-0">@lang('translation.account') @lang('translation.balance')</h5>
                        </div>
                    </div>
                    <h3 class="text-primary mb-0"><span class="subaccount_balance_span">KES {{ $subaccount->balance_kes }}</span></h3>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-2">
                        <div class="flex-grow-1">
                            <h5 class="card-title mb-0">@lang('translation.subaccount-id')</h5>
                        </div>
                    </div>
                    <h3 class="text-primary mb-0">{{ $subaccount->id }}</h3>
                </div>
            </div>
            <div class="card">
                <div class="card-body">
                    <div class="d-flex align-items-center mb-4">
                        <div class="flex-grow-1">
                            <h5 class="card-title mb-0">@lang('translation.pricing') (KES)</h5>
                        </div>
                    </div>
                    <div class="mb-3 d-flex">
                        <div class="avatar-xs d-block flex-shrink-0 me-3">
                            <span class="avatar-title rounded-circle fs-16 bg-body text-body">
                                <i class="ri-message-3-line"></i>
                            </span>
                        </div>
                        <input type="text" class="form-control" id="" readonly placeholder="SMS Pricing"
                            value="@lang('translation.sms'): {{ $subaccount->sms_pricing }}">
                    </div>
                    <div class="mb-3 d-flex">
                        <div class="avatar-xs d-block flex-shrink-0 me-3">
                            <span class="avatar-title rounded-circle fs-16 bg-body text-body">
                                <i class="ri-voiceprint-line"></i>
                            </span>
                        </div>
                        <input type="text" class="form-control" id="" readonly placeholder="Voice Pricing"
                            value="@lang('translation.voice'): {{ $subaccount->voice_pricing }}">
                    </div>
                    
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
                            <a class="nav-link" data-bs-toggle="tab" href="#preferences" role="tab">
                                <i class="far fa-envelope"></i>
                                Preferences
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#sender_ids" role="tab">
                                <i class="far fa-envelope"></i>
                                Sender IDs
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" data-bs-toggle="tab" href="#transactions" role="tab">
                                <i class="far fa-envelope"></i>
                                Accounting
                            </a>
                        </li>
                    </ul>
                </div>
                <div class="card-body p-4">
                    <div class="tab-content">
                        <div class="tab-pane active" id="personalDetails" role="tabpanel">
                            <form class=" gy-3 needs-validation" novalidate id="accountProfileForm" action="javascript:void(0);">
                                <div class="row">
                                    <!-- Company Name -->
                                    <div class="col-md-6 mb-3">
                                        <label for="branch_name" class="form-label">Company Name</label>
                                        <div class="form-icon">
                                            <input type="text" name="branch_name" id="branch_name" class="form-control form-control-icon" placeholder="Enter Company Name" value="{{ old('branch_name', $subaccount->branch_name) }}" required>
                                            <i class="ri-user-line"></i>
                                            <div class="invalid-feedback">Enter Company Name</div>
                                        </div>
                                    </div>
                                    <!-- Company Email -->
                                    <div class="col-md-6 mb-3">
                                        <label for="branch_email" class="form-label">Company Email</label>
                                        <div class="form-icon">
                                            <input type="email" name="branch_email" id="branch_email" class="form-control form-control-icon" placeholder="Company Email" value="{{ old('branch_email', $subaccount->branch_email) }}" required>
                                            <i class="ri-mail-unread-line"></i>
                                            <div class="invalid-feedback">Enter Company Email.</div>
                                        </div>
                                    </div>
                                    <!-- Company Phone -->
                                    <div class="col-md-6 mb-3">
                                        <label for="company_phone" class="form-label">Company Phone</label>
                                        <div class="form-icon">
                                            <input type="text" name="company_phone" id="company_phone" class="form-control form-control-icon" placeholder="Company Phone" value="{{ old('company_phone', $subaccount->company_phone) }}" required>
                                            <i class="ri-smartphone-line"></i>
                                            <div class="invalid-feedback">Enter Company Phone</div>
                                        </div>
                                    </div>
                                    <!-- Category -->
                                    <div class="col-md-6 mb-3">
                                        <label for="category" class="form-label">Category</label>
                                        <div class="form-icon">
                                            <select class="form-control form-control-icon" id="category" name="category_id" required>
                                                <option value="">--Select--</option>
                                                @foreach($categories as $category)
                                                    <option value="{{ $category->id }}" {{ old('category_id', $subaccount->category_id) == $category->id ? 'selected' : '' }}>
                                                        {{ $category->name }}
                                                    </option>
                                                @endforeach
                                            </select>
                                            <i class="ri-profile-line"></i>
                                            <div class="invalid-feedback">Please choose a category.</div>
                                        </div>
                                    </div>
                                    <!-- Company Phone -->
                                    <div class="col-md-12 mb-3">
                                        <label for="website" class="form-label">Company Website</label>
                                        <div class="form-icon">
                                            <input type="url" name="website" id="website" class="form-control form-control-icon" placeholder="Company Website" value="{{ old('website', $subaccount->website) }}">
                                            <i class="ri-global-line"></i>
                                            <div class="invalid-feedback">Enter Company Website</div>
                                        </div>
                                    </div>
                                    <!-- About -->
                                    <div class="col-md-12 mb-3">
                                        <label for="about" class="form-label">About</label>
                                        <textarea class="form-control" id="about" name="about" rows="3">{{ old('about', $subaccount->about) }}</textarea>
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
                                                {{-- @include('pages.settings.partials.users_table', ['users' => $subaccount->users]) --}}
                                            </tbody>
                                        </table>
                                    </div>
                                    <!--end row-->
                                </div>
                            </div>
                            <!--end col-->
                        </div>
                        <!--end tab-pane-->
                        <div class="tab-pane" id="preferences" role="tabpanel">
                            <form action="{{ route('settings.update') }}" method="POST">
                                @csrf
                                <input type="hidden" id="accountId" name="branch_id" value="{{ $subaccount->id }}">
                                @include('components.preferences')
                                <button type="submit" class="btn btn-primary mt-3">Save Settings</button>
                            </form>
                        </div>
                        <!--end tab-pane-->
                        <div class="tab-pane" id="sender_ids" role="tabpanel">
                            <div class="hstack gap-2 justify-content-end">
                                <button class="btn btn-primary" id="assignShortcodeButton">Assign Sender</button>
                            </div>
                            <div id="newlink">
                                <div id="1">
                                    <div class="row">
                                        <table class="table">
                                            <thead>
                                                <th>Name</th>
                                                <th>Type</th>
                                                <th>Active</th>
                                                <th>Action</th>
                                            </thead>
                                            <tbody id="sendersTableBody">
                                            </tbody>
                                        </table>
                                    </div>
                                    <!--end row-->
                                </div>
                            </div>
                            <!--end col-->
                        </div>
                        <!--end tab-pane-->
                        <div class="tab-pane" id="transactions" role="tabpanel">
                            <div class="hstack gap-2 justify-content-end">
                                <button class="btn btn-primary" id="topUpButton">Top Up</button>
                            </div>
                            <div id="newlink">
                                <div id="1">
                                    <div class="row">
                                        <table class="table">
                                            <thead>
                                                <th>Reference</th>
                                                <th>Amount</th>
                                                <th>Units</th>
                                                <th>Created</th>
                                                <th>Action</th>
                                            </thead>
                                            <tbody id="transactionsTableBody">
                                                
                                            </tbody>
                                        </table>
                                    </div>
                                    <!--end row-->
                                </div>
                            </div>
                            <!--end col-->
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
            <input type="hidden" id="accountId" name="branch_id" value="{{ $subaccount->id }}">
            <input type="hidden" id="is_subaccount" name="is_subaccount" value="{{ $subaccount->id }}">
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
            <button type="button" class="btn btn-primary" onclick="addUser()">Add User</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Add Subaccount Modal -->
<div class="modal fade flip" id="addSubAccountModal" tabindex="-1" aria-labelledby="addSubAccountModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header bg-primary-subtle p-3">
          <h5 class="modal-title" id="addSubAccountModalLabel">Add New Subaccount</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="addsubAccountForm">
            @csrf
            <div class="row gy-4">
                <input type="hidden" id="main_branch_id" name="main_branch_id" value="{{ $subaccount->id }}">
                <input type="hidden" id="subbranch_id" name="subbranch_id" value="">
                <div class="col-md-6">
                    <div>
                        <label for="branch_name" class="form-label">Company Name</label>
                        <input type="text" name="branch_name" class="form-control" value="{{ old('branch_name') }}" placeholder="Enter Company Name" required>
                        @error('branch_name')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <!--end col-->
                <div class="col-md-6">
                    <div>
                        <label for="branch_email" class="form-label">Company Email</label>
                        <div class="form-icon">
                            <input type="email" name="branch_email" class="form-control form-control-icon" value="{{ old('branch_email') }}" placeholder="Company Email">
                            <i class="ri-mail-unread-line"></i>
                            @error('branch_email')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                        </div>
                    </div>
                </div>
                <!--end col-->
                <div class="col-md-6">
                    <div>
                        <label for="company_phone" class="form-label">Company Phone</label>
                        <div class="form-icon">
                            <input type="text" name="company_phone" class="form-control form-control-icon" value="{{ old('company_phone') }}" placeholder="Company Phone">
                            <i class="ri-smartphone-line"></i>
                            @error('company_phone')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                        </div>
                    </div>
                </div>
                <!--end col-->
                <div class="col-md-6">
                    <div>
                        <label for="sms_pricing" class="form-label">SMS Pricing</label>
                        <input type="number" name="sms_pricing" class="form-control" value="{{ old('sms_pricing', '1.0') }}" placeholder="Enter SMS Pricing" required>
                        @error('sms_pricing')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <!--end col-->
                <div class="col-md-6">
                    <div>
                        <label for="voice_pricing" class="form-label">Voice Pricing</label>
                        <input type="number" name="voice_pricing" class="form-control" value="{{ old('voice_pricing', '10.0') }}" placeholder="Enter Voice Pricing" required>
                        @error('voice_pricing')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <!--end col-->
                <div class="col-md-6">
                    <div>
                        <label for="balance" class="form-label">Balance</label>
                        <input type="number" name="balance" id="balance" class="form-control" value="{{ old('website', '10.0') }}" placeholder="Enter balance" required>
                        @error('balance')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <!--end col-->
                <div class="col-md-12">
                    <div>
                        <label for="balance" class="form-label">Website</label>
                        <input type="url" name="website" id="website" class="form-control" value="{{ old('website') }}" placeholder="Enter Website">
                        @error('website')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <!--end col-->
                <div class="col-md-12">
                    <div>
                        <label for="category" class="form-label">Category</label>
                        <select class="form-control select2" name="category_id" required>
                            <option value="">--Select--</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                        @error('category_id')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <!--end col-->
                <div class="col-md-12">
                    <div>
                        <label for="about" class="form-label">About</label>
                        <textarea class="form-control" name="about_1" rows="3">{{ old('about') }}</textarea>
                        @error('about')
                            <div class="text-danger">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
                <!--end col-->
                <button type="button" class="btn btn-primary" onclick="addOrUpdateSubAccount()">Save Subaccount</button>
                {{-- <button type="button" class="btn btn-primary" onclick="addSubAccount()">Add Subaccount</button> --}}
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>
<!-- Assign Shortcode Modal -->
<div class="modal fade flip" id="assignShortcodeModal" tabindex="-1" aria-labelledby="assignShortcodeModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-center">
        <div class="modal-content">
            <div class="modal-header bg-primary-subtle p-3">
                <h5 class="modal-title" id="assignShortcodeModalLabel">Assign Shortcodes</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div id="shortcodesList"></div> <!-- This will be filled by AJAX -->
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>

<!-- Top UP Modal -->
<div class="modal fade flip" id="topUpModal" tabindex="-1" aria-labelledby="topUpModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-md modal-dialog-center">
        <div class="modal-content">
            <div class="modal-header bg-primary-subtle p-3">
                <h5 class="modal-title" id="topUpModalLabel">Top Up {{ $subaccount->branch_name }}</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="col-md-12 mb-3">
                    <label for="available_units" class="form-label">Available Balance (KES)</label>
                    <input type="text" name="available_units" id="available_units" class="form-control" readonly placeholder="Fetching available balance...">
                    <div id="availableUnitsError" class="text-danger d-none">Unable to fetch available balance</div>
                </div>
                <div class="col-md-12">
                    <label for="units_to_topup" class="form-label">Top-Up Amount (KES)</label>
                    <input type="number" name="units_to_topup" id="units_to_topup" class="form-control" placeholder="Enter Amount (KES) to Top Up">
                    <div id="topupError" class="text-danger d-none">Top-up amount cannot exceed available balance.</div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" id="topUpSubmitBtn" class="btn btn-primary" onclick="topUpSubaccount()">
                    <span id="spinner" class="spinner-border spinner-border-sm d-none" role="status" aria-hidden="true"></span>
                    Top Up
                </button>
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
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
    <script src="{{ asset('build/libs/sweetalert2/sweetalert2.min.js') }}"></script>
    <script src="{{ asset('build/js/pages/profile-setting.init.js') }}"></script>
    <script src="{{ asset('build/js/pages/form-validation.init.js') }}"></script>
    <script src="{{ asset('build/js/pages/notifications.init.js') }}"></script>
    <script src="{{ asset('build/js/pages/select2.init.js') }}"></script>
    <script src="{{ asset('build/js/app.js') }}"></script>
    <script>
        // Fetch User on Intialize
        fetchUsers();
        fetchSenders();
        fetchPayments();

        // update account details
        document.getElementById('accountProfileForm').addEventListener('submit', function (e) {
            e.preventDefault();
            let formData = new FormData(this);
        
            axios.post("{{ route('updateAccountDetails', $subaccount->id) }}", formData)
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
                    showToast('Failed to update user status!', 5000, true, 'top', 'center', 'bg-danger', true);
                });
        });

    function showEditModal(user) {
        document.getElementById('userId').value = user.id;
        document.getElementById('editName').value = user.name;
        document.getElementById('editPhone').value = user.phone;
        document.getElementById('editEmail').value = user.email;
        document.getElementById('editPassword').value = '';
        new bootstrap.Modal(document.getElementById('editUserModal')).show();
    }

    function updateUser() {
        const id = document.getElementById('userId').value;
        const formData = new FormData(document.getElementById('editUserForm'));

        axios.post(`/update-user-details/${id}`, formData)
            .then(response => {
                fetchUsers();
                $('#editUserModal').modal('hide');
                showToast(response.data.message, 5000, true, 'top', 'center', 'bg-success', true);
            })
            .catch(error => {
                showToast("Something went wrong", 5000, true, 'top', 'center', 'bg-danger', true);
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
            })
            .catch(error => {
                if (error.response && error.response.data.errors) {
                    const errors = error.response.data.errors;

                    // General validation errors
                    let errorMessage = 'Please fix the following errors:<ul>';

                    // Loop through each error field and append to the errorMessage string
                    for (const field in errors) {
                        if (errors.hasOwnProperty(field)) {
                            errorMessage += `<li>${errors[field][0]}</li>`; // Take the first error message for each field
                        }
                    }

                    errorMessage += '</ul>';

                    // Display all errors in a SweetAlert
                    Swal.fire({
                        title: 'Error',
                        html: errorMessage, // Display errors as HTML
                        icon: 'error',
                        confirmButtonText: 'OK'
                    });
                } else {
                    showToast('An unexpected error occurred.', 5000, true, 'top', 'center', 'bg-danger', true);
                }
                console.error(error);
            });
    }

    // Event listener for deleting user
    $(document).on('click', '.delete-user', function (e) {
        e.preventDefault();
        let userId = $(this).data('id');
        let currentUserId = {{ Auth::id() }};
        if(currentUserId === userId){
            Swal.fire('Error!', 'Failed, you cannot delete yourself.', 'error');
            return;
        }
        console.log('Delete button clicked!');

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
                        Swal.fire('Error!', 'Failed to delete user.', 'error');
                    });
            }
        });
    });

    // Function to fetch and reload users
    function fetchUsers() {
        let subaccountId = {{ $branchId }};
        axios.get("{{ route('fetchUsers') }}?subbranch_id=" + subaccountId)
            .then(response => {
                document.getElementById('usersTableBody').innerHTML = response.data;
                // showToast('Users fetched successfully!', 5000, true, 'top', 'center', 'bg-success', true);
            })
            .catch(error => {
                showToast('Error fetching users!', 5000, true, 'top', 'center', 'bg-danger', true);
            });
    }

    // Function to fetch and reload senders
    function fetchSenders() {
        let subaccountId = {{ $branchId }};
        axios.get("{{ route('fetchSenders') }}?subbranch_id=" + subaccountId)
            .then(response => {
                document.getElementById('sendersTableBody').innerHTML = response.data;
            })
            .catch(error => {
                showToast('Error fetching senders!', 5000, true, 'top', 'center', 'bg-danger', true);
            });
    }

    // Function to fetch and reload subaccount payments
    function fetchPayments() {
        // showShimmerLoader();
        let subaccountId = {{ $branchId }};
        axios.get("{{ route('fetchSubaccountPayments') }}?subbranch_id=" + subaccountId)
            .then(response => {
                document.getElementById('transactionsTableBody').innerHTML = response.data;
                // hideShimmerLoader();
            })
            .catch(error => {
                // hideShimmerLoader();
                // showToast('Error fetching payments!', 5000, true, 'top', 'center', 'bg-danger', true);
            });
    }

    // Show the shimmer loader
    // function showShimmerLoader() {
    //     document.getElementById("shimmer").style.display = "block";
    // }

    // Hide the shimmer loader
    // function hideShimmerLoader() {
    //     document.getElementById("shimmer").style.display = "none";
    // }
    
    $(document).ready(function () {
        let availableUnits = 0;

        // Open top-up modal and fetch available units from the main account
        $('#topUpButton').on('click', function () {
            const subaccountId = {{ $branchId }};
            
            $.get('/settings/subaccount/' + subaccountId + '/available-units', function (response) {
                if (response.success) {
                    availableUnits = response.available_units;
                    $('#available_units').val(availableUnits); // Populate the available units
                    $('#availableUnitsError').addClass('d-none');
                } else {
                    $('#availableUnitsError').removeClass('d-none');
                }
                $('#topUpModal').modal('show'); // Show the modal
            }).fail(function () {
                $('#availableUnitsError').removeClass('d-none'); // Show error if fetching fails
            });
        });

        // Live validation for top-up units input
        $('#units_to_topup').on('input', function () {
            const unitsToTopUp = $(this).val();
            if (unitsToTopUp > availableUnits) {
                $('#topupError').removeClass('d-none'); // Show error if more units are entered
                $('#topUpSubmitBtn').prop('disabled', true); // Disable submit button
            } else {
                $('#topupError').addClass('d-none'); // Hide error
                $('#topUpSubmitBtn').prop('disabled', false); // Enable submit button
            }
        });

        // Function to handle the top-up logic
        window.topUpSubaccount = function () {
            var $spinner = $('#spinner');
            const unitsToTopUp = $('#units_to_topup').val();
            const subaccountId = {{ $branchId }};

            // Disable the button and show the spinner
            $('#topUpSubmitBtn').prop('disabled', true);
            $spinner.removeClass('d-none');

            if (unitsToTopUp > 0 && unitsToTopUp <= availableUnits) {
                $.post('/settings/subaccount/' + subaccountId + '/top-up', {
                    units: unitsToTopUp,
                    _token: $('meta[name="csrf-token"]').attr('content') // Laravel's CSRF token
                }).done(function (response) {
                    if (response.success) {
                        // Reset the button and hide the spinner
                        $('#topUpSubmitBtn').prop('disabled', false);
                        $spinner.addClass('d-none');

                        $('#topUpModal').modal('hide');
                        showToast(response.message, 5000, true, 'top', 'center', 'bg-success', true);
                        // Update the UI to reflect the new balance
                        $('.subaccount_balance_span').text(response.subaccount_balance);
                        fetchPayments();
                    } else {
                        // Reset the button and hide the spinner
                        $('#topUpSubmitBtn').prop('disabled', false);
                        $spinner.addClass('d-none');
                        showToast(response.message, 5000, true, 'top', 'center', 'bg-danger', true);
                    }
                }).fail(function (jqXHR) {
                    // Reset the button and hide the spinner
                    $('#topUpSubmitBtn').prop('disabled', false);
                    $spinner.addClass('d-none');

                    showToast('Failed to top up. Try again later.', 5000, true, 'top', 'center', 'bg-danger', true);
                });
            } else {
                // Reset the button and hide the spinner
                $('#topUpSubmitBtn').prop('disabled', false);
                $spinner.addClass('d-none');
                showToast('Invalid top-up amount.', 5000, true, 'top', 'center', 'bg-danger', true);
            }
        };

        // Open modal and load shortcodes for assignment
        $('#assignShortcodeButton').on('click', function () {
            const subaccountId = {{ $branchId }};

            $.get('/settings/subaccount/' + subaccountId + '/assign-shortcodes', function (data) {
                $('#shortcodesList').html(data); // Load the shortcodes into the modal
                $('#assignShortcodeModal').modal('show'); // Show the modal
            });
        });

        // Handle the switch toggle to assign/unassign shortcodes
        $(document).on('change', '.sender-toggle-assign', function () {
            const shortcodeId = $(this).data('id');
            const subaccountId = {{ $branchId }};
            const isChecked = $(this).is(':checked');

            $.post('/settings/subaccount/' + subaccountId + '/assign-shortcode', {
                shortcode_id: shortcodeId,
                assign: isChecked,
                _token: $('meta[name="csrf-token"]').attr('content') // For Laravel's CSRF protection
            }).done(function (response) {
                fetchSenders();
                showToast(response.message, 2000, true, 'top', 'center', 'bg-success', true);
            }).fail(function (jqXHR) {
                const response = jqXHR.responseJSON;
                const errorMessage = response && response.message ? response.message : 'An error occurred while assigning the shortcode.';
                showToast(errorMessage, 5000, true, 'top', 'center', 'bg-danger', true);
            });
        });

    });

    // Event listener for deleting user
    $(document).on('click', '.delete-sender', function (e) {
        e.preventDefault();
        let senderId = $(this).data('id');
        let senderName = $(this).data('sender');
        let subaccountId = {{ $branchId }};
        console.log('Delete button clicked!');

        Swal.fire({
            title: 'Are you sure?',
            html: `You want to remove <strong>${senderName}</strong>?`,
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#3085d6',
            cancelButtonColor: '#d33',
            confirmButtonText: 'Yes, remove it!'
        }).then((result) => {
            if (result.isConfirmed) {
                axios.post(`/remove-sender/${senderId}/subaccount/${subaccountId}`)
                    .then(response => {
                        fetchSenders();
                        Swal.fire('Deleted!', response.data.message, 'success');
                    })
                    .catch(error => {
                        Swal.fire('Error!', 'Failed to remove sender.', 'error');
                    });
            }
        });
    });
    </script>
@endsection
