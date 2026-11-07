<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Customer;
use App\Services\SMSService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::where('branch_id', $request->user()->branch_id);

        if ($request->has('search') && $request->search != '') {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                    ->orWhere('organization_name', 'like', "%{$search}%")
                    ->orWhere('phone', 'like', "%{$search}%")
                    ->orWhere('email', 'like', "%{$search}%");
            });
        }

        $customers = $query->orderBy('organization_name', 'asc')->paginate(50);

        return response()->json($customers);
    }

    public function store(Request $request)
    {
        if (!$request->user()->can('create customers')) {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'billing_address' => 'nullable|string',
            'delivery_location' => 'nullable|string',
            'organization_name' => 'required|string',
            'tray_preference' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $customer = Customer::create([
            'branch_id' => $request->user()->branch_id,
            'name' => $request->name,
            'phone' => formatPhoneNumber($request->phone), // Ensure your helper is available
            'email' => $request->email,
            'billing_address' => $request->billing_address,
            'delivery_location' => $request->delivery_location,
            'organization_name' => $request->organization_name,
            'tray_preference' => $request->tray_preference ?? 'none',
        ]);

        if ($customer) {
            try {
                $smsService = new SMSService();
                $smsService->sendSMS($customer->phone, "Hi {$customer->organization_name},\n\nWelcome to Pre-Packed Holy Communion Elements family.\nYour details have been successfully added to our system.\n\nOrdering is now simple and seamless! Call/WhatsApp 0799 988 371 \nWe're honored to serve you.");
            } catch (\Exception $e) {
                // Log SMS failure but still return success for customer creation
                \Illuminate\Support\Facades\Log::error("Failed to send welcome SMS: " . $e->getMessage());
            }
        }

        return response()->json([
            'success' => true,
            'message' => 'Customer created successfully!',
            'data' => $customer
        ]);
    }

    public function show(Request $request, $id)
    {
        $customer = Customer::where('branch_id', $request->user()->branch_id)->findOrFail($id);
        return response()->json($customer);
    }

    public function update(Request $request, $id)
    {
        if (!$request->user()->can('update customers')) {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }

        $validator = Validator::make($request->all(), [
            'name' => 'nullable|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'organization_name' => 'required|string|max:255',
            'billing_address' => 'nullable|string',
            'delivery_location' => 'nullable|string',
            'tray_preference' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $customer = Customer::where('branch_id', $request->user()->branch_id)->findOrFail($id);

        $data = $request->only('name', 'email', 'billing_address', 'delivery_location', 'organization_name', 'tray_preference');
        $data['phone'] = formatPhoneNumber($request->phone);

        $customer->update($data);

        return response()->json([
            'success' => true,
            'message' => 'Customer updated successfully!',
            'data' => $customer
        ]);
    }

    public function destroy(Request $request, $id)
    {
        if (!$request->user()->can('delete customers')) {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }

        $customer = Customer::where('branch_id', $request->user()->branch_id)->findOrFail($id);

        // Crucial check to prevent orphan invoices
        if ($customer->invoices()->exists()) {
            return response()->json([
                'success' => false,
                'message' => 'Action Denied: Cannot delete a customer who has existing invoices.'
            ], 400);
        }

        $customer->delete();

        return response()->json([
            'success' => true,
            'message' => 'Customer deleted successfully!'
        ]);
    }
}
