<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Account;
use App\Models\Category;
use App\Services\SMSService;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use App\Models\AccountBillingSetting;
use Illuminate\Support\Facades\Validator;

class AccountController extends Controller
{
    protected $smsService;

    public function __construct(SMSService $smsService)
    {
        $this->smsService = $smsService;
    }

    public function index()
    {
        $accounts = Account::with(['users', 'category'])->get();
        return view('pages.accounts.index', compact('accounts'));
    }

    public function create()
    {
        $roles = Role::all();
        $categories = Category::all();

        // Get the last used numeric part of kra_pin_number
        $lastAccount = Account::where('kra_pin_number', 'LIKE', 'RTA%')
            ->orderByRaw("CAST(SUBSTRING(kra_pin_number, 4) AS UNSIGNED) DESC")
            ->first();

        // Extract and increment the last numeric part
        $lastNumber = $lastAccount ? (int)substr($lastAccount->kra_pin_number, 3) + 1 : 1001;
        $nextAccountNumber = 'RTA' . $lastNumber;

        return view('pages.accounts.create', compact('roles', 'categories', 'nextAccountNumber'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'branch_name'   => 'required|string|max:255',
            'company_email'  => 'required|email|unique:accounts,company_email|max:255',
            'kra_pin_number' => 'required|unique:accounts,kra_pin_number|max:255',
            'company_phone'  => 'required|string|unique:accounts,company_phone|max:15',
            'category_id'    => 'required|exists:categories,id',

            'sms_pricing'    => 'required|numeric|min:0',
            'voice_pricing'  => 'required|numeric|min:0',

            // correct field
            'opening_balance_kes' => 'required|numeric|min:0',

            'about'          => 'nullable|string',
            'website'        => 'nullable|string|max:255',
            'isActive'       => 'required|in:0,1',

            // user
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|max:255|unique:users,email',
            'phone'    => 'required|string|max:15|unique:users,phone',
            'role'     => 'required|string|in:admin,manager,customer',
            'password' => 'required|string|min:8',
            'sendEmail' => 'required|in:yes,no',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $branch = Account::create([
            'branch_name'   => $request->branch_name,
            'company_email'  => $request->company_email,
            'kra_pin_number' => $request->kra_pin_number,
            'company_phone'  => $request->company_phone,
            'category_id'    => $request->category_id,
            'sms_pricing'    => $request->sms_pricing,
            'voice_pricing'  => $request->voice_pricing,
            'balance_minor'  => (int) round(((float) $request->opening_balance_kes) * 100),

            'website'        => $request->website,
            'isActive'       => $request->isActive,
            'about'          => $request->about,
        ]);

        // create default billing settings for new account
        AccountBillingSetting::firstOrCreate(
            ['branch_id' => $branch->id],
            [
                'markup_percent' => (float) env('MARKUP_PERCENT', 15.000),
                'min_fee_minor' => (int) env('MIN_FEE_MINOR', 60),
                'fx_spread_bps' => (int) env('FX_SPREAD_BPS', 50),
                'monthly_platform_fee_minor' => (int) env('MONTHLY_PLATFORM_FEE_MINOR', 0),
            ]
        );

        $user = User::create([
            'name'       => $request->name,
            'email'      => $request->email,
            'phone'      => $request->phone,
            'role'       => $request->role,
            'password'   => Hash::make($request->password),
            'branch_id' => $branch->id,
        ]);

        $roleInstance = Role::where('name', $request->role)->first();
        $permissions  = $roleInstance ? $roleInstance->permissions()->pluck('name') : [];
        // $user->assignRole($roleInstance);
        // $user->syncPermissions($permissions);

        $user->assignRole($request->role);

        if ($request->sendEmail === 'yes') {
            $mergeData = [
                "password" => $request->password,
                "login_url_link" => "https://rebuetext.com/login",
                "login_url_text" => "Account Login",
                "name" => $request->branch_name,
                "team" => "RebueText",
                "product_name" => "RebueText",
                "username" => $request->email,
            ];

            $this->smsService->sendSMS(
                $request->company_phone,
                "Your account has been created succesfully, check your company email for login details."
            );

            sendZeptoMail(
                $request->company_email,
                '2d6f.10b4e7b927f81eea.k1.d18970f0-a845-11ef-8ab8-525400f92481.19350609d7f',
                $mergeData
            );
        }

        return redirect()->route('accounts.index')->with('success', 'Account and user created successfully');
    }

    public function edit($id)
    {
        $branch = Account::with(['users', 'billingSetting'])->findOrFail($id);
        $categories = Category::all();
        // return $branch;
        return view('pages.accounts.edit', compact('account', 'categories'));
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'branch_name'  => 'required|string|max:255',
            'company_email' => 'required|email|max:255',
            'company_phone' => 'required|string|max:15',
            'category_id'   => 'required|exists:categories,id',

            'sms_pricing'   => 'required|numeric|min:0',
            'voice_pricing' => 'required|numeric|min:0',

            'kra_pin_number' => 'nullable|string|max:255',
            'about'          => 'nullable|string',
            'website'        => 'nullable|string|max:255',
            'isActive'       => 'required|in:0,1',

            // billing fields OPTIONAL (so normal account updates don’t fail)
            'markup_percent'        => 'nullable|numeric|min:0|max:500',
            'min_fee'              => 'nullable|numeric|min:0',
            'fx_spread_bps'         => 'nullable|integer|min:0|max:5000',
            'monthly_platform_fee'  => 'nullable|numeric|min:0',
        ]);

        if ($validator->fails()) {
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $branch = Account::findOrFail($id);

        // Ensure kra_pin_number exists
        $accountNumber = $request->kra_pin_number;
        if (!$accountNumber) {
            $lastAccount = Account::where('kra_pin_number', 'LIKE', 'RTA%')
                ->orderByRaw("CAST(SUBSTRING(kra_pin_number, 4) AS UNSIGNED) DESC")
                ->first();

            $lastNumber = $lastAccount ? (int) substr($lastAccount->kra_pin_number, 3) + 1 : 1001;
            $accountNumber = 'RTA' . $lastNumber;
        }

        $branch->update([
            'branch_name'   => $request->branch_name,
            'company_email'  => $request->company_email,
            'company_phone'  => $request->company_phone,
            'category_id'    => $request->category_id,
            'sms_pricing'    => $request->sms_pricing,
            'voice_pricing'  => $request->voice_pricing,
            'kra_pin_number' => $accountNumber,
            'website'        => $request->website,
            'about'          => $request->about,
            'isActive'       => $request->isActive,
        ]);

        // update billing ONLY if all billing fields are present
        $hasBillingInput =
            $request->filled('markup_percent') ||
            $request->filled('min_fee') ||
            $request->filled('fx_spread_bps') ||
            $request->filled('monthly_platform_fee');

        if ($hasBillingInput) {
            $setting = AccountBillingSetting::firstOrCreate(['branch_id' => $branch->id]);

            $setting->update([
                'markup_percent' => round((float) ($request->markup_percent ?? 0), 3),
                'min_fee_minor' => (int) round(((float) ($request->min_fee ?? 0)) * 100),
                'fx_spread_bps' => (int) ($request->fx_spread_bps ?? 0),
                'monthly_platform_fee_minor' => (int) round(((float) ($request->monthly_platform_fee ?? 0)) * 100),
            ]);
        }

        return redirect()->route('accounts.index')->with('success', 'Account updated successfully');
    }

    public function editRoles(User $user)
    {
        $roles = Role::all();
        $userRoles = $user->roles->pluck('name')->toArray();
        return view('pages.accounts.edit', compact('user', 'roles', 'userRoles'));
    }

    public function updateRoles(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255',
            'role' => 'required|exists:roles,id'
        ]);

