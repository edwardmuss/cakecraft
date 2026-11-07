<?php

namespace App\Http\Controllers;

use App\Models\Account;
use App\Models\Billing;
use App\Models\Branch;
use App\Models\Category;
use App\Models\Payment;
use App\Models\Shortcode;
use App\Models\User;
use App\Services\ActivityLogger;
use App\Services\Billing\AccountLedgerService;
use App\Services\SMSService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;

class SettingsController extends Controller
{
    protected $smsService;

    public function __construct(SMSService $smsService)
    {
        $this->smsService = $smsService;
    }

    public function index()
    {
        $user = Auth::user();
        $branchId = $user->branch_id;
        $branch = Branch::with(['users', 'devices'])->findOrFail($branchId);

        // Fetch available roles based on user hierarchy for the Add/Edit modals
        $roleQuery = Role::query();
        if ($user->hasRole('super-admin')) {
            $roleQuery->whereNull('branch_id')->orWhereNotNull('branch_id');
        } elseif ($user->hasRole('manager')) {
            $roleQuery->where(function ($q) use ($branchId) {
                $q->whereIn('name', ['manager', 'customer'])
                    ->orWhere('branch_id', $branchId);
            });
        } else {
            $roleQuery->where(function ($q) use ($branchId) {
                $q->where('name', 'customer')
                    ->orWhere('branch_id', $branchId);
            });
        }
        $roles = $roleQuery->get();

        // Existing data fetching
        $branchSettings = $branch->settings()->pluck('value', 'key')->toArray();
        $branch_users = User::where('branch_id', $branchId)->with('roles')->get(); // Eager load roles
        $senders = $branch->shortcodes;
        $latestDevices = $branch->devices()->latest('last_used_at')->take(10)->get();

        // return $branch_users;

        return view('settings.index', compact(
            'branch',
            'branchSettings',
            'branch_users',
            'latestDevices',
            'roles' // Pass the filtered roles to the view
        ));
    }

    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Get the authenticated user branch
        $branch = Auth::user()->branch;

        // Handle file upload
        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');
            $fileName = time() . '.' . $file->getClientOriginalExtension();

            // Move the file to the desired directory
            $file->move(public_path('images/avatars'), $fileName);

            // Update account avatar path in the database
            $branch->company_logo = $fileName;
            $branch->save();

            return response()->json([
                'success' => true,
                'image_url' => asset('images/avatars/' . $branch->company_logo)
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Profile Photo upload failed']);
    }

    public function updateCover(Request $request)
    {
        $request->validate([
            'branch_cover_image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);

        // Get the authenticated user branch
        $branch = Auth::user()->branch;

        // Handle file upload
        if ($request->hasFile('branch_cover_image')) {
            $file = $request->file('branch_cover_image');
            $fileName = time() . '.' . $file->getClientOriginalExtension();

            // Move the file to the desired directory
            $file->move(public_path('images/covers'), $fileName);

            // Update account avatar path in the database
            $branch->branch_cover_image = $fileName;
            $branch->save();

            return response()->json([
                'success' => true,
                'image_url' => asset('images/covers/' . $branch->branch_cover_image)
            ]);
        }

        return response()->json(['success' => false, 'message' => 'Cover image upload failed']);
    }

    // Function to return partial view for users
    public function fetchUsers(Request $request)
    {
        if (isset($request->subbranch_id)) {
            $branchId = $request->subbranch_id;
        } else {
            $branchId = Auth::user()->branch_id;
        }

        $users = User::where('branch_id', $branchId)->with('roles')->orderBy('id', 'DESC')->get();

        return view('settings.partials.users_table', compact('users'))->render();
    }

    public function update(Request $request)
    {
        $request->validate([
            'settings' => 'required|array',
            'settings.*' => 'string',
        ]);

        // return $request->all();

        $branchId = $request->branch_id;
        $branch = Branch::findOrFail($branchId);

        $settingsData = $request->input('settings');

        // return $settingsData;

        foreach ($settingsData as $key => $value) {
            if ($key == 'otp_method' && $value == 'authenticator_app') {
                $request->session()->put('showQr', true);
                return redirect()->route('otp.auth.verify');
            }
            $branch->setSetting($key, $value);
        }

        return redirect()->back()->with('success', 'Settings updated successfully.');
    }

    public function updateAccountDetails(Request $request, $id)
    {
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'branch_name' => 'required|string|max:255',
            'branch_email' => 'required|email|max:255',
            'company_phone' => 'required|string|max:15',
            'about' => 'nullable|string',
            'website' => 'nullable|url|string',
        ]);

        // Return validation errors as JSON
        if ($validator->fails()) {
            return response()->json([
                'isSuccess' => false,
                'Message' => 'Validation error!',
                'errors' => $validator->errors()
            ], 422); // 422 status for validation errors
        }

        // Update the account
        $branch = Branch::findOrFail($id);
        $branch->update([
            'branch_name' => $request->branch_name,
            'branch_email' => $request->branch_email,
            'company_phone' => $request->company_phone,
            'category_id' => $request->category_id,
            'website' => $request->website,
            'about' => $request->about,
        ]);

        return response()->json([
            'isSuccess' => true,
            'Message' => "Account details updated successfully!"
        ], 200);
    }

    public function updateUserStatus(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->isActive = $request->isActive;
        $user->save();
        if ($user->isActive) {
            $message = "User: $user->name has been activated successfully";
        } else {
            $message = "User: $user->name has been de-activated successfully";
        }

        return response()->json(['message' => $message]);
    }

    public function updateUserDetails(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $user->name = $request->name;
        $user->phone = $request->phone;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        // Sync multiple roles (Spatie handles the pivot table automatically)
        if ($request->has('roles')) {
            $user->syncRoles($request->roles);
        }

        return response()->json(['message' => 'User updated successfully.']);
    }

    public function addUser(Request $request)
    {
        // Validation happens outside the transaction
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:15',
            'branch_id' => 'required|exists:branches,id',
            'password' => 'required|string|min:8',
            'roles' => 'required|array' // Ensure roles are sent as an array
        ]);

