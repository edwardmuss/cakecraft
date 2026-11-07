<?php

namespace App\Http\Controllers;

use App\Exports\CustomersExport;
use App\Models\Customer;
use App\Services\SMSService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Maatwebsite\Excel\Facades\Excel;

class CustomerController extends Controller
{
    public function index(Request $request)
    {
        $query = Customer::where('branch_id', Auth::user()->branch_id);

        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                $q->where('name', 'like', "%{$searchTerm}%")
                    ->orWhere('organization_name', 'like', "%{$searchTerm}%")
                    ->orWhere('phone', 'like', "%{$searchTerm}%")
                    ->orWhere('email', 'like', "%{$searchTerm}%");
            });
        }

        // appends() keeps the search string in the URL when clicking next page
        $customers = $query->orderBy('organization_name', 'asc')
            ->paginate(50)
            ->appends($request->query());

        return view('customers.index', compact('customers'));
    }

    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'billing_address' => 'nullable|string',
            'delivery_location' => 'nullable|string',
            'organization_name' => 'nullable|string|max:255',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $customer = Customer::create([
            'branch_id' => Auth::user()->branch_id,
            'name' => $request->name,
            'phone' => $request->phone,
            'email' => $request->email,
            'billing_address' => $request->billing_address,
            'delivery_location' => $request->delivery_location,
            'organization_name' => $request->organization_name,
        ]);

        if ($customer) {
            (new SMSService())->sendSMS($customer->phone, $this->welcomeMessage($customer));
        }

        return response()->json(['success' => 'Customer created successfully!', 'customer' => $customer]);
    }

    public function edit($id)
    {
        $customer = Customer::where('branch_id', Auth::user()->branch_id)->findOrFail($id);

        return response()->json($customer);
    }

    public function update(Request $request, $id)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'email' => 'nullable|email|max:255',
            'organization_name' => 'nullable|string|max:255',
            'billing_address' => 'nullable|string',
            'delivery_location' => 'nullable|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $customer = Customer::where('branch_id', Auth::user()->branch_id)->findOrFail($id);
        $customer->update($request->only('name', 'phone', 'email', 'billing_address', 'delivery_location', 'organization_name'));

        return response()->json(['success' => 'Customer updated successfully!', 'customer' => $customer]);
    }

    public function destroy($id)
    {
        $customer = Customer::where('branch_id', Auth::user()->branch_id)->findOrFail($id);
        $customer->delete();

        return response()->json(['success' => 'Customer deleted successfully!']);
    }

    public function export()
    {
        $branchId = Auth::user()->branch_id;
        $timestamp = now()->format('Y_m_d_His');

        return Excel::download(new CustomersExport($branchId), "customers_{$timestamp}.xlsx");
    }

    public function downloadTemplate()
    {
        $headers = [
            'Cache-Control'       => 'must-revalidate, post-check=0, pre-check=0',
            'Content-type'        => 'text/csv',
            'Content-Disposition' => 'attachment; filename=customers_template.csv',
            'Expires'             => '0',
            'Pragma'              => 'public'
        ];

        // Define the expected columns
        $columns = ['NAME', 'PHONE', 'ORGANIZATION', 'EMAIL', 'BILLING_ADDRESS', 'DELIVERY_LOCATION'];

        $callback = function () use ($columns) {
            $file = fopen('php://output', 'w');
            fputcsv($file, $columns);
            // Add a couple of helpful sample rows
            fputcsv($file, ['John Doe', '0712345678', 'Grace Ministry', 'john@example.com', 'Nairobi CBD', 'Nairobi CBD']);
            fputcsv($file, ['PCEA Kikuyu', '0722000000', '', '', 'Kikuyu', 'Kikuyu Town']);
            fclose($file);
        };

        return response()->stream($callback, 200, $headers);
    }

    public function import(Request $request)
    {
        $request->validate([
            'file' => 'required|mimes:csv,txt,xlsx,xls|max:5120',
        ]);

        // Convert the uploaded file into an array
        $data = Excel::toArray(new \stdClass, $request->file('file'));
        $rows = $data[0]; // Get the first sheet

        $branchId = Auth::user()->branch_id;
        $importedCount = 0;
        $skippedCount = 0;
        $seenPhones = [];

        // Get all existing formatted phones for this branch to prevent DB duplicates
        $existingPhones = Customer::where('branch_id', $branchId)->pluck('phone')->toArray();
        $orgKeywords = ['ltd', 'limited', 'company', 'school', 'academy', 'hotel', 'restaurant', 'foundation', 'enterprises', 'group'];

        foreach ($rows as $key => $row) {
            if ($key === 0) continue; // Skip the header row

            $rawName     = trim($row[0] ?? '');
            $rawPhone    = trim($row[1] ?? '');
            $rawOrg      = trim($row[2] ?? '');
            $rawEmail    = trim($row[3] ?? '');
            $rawBilling  = trim($row[4] ?? '');
            $rawDelivery = trim($row[5] ?? '');

            if (empty($rawName) && empty($rawPhone)) continue;

            // Format the phone number using your helper!
            $cleanPhone = formatPhoneNumber($rawPhone);

            // Skip if no phone, if it's duplicated in this file, or already in the database
            if (empty($cleanPhone) || in_array($cleanPhone, $seenPhones) || in_array($cleanPhone, $existingPhones)) {
                $skippedCount++;
                continue;
            }

            $seenPhones[] = $cleanPhone;
            $name = $rawName;
            $organization = $rawOrg;

            // Smart Organization Detection
            if (empty($organization)) {
                foreach ($orgKeywords as $keyword) {
                    if (stripos($rawName, $keyword) !== false) {
                        $organization = $rawName;
                        break;
                    }
                }
            }

            $customer = Customer::create([
                'branch_id'         => $branchId,
                'name'              => $name,
                'organization_name' => $organization,
                'phone'             => $cleanPhone,
                'email'             => $rawEmail ?: null,
                'billing_address'   => $rawBilling ?: null,
                'delivery_location' => $rawDelivery ?: null,
            ]);

            if ($customer) {
                (new SMSService())->sendSMS($customer->phone, $this->welcomeMessage($customer));
            }

            $importedCount++;
        }

        return response()->json([
            'success' => "Import complete! Successfully added {$importedCount} customers. Skipped {$skippedCount} duplicates/invalid rows."
        ]);
    }

    private function welcomeMessage(Customer $customer): string
    {
        $business = \App\Support\Brand::name();
        $phone = \App\Support\Brand::get('phone');

        return "Hi {$customer->name},\n\nWelcome to {$business}! Your details have been saved.\n\nOrdering a cake is easy: call or WhatsApp {$phone}.\n\nWe look forward to baking for your celebrations.";
    }
}