        $user = User::findOrFail($id);
        $user->name = $request->name;
        $user->email = $request->email;
        $user->save();

        $role = Role::findOrFail($request->role);

        // Prevent manager from assigning admin role
        if (Auth::user()->hasRole('manager') && $role->name == 'admin') {
            return redirect()->back()->with('error', 'You are not authorized to assign the admin role.');
        }

        // Sync roles to ensure only one role is assigned
        $user->roles()->sync([$request->role]);

        return redirect()->back()->with('success', 'User roles updated successfully.');
    }

    function destroy($id)
    {
        $branch = Account::findOrFail($id);
        $branch->delete();
        return redirect()->route('accounts.index')->with('success', 'Account deleted deleted successfully');
    }

    public function userStore(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users',
                'phone' => 'required|string|max:15',
                'branch_id' => 'required|exists:accounts,id',
                'role' => 'required|string',
                'password' => 'required|min:8',
            ]);

            $user = User::create([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'branch_id' => $validated['branch_id'],
                'role' => $validated['role'],
                'password' => bcrypt($validated['password']),
            ]);

            return response()->json(['status' => true, 'message' => 'User created successfully!', 'user' => $user]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }

    public function userUpdate(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);

            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'email' => 'required|email|unique:users,email,' . $user->id,
                'phone' => 'required|string|max:15',
                'role' => 'required|string',
                'password' => 'nullable|min:8',
            ]);

            $user->update([
                'name' => $validated['name'],
                'email' => $validated['email'],
                'phone' => $validated['phone'],
                'role' => $validated['role'],
                'password' => $validated['password'] ? bcrypt($validated['password']) : $user->password,
            ]);

            return response()->json(['status' => true, 'message' => 'User updated successfully!', 'user' => $user]);
        } catch (\Exception $e) {
            return response()->json(['status' => false, 'message' => $e->getMessage()], 500);
        }
    }


    public function userDestroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['status' => true, 'message' => 'User deleted successfully!']);
    }
}