        // 2. Start the Database Transaction
        DB::beginTransaction();

        try {
            // Create the User
            $user = User::create([
                'name' => $validatedData['name'],
                'email' => $validatedData['email'],
                'phone' => $validatedData['phone'],
                'branch_id' => $validatedData['branch_id'],
                'isActive' => true,
                'role' => $validatedData['roles'][0],
                'password' => Hash::make($validatedData['password']),
                'registration_verified_at' => now(),
            ]);

            // Assign multiple roles
            $user->assignRole($validatedData['roles']);

            // If everything went perfectly, commit the changes to the database
            DB::commit();

            return response()->json(['message' => 'User added successfully.'], 200);
        } catch (\Exception $e) {
            // 6. If anything failed, rollback the database to its previous state
            DB::rollBack();

            // Log the actual error for debugging
            Log::error('User Creation Failed: ' . $e->getMessage());

            // Return a safe error message to the frontend
            return response()->json([
                'errors' => [
                    'general' => ['An error occurred while creating the user. Please try again.']
                ]
            ], 500);
        }
    }

    public function deleteUser($id)
    {
        // Prevent the user from deleting themselves
        if (Auth::id() == $id) {
            return response()->json([
                'message' => 'Action Denied: You cannot delete your own account.'
            ], 403); // 403 Forbidden
        }

        // Find and delete the user
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'User deleted successfully.']);
    }

    public function updatePassword(Request $request, $id)
    {
        $request->validate([
            'current_password' => ['required', 'string'],
            'password' => ['required', 'string', 'min:6', 'confirmed'],
        ], [
            // Custom error messages
            'password.confirmed' => 'New password and confirm password do not match.',
            'current_password.required' => 'Please enter your current password.',
            'password.required' => 'Please enter a new password.',
            'password.min' => 'The new password must be at least 6 characters long.'
        ]);

        if (!(Hash::check($request->get('current_password'), Auth::user()->password))) {
            return response()->json([
                'isSuccess' => false,
                'Message' => "Your current password does not match the one we have on record."
            ], 200); // Status code
        } else {
            $user = User::find($id);
            $user->password = Hash::make($request->get('password'));
            $user->update();
            if ($user) {
                Session::flash('message', 'Password updated successfully!');
                Session::flash('alert-class', 'alert-success');
                return response()->json([
                    'isSuccess' => true,
                    'Message' => "Password updated successfully!"
                ], 200); // Status code here
            } else {
                Session::flash('message', 'Something went wrong!');
                Session::flash('alert-class', 'alert-danger');
                return response()->json([
                    'isSuccess' => true,
                    'Message' => "Something went wrong!"
                ], 200); // Status code here
            }
        }
    }

    public function addsubAccount(Request $request)
    {
        $validatedData = $request->validate([
            'branch_name' => 'required|string|max:255',
            'company_email' => 'required|email|unique:accounts,company_email|max:255',
            'company_phone' => 'required|string|unique:accounts,company_phone|max:15',
            'category_id' => 'required|exists:categories,id',
            'sms_pricing' => 'required|numeric',
            'voice_pricing' => 'required|numeric',
            'balance' => 'required|numeric',
            'website' => 'nullable|string',
        ]);
        $validatedData['about'] = $request->about_1;
        $validatedData['parent_id'] = $request->main_branch_id;
        $validatedData['isActive'] = true;
        $branch = Account::create($validatedData);

        if ($branch) {
            return response()->json([
                'isSuccess' => true,
                'message' => "Subaccount added successfully!"
            ], 200);
        } else {
            return response()->json([
                'isSuccess' => false,
                'message' => "something went wrong!"
            ], 422);
        }
    }
    function updatesubAccount(Request $request, $id)
    {
        // return $request->all();
        // Validate the request data
        $validator = Validator::make($request->all(), [
            'branch_name' => 'required|string|max:255',
            'company_email' => 'required|email|max:255|unique:accounts,company_email,' . $id,
            'company_phone' => 'required|string|max:15|unique:accounts,company_phone,' . $id,
            'category_id' => 'required|exists:categories,id',
            'about_1' => 'nullable|string',
            'website' => 'nullable|url|string',
        ]);

        // Return validation errors as JSON
        if ($validator->fails()) {
            return response()->json([
                'isSuccess' => false,
                'message' => 'Validation error!',
                'errors' => $validator->errors()
            ], 422); // 422 status for validation errors
        }

        // Update the account
        $branch = Account::findOrFail($id);
        $branch->update([
            'branch_name' => $request->branch_name,
            'company_email' => $request->company_email,
            'company_phone' => $request->company_phone,
            'sms_pricing' => $request->sms_pricing,
            'voice_pricing' => $request->voice_pricing,
            'balance' => $request->balance,
            'category_id' => $request->category_id,
            'website' => $request->website,
            'about' => $request->about_1,
        ]);

        return response()->json([
            'isSuccess' => true,
            'message' => "Subaccount details updated successfully!"
        ], 200);
    }
    public function updateSubAccountStatus(Request $request, $id)
    {
        $subaccount = Account::findOrFail($id);
        $subaccount->isActive = $request->isActive;
        $subaccount->save();
        if ($subaccount->isActive) {
            $message = "subaccount: $subaccount->branch_name has been activated successfully";
        } else {
            $message = "subaccount: $subaccount->branch_name has been de-activated successfully";
        }

        return response()->json(['message' => $message]);
    }

    public function deleteSubaccount($id)
    {
        $user = Account::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'Account deleted successfully.']);
    }

    public function subaccountIndex($branchId)
    {
        $main_branch_id = Auth::user()->branch_id;
        $main_account = Account::findOrFail($main_branch_id);

        $shortcodes = $main_account->shortcodes()
            ->orderByRaw('IF(`account_shortcodes`.`default` = true, 0, 1)')
            ->get();

        $subaccount = Account::with('users')->findOrFail($branchId);

        // Get subaccount's assigned shortcodes
        $assignedShortcodes = $subaccount->shortcodes()->pluck('shortcode_id')->toArray();

        $accountSettings = $subaccount->settings()->pluck('value', 'key')->toArray();
        $categories = Category::all();
        return view('pages.settings.subaccounts', compact('subaccount', 'accountSettings', 'categories', 'shortcodes', 'assignedShortcodes', 'accountId'));
    }

    // Function to return partial view for users
    public function fetchSenders(Request $request)
    {
        $branchId = $request->subbranch_id;

        $branch = Account::findOrFail($branchId);
        $senders = $branch->shortcodes; // Retrieve all associated shortcodes
        // return $shortcodes;
        // $branch->shortcodes()->attach($shortcode->id);

        return view('pages.settings.partials.senders_table', compact('senders'))->render();
    }

    public function getShortcodesForAssignment($subaccountId)
    {
        // Get parent account's shortcodes
        $main_branch_id = Auth::user()->branch_id;
        $main_account = Account::findOrFail($main_branch_id);

        $shortcodes = $main_account->shortcodes()
            ->orderByRaw('IF(`account_shortcodes`.`default` = true, 0, 1)')
            ->get();

        // Get subaccount's assigned shortcodes
        $subaccount = Account::findOrFail($subaccountId);
        $assignedShortcodes = $subaccount->shortcodes()->pluck('shortcode_id')->toArray();

        return view('pages.settings.partials.assign-shortcodes', compact('shortcodes', 'assignedShortcodes', 'subaccount'));
    }

    public function assignShortcode(Request $request, $subaccountId)
    {
        $subaccount = Account::findOrFail($subaccountId);
        $shortcodeId = $request->input('shortcode_id');
        $assign = $request->input('assign'); // Boolean value: true to assign, false to unassign
        $assign = filter_var($assign, FILTER_VALIDATE_BOOLEAN);

        if ($assign) {
            // Check if the shortcode is already assigned to the account
            $alreadyAssigned = $subaccount->shortcodes()->where('shortcode_id', $shortcodeId)->exists();

            if (!$alreadyAssigned) {
                $subaccount->shortcodes()->attach($shortcodeId);
                return response()->json(['success' => true, 'message' => 'Shortcode assigned successfully']);
            } else {
                return response()->json(['success' => false, 'message' => 'Shortcode is already assigned to this account.'], 400);
            }
        } else {
            // Detach the shortcode
            $alreadyAssigned = $subaccount->shortcodes()->where('shortcode_id', $shortcodeId)->exists();
            if ($alreadyAssigned) {
                $subaccount->shortcodes()->detach($shortcodeId);
                return response()->json(['success' => true, 'message' => 'Shortcode detached successfully']);
            } else {
                return response()->json(['success' => false, 'message' => 'Shortcode is not assigned to this account.'], 400);
            }
        }
    }

    public function removeSender($shortcodeId, $subaccountId)
    {
        $subaccount = Account::findOrFail($subaccountId);
        $subaccount->shortcodes()->detach($shortcodeId);

        return response()->json(['message' => 'Sender removed successfully.']);
    }

    // Function to return partial view for transactions
    public function fetchSubaccountPayments(Request $request)
    {
        $branchId = $request->subbranch_id;

        $branch = Account::findOrFail($branchId);
        $payments = $branch->payments()->orderBy('id', 'desc')->limit(50)->get();

        return view('pages.settings.partials.transactions_table', compact('payments'))->render();
    }

    public function getAvailableUnits($subaccountId)
    {
        $main_branch_id = Auth::user()->branch_id;
        $main_account = Account::findOrFail($main_branch_id);
        $availableUnits = $main_account->balance;

        if ($availableUnits >= 0) {
            return response()->json(['success' => true, 'available_units' => $availableUnits]);
        }

        return response()->json(['success' => false, 'message' => 'Unable to fetch available units']);
    }

    public function topUpSubaccount(Request $request, $subaccountId)
    {
        $request->validate([
            'amount' => 'required|numeric|min:1',
        ]);

        $main_branch_id = Auth::user()->branch_id;
        $mainAccount = Account::findOrFail($main_branch_id);
        $subAccount = Account::findOrFail($subaccountId);

        $amount = $request->input('amount');
        $amountMinor = $amount * 100; // Convert to minor units (cents)

        // Check if main account has sufficient balance
        if ($mainAccount->balance_minor < $amountMinor) {
            return response()->json(['success' => false, 'message' => 'Insufficient funds in the main account.']);
        }

        try {
            return DB::transaction(function () use ($mainAccount, $subAccount, $amount, $amountMinor) {
                $transactionRef = 'TXN-' . strtoupper(uniqid());

                // // Deduct from Main Account
                // $mainAccount->decrement('balance_minor', $amountMinor);

                // // Add to Subaccount
                // $subAccount->increment('balance_minor', $amountMinor);

                // Log Ledger Entry for Main Account (Debit)
                app(AccountLedgerService::class)->apply(
                    accountId: $mainAccount->id,
                    createdBy: Auth::user()->id,
                    amountMinor: -$amountMinor, // Negative because it's a deduction
                    currency: $mainAccount->currency ?? 'KES',
                    source: 'transfer',
                    eventType: 'subaccount_transfer',
                    idempotencyKey: $transactionRef . '_main',
                    reference: $transactionRef,
                    description: "Transfer to {$subAccount->branch_name}. REF: {$transactionRef}",
                    meta: ['recipient_id' => $subAccount->id]
                );

                // Log Ledger Entry for Subaccount (Credit)
                app(AccountLedgerService::class)->apply(
                    accountId: $subAccount->id,
                    createdBy: Auth::user()->id,
                    amountMinor: $amountMinor, // Positive because it's an addition
                    currency: $subAccount->currency ?? 'KES',
                    source: 'transfer',
                    eventType: 'main_account_topup',
                    idempotencyKey: $transactionRef . '_' . $subAccount->id,
                    reference: $transactionRef,
                    description: "Received transfer from {$mainAccount->branch_name}. REF: {$transactionRef}",
                    meta: ['sender_id' => $mainAccount->id]
                );

                // Activity Logging
                ActivityLogger::log('Subaccount Top Up', Auth::user()->name . " transferred KES $amount to {$subAccount->branch_name} REF: $transactionRef");

                // Send Notifications
                $this->smsService->sendSMS($mainAccount->company_phone, "KES $amount transferred to $subAccount->branch_name. REF: $transactionRef. New Balance: KES " . ($mainAccount->balance_minor / 100));
                $this->smsService->sendSMS($subAccount->company_phone, "Success! Your account has been topped up with KES $amount by $mainAccount->branch_name. REF: $transactionRef");

                return response()->json([
                    'success' => true,
                    'message' => 'Funds successfully transferred to subaccount.',
                    'mainaccount_balance' => $mainAccount->balance_minor / 100,
                    'subaccount_balance' => $subAccount->balance_minor / 100
                ]);
            });
        } catch (\Exception $e) {
            Log::error("Transfer failed: " . $e->getMessage());
            return response()->json(['success' => false, 'message' => 'Transaction failed. Please try again.']);
        }
    }
}
