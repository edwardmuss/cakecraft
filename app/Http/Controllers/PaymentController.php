<?php

namespace App\Http\Controllers;

use App\Events\PaymentReceived;
use App\Exports\LedgerExport;
use App\Exports\PaymentsExport;
use App\Helpers\MpesaHelper;
use App\Mail\PaymentInvoiceMail;
use App\Models\Account;
use App\Models\AccountLedgerEntry;
use App\Models\Billing;
use App\Models\Invoice;
use App\Models\KcbTransaction;
use App\Models\Payment;
use App\Models\StkPush;
use App\Services\ActivityLogger;
use App\Services\CyberSourcePaymentService;
use App\Services\MsisdnDecoderService;
use App\Services\Payments\Adapters\KcbIpnAdapter;
use App\Services\Payments\Adapters\MpesaC2BAdapter;
use App\Services\Payments\Adapters\PaystackAdapter;
use App\Services\Payments\PaymentProcessingService;
use App\Services\SMSService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;
use Maatwebsite\Excel\Facades\Excel;
use Paystack;
use PDF;

class PaymentController extends Controller
{
    protected $smsService;

    protected $cyberSourcePaymentService;

    public function __construct(SMSService $smsService, CyberSourcePaymentService $cyberSourcePaymentService)
    {
        $this->smsService = $smsService;
        $this->cyberSourcePaymentService = $cyberSourcePaymentService;
    }

    public function index(Request $request)
    {
        $branchId = Auth::user()->branch_id;

        // Default to last_30_days if no filter is set in the URL
        $filter = $request->get('filter', 'last_30_days');

        $query = Payment::with(['invoice.customer'])->where('branch_id', $branchId);

        // --- 1. TEXT SEARCH (Ref, Invoice #, Customer Details) ---
        if ($request->filled('search')) {
            $searchTerm = $request->search;
            $query->where(function ($q) use ($searchTerm) {
                // Search Payment Reference
                $q->where('reference', 'like', "%{$searchTerm}%")
                    // Deep Search into Invoice & Customer
                    ->orWhereHas('invoice', function ($invQ) use ($searchTerm) {
                        $invQ->where('invoice_number', 'like', "%{$searchTerm}%")
                            ->orWhereHas('customer', function ($custQ) use ($searchTerm) {
                                $custQ->where('name', 'like', "%{$searchTerm}%")
                                    ->orWhere('organization_name', 'like', "%{$searchTerm}%")
                                    ->orWhere('phone', 'like', "%{$searchTerm}%");
                            });
                    });
            });
        }

        // --- 2. FILTER BY CUSTOMER ---
        if ($request->filled('customer_id')) {
            $customerId = $request->customer_id;
            $query->whereHas('invoice', function ($invQ) use ($customerId) {
                $invQ->where('customer_id', $customerId);
            });
        }

        // --- 3. FILTER BY METHOD ---
        if ($request->filled('method')) {
            $query->where('method', $request->method);
        }

        // --- 4. FILTER BY DATE ---
        if ($filter !== 'all') {
            $endDate = \Carbon\Carbon::now()->endOfDay();

            switch ($filter) {
                case 'today':
                    $startDate = \Carbon\Carbon::today()->startOfDay();
                    break;
                case 'this_week':
                    $startDate = \Carbon\Carbon::now()->startOfWeek();
                    break;
                case 'this_month':
                    $startDate = \Carbon\Carbon::now()->startOfMonth();
                    break;
                case 'this_year':
                    $startDate = \Carbon\Carbon::now()->startOfYear();
                    break;
                case 'custom':
                    $startDate = $request->start_date ? \Carbon\Carbon::parse($request->start_date)->startOfDay() : \Carbon\Carbon::today()->startOfDay();
                    $endDate = $request->end_date ? \Carbon\Carbon::parse($request->end_date)->endOfDay() : \Carbon\Carbon::now()->endOfDay();
                    break;
                case 'last_30_days':
                default:
                    $startDate = \Carbon\Carbon::today()->subDays(30)->startOfDay();
                    break;
            }

            $query->whereBetween('created_at', [$startDate, $endDate]);
        }

        // appends() keeps all search/filter params in the URL when changing pages!
        $payments = $query->latest()->paginate(15)->appends($request->query());

        // Fetch customers for the dropdown filter
        $customers = \App\Models\Customer::where('branch_id', $branchId)->orderBy('organization_name')->get();

        return view('payments.index', compact('payments', 'filter', 'customers'));
    }

    public function store(Request $request)
    {
        $branchId = Auth::user()->branch_id;

        $validator = Validator::make($request->all(), [
            'invoice_id' => [
                'required',
                Rule::exists('invoices', 'id')->where('branch_id', $branchId)
            ],
            'amount' => 'required|numeric|min:0.01',
            'method' => 'required|string',
            'payment_date' => 'nullable|date',
            // The migration enforces a unique method + reference combo
            'reference' => [
                'required',
                'string',
                Rule::unique('payments')->where(function ($query) use ($request) {
                    return $query->where('method', $request->method);
                })
            ],
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $invoice = Invoice::where('branch_id', $branchId)->findOrFail($request->invoice_id);
        $balance = $invoice->total_amount - $invoice->amount_paid;

        // Prevent overpayment
        if ($request->amount > $balance) {
            return response()->json([
                'errors' => ['amount' => ["Payment amount cannot exceed the balance due of {$balance}"]]
            ], 422);
        }

        // --- Wallet Payment Logic ---
        if ($request->method === 'Wallet') {
            if ($invoice->customer->wallet_balance < $request->amount) {
                return response()->json(['errors' => ['amount' => ['Insufficient wallet balance.']]], 422);
            }
            // Deduct from wallet
            $invoice->customer->decrement('wallet_balance', $request->amount);
            $reference = 'WALLET-PAY-' . time();
        } else {
            $reference = $request->reference;
        }

        // Create the Payment Record
        $payment = Payment::create([
            'branch_id' => $branchId,
            'invoice_id' => $invoice->id,
            'amount' => $request->amount,
            'method' => $request->method,
            'reference' => $reference,
            'status' => 'completed',
            // Fallback to now() if no custom date is provided
            'created_at' => $request->payment_date ? \Carbon\Carbon::parse($request->payment_date) : now(),
        ]);

        // Update Invoice Totals
        $newAmountPaid = $invoice->amount_paid + $request->amount;
        $newStatus = $invoice->status;

        if ($newAmountPaid >= $invoice->total_amount) {
            $newStatus = 'paid';
        } elseif ($newAmountPaid > 0) {
            $newStatus = 'partially_paid';
        }

        $invoice->update([
            'amount_paid' => $newAmountPaid,
            'status' => $newStatus,
        ]);

        // Trigger your PaymentReceived Event for the SMS!
        event(new PaymentReceived($payment));

        return response()->json(['success' => 'Payment recorded successfully!']);
    }

    // --- Transfer Overpayment to Wallet ---
    public function transferToWallet($invoiceId)
    {
        $invoice = \App\Models\Invoice::findOrFail($invoiceId);
        $excess = $invoice->amount_paid - $invoice->total_amount;

        if ($excess <= 0) {
            return response()->json(['message' => 'No overpayment found on this invoice.'], 400);
        }

        DB::beginTransaction();
        try {
            // Credit the Customer's Wallet
            $invoice->customer->increment('wallet_balance', $excess);

            // Adjust the Invoice paid amount down to the exact total
            $invoice->amount_paid = $invoice->total_amount;
            $invoice->save();

            // Create a negative payment to balance the invoice ledger!
            \App\Models\Payment::create([
                'branch_id' => $invoice->branch_id,
                'invoice_id' => $invoice->id,
                'amount' => -$excess,
                'method' => 'Wallet Transfer',
                'reference' => 'TO-WALLET-' . time(),
                'status' => 'completed',
                'created_at' => now(),
            ]);

            DB::commit();
            return response()->json(['success' => 'Overpayment moved to wallet successfully!']);
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['message' => 'Transfer failed.'], 500);
        }
    }

    public function edit($id)
    {
        $payment = Payment::where('branch_id', Auth::user()->branch_id)->findOrFail($id);
        return response()->json($payment);
    }

    public function update(Request $request, $id)
    {
        $branchId = Auth::user()->branch_id;
        $payment = Payment::with('invoice')->where('branch_id', $branchId)->findOrFail($id);

        $validator = Validator::make($request->all(), [
            'amount' => 'required|numeric|min:0.01',
            'method' => 'required|string',
            'reference' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $invoice = $payment->invoice;

        // Calculate the difference to adjust the invoice's amount_paid
        $difference = $request->amount - $payment->amount;
        $newAmountPaid = $invoice->amount_paid + $difference;

        // Ensure they aren't pushing the payment past the total amount
        if ($newAmountPaid > $invoice->total_amount) {
            return response()->json([
                'errors' => ['amount' => ["This edit would overpay the invoice by " . ($newAmountPaid - $invoice->total_amount)]]
            ], 422);
        }

        // 1. Update Payment
        $payment->update([
            'amount' => $request->amount,
            'method' => $request->method,
            'reference' => $request->reference,
        ]);

        // 2. Update Invoice Status
        $newStatus = 'processing';
        if ($newAmountPaid >= $invoice->total_amount) {
            $newStatus = 'paid';
        } elseif ($newAmountPaid > 0) {
            $newStatus = 'partially_paid';
        }

        $invoice->update([
            'amount_paid' => $newAmountPaid,
            'status' => $newStatus,
        ]);

        return response()->json(['success' => 'Payment updated successfully!']);
    }

    public function destroy($id)
    {
        $payment = Payment::findOrFail($id);

        // Ensure the user is authorized to delete this specific payment
        abort_if($payment->branch_id !== Auth::user()->branch_id, 403);

        $invoice = $payment->invoice;

        // Start a database transaction to ensure data integrity
        DB::beginTransaction();
        try {
            $paymentAmount = $payment->amount;
            $payment->delete();

            if ($invoice) {
                // Deduct the deleted payment amount from the invoice's paid total
                $invoice->amount_paid -= $paymentAmount;

                // If the invoice was fully paid, but now has a balance, revert status to partially_paid
                if ($invoice->amount_paid < $invoice->total_amount && $invoice->status === 'paid') {
                    $invoice->status = 'partially_paid';
                }

                // If it's completely unpaid now, revert to processing
                if ($invoice->amount_paid <= 0 && $invoice->status === 'partially_paid') {
                    $invoice->status = 'processing';
                    $invoice->amount_paid = 0; // Safeguard against negative values
                }

                $invoice->save();
            }

            DB::commit();
            return response()->json(['success' => 'Payment deleted successfully.']);
        } catch (\Exception $e) {
            DB::rollback();
            return response()->json(['message' => 'Failed to delete payment.'], 500);
        }
    }

    public function downloadReceipt($id)
    {
        $payment = Payment::with(['invoice.customer', 'branch'])->findOrFail($id);

        if ($payment->branch_id !== Auth::user()->branch_id) {
            abort(403);
        }

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('payments.receipt_pdf', compact('payment'));
        return $pdf->stream('Receipt_' . $payment->reference . '.pdf');
    }

    public function publicReceipt($id)
    {
        // Notice we do NOT check Auth::user() here. The 'signed' middleware protects this.
        $payment = Payment::with(['invoice.customer', 'invoice.branch'])->findOrFail($id);

        $pdf = \Barryvdh\DomPDF\Facade\Pdf::loadView('payments.receipt_pdf', compact('payment'));

        return $pdf->stream('Receipt_' . $payment->reference . '.pdf');
    }

    public function export(Request $request)
    {
        $branchId = Auth::user()->branch_id;
        $filter = $request->get('filter', 'last_30_days');

        $startDate = Carbon::today()->subDays(30)->startOfDay();
        $endDate = Carbon::now()->endOfDay();

        switch ($filter) {
            case 'today':
                $startDate = Carbon::today()->startOfDay();
                break;
            case 'this_week':
                $startDate = Carbon::now()->startOfWeek();
                break;
            case 'this_month':
                $startDate = Carbon::now()->startOfMonth();
                break;
            case 'this_year':
                $startDate = Carbon::now()->startOfYear();
                break;
            case 'custom':
                $request->validate([
                    'start_date' => 'required|date',
                    'end_date' => 'required|date|after_or_equal:start_date',
                ]);
                $startDate = Carbon::parse($request->start_date)->startOfDay();
                $endDate = Carbon::parse($request->end_date)->endOfDay();
                break;
            case 'last_30_days':
            default:
                $startDate = Carbon::today()->subDays(30)->startOfDay();
                break;
        }

        $timestamp = now()->format('Y_m_d_His');
        $fileName = "payments_{$filter}_{$timestamp}.xlsx";

        return Excel::download(new PaymentsExport($branchId, $startDate, $endDate), $fileName);
    }


    public function mpesaTopUp(Request $request)
    {
        try {

            DB::beginTransaction();

            $mpesahelper = new MpesaHelper();
            $amount = $request->amount;
            $account_number = $request->account_number;
            $phone = formatPhoneNumber($request->mpesa_number);
            $response = $mpesahelper->express($amount, $phone,  $account_number);
            $result = json_decode($response);
            if (isset($result) && isset($result->CheckoutRequestID)) {
                $CheckoutRequestID = $result->CheckoutRequestID;
                $MerchantRequestID = $result->MerchantRequestID;
                $data = [
                    'CheckoutRequestID' => $CheckoutRequestID,
                    'MerchantRequestID' => $MerchantRequestID,
                    'account' =>  $account_number,
                    'amount' => $amount,
                    'PhoneNumber' => $phone,
                    'method' => 'STK-Push',
                    'channel' => 'MPESA',
                    'status' => 'pending',
                    'created_by' => Auth::user()->id,
                ];
                $stkpayment = StkPush::create($data);

                DB::commit();
                return response()->json([
                    'success' => true,
                    'message' => "Success! You will be taken to Mpesa menu shortly",
                    'account' =>  $account_number,
                    'method' => 'mpesa',
                    'CheckoutRequestID' => $CheckoutRequestID,
                    'MerchantRequestID' => $MerchantRequestID,
                ]);
            } else {
                return response()->json([
                    'success' => false,
                    'message' => "Failed! Please try again Later",
                    'error' => $result->errorMessage,
                ]);
            }
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed. We could not process your payment',
                'success' => false,
                'error' => $e->getMessage()
            ], 200);
        }
    }

    public function stkcallback(Request $request)
    {
        $payload = $request->all();
        Log::info('Received STK Callback: ' . json_encode($payload));

        $stk = data_get($payload, 'Body.stkCallback', []);
        $merchantRequestId = (string) data_get($stk, 'MerchantRequestID', '');
        $checkoutRequestId = (string) data_get($stk, 'CheckoutRequestID', '');
        $resultDesc        = (string) data_get($stk, 'ResultDesc', '');
        $resultCode        = (int) data_get($stk, 'ResultCode', 1);

        if ($merchantRequestId === '' || $checkoutRequestId === '') {
            return response()->json(['success' => false, 'message' => 'Invalid callback payload'], 400);
        }

        $stkpayment = StkPush::where('CheckoutRequestID', $checkoutRequestId)
            ->where('MerchantRequestID', $merchantRequestId)
            ->first();

        if (!$stkpayment) {
            return response()->json([
                'success' => false,
                'message' => 'Failed, no record matching the transaction',
            ], 404);
        }

        // FAILED / CANCELLED
        if ($resultCode !== 0) {
            $stkpayment->update([
                'status' => $resultDesc ?: 'failed',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Callback received (failed)',
                'result_code' => $resultCode,
                'result_description' => $resultDesc,
            ]);
        }

        // SUCCESS
        $items = (array) data_get($stk, 'CallbackMetadata.Item', []);
        $m = $this->mpesaItems($items);

        $amount          = (float) ($m['Amount'] ?? 0);
        $receiptNumber   = (string) ($m['MpesaReceiptNumber'] ?? '');
        $phone           = (string) ($m['PhoneNumber'] ?? '');
        $transactionDate = (string) ($m['TransactionDate'] ?? '');

        if ($amount <= 0 || $receiptNumber === '') {
            // still store what we can for debugging
            $stkpayment->update([
                'status' => 'confirmed_but_missing_fields',
            ]);

            return response()->json([
                'success' => false,
                'message' => 'Missing Amount or MpesaReceiptNumber in metadata',
            ], 422);
        }

        // Resolve account number (if 2 parts take last else whole "#")
        $parts = explode('#', (string) $stkpayment->account);
        $displayAccountNumber = (count($parts) > 1) ? $parts[1] : $stkpayment->account;

        DB::transaction(function () use (
            $stkpayment,
            $displayAccountNumber,
            $amount,
            $receiptNumber,
            $phone,
            $transactionDate,
            $merchantRequestId,
            $checkoutRequestId,
        ) {

            // Update STK record
            $stkpayment->update([
                'CheckoutRequestID' => $checkoutRequestId,
                'MerchantRequestID' => $merchantRequestId,
                'amount' => $amount,
                'PhoneNumber' => $phone,
                'TransactionDate' => $transactionDate,
                'ReceiptNumber' => $receiptNumber,
                'status' => 'confirmed',
            ]);

            // Lock Invoice row to prevent race conditions
            $invoice = Invoice::where('invoice_number', $displayAccountNumber)
                ->lockForUpdate()
                ->first();

            // if ($invoice) {
            //     // 1. Create the Payment Record
            //     $payment = Payment::create([
            //         'branch_id' => $invoice->branch_id,
            //         'invoice_id' => $invoice->id,
            //         'amount' => $amount,
            //         'method' => 'M-Pesa',
            //         'reference' => $receiptNumber,
            //         'status' => 'completed',
            //     ]);

            //     // 2. Update Invoice Totals
            //     $newAmountPaid = $invoice->amount_paid + $amount;
            //     $newStatus = $invoice->status;

            //     if ($newAmountPaid >= $invoice->total_amount) {
            //         $newStatus = 'paid';
            //     } elseif ($newAmountPaid > 0) {
            //         $newStatus = 'partially_paid';
            //     }

            //     $invoice->update([
            //         'amount_paid' => $newAmountPaid,
            //         'status' => $newStatus,
            //     ]);

            //     // Trigger your PaymentReceived Event for the SMS!
            //     event(new PaymentReceived($payment));

            //     ActivityLogger::log(
            //         'Success MPESA Top Up',
            //         "Transaction was successful amount: KES {$amount} REF: {$receiptNumber}",
            //         null,
            //         null,
            //         $invoice->branch_id
            //     );
            // }
        });

        return response()->json([
            'success' => true,
            'message' => 'Transaction was successful',
            'result_code' => $resultCode,
            'result_description' => $resultDesc,
        ]);
    }

    private function mpesaItems(array $items): array
    {
        $out = [];
        foreach ($items as $it) {
            $name = $it['Name'] ?? null;
            if (!$name) continue;
            $out[$name] = $it['Value'] ?? null;
        }
        return $out;
    }

    function checkTransactionStatus(Request $request)
    {
        $checkout_request_id = $request->CheckoutRequestID;
        $merchant_request_id = $request->MerchantRequestID;
        $stkpayment = StkPush::where('CheckoutRequestID', $checkout_request_id)->where('MerchantRequestID', $merchant_request_id)->first();
        if ($stkpayment) {
            return response()->json([
                'success' => true,
                'message' => $stkpayment->status,
                'status' => $stkpayment->status,
                'receipt_number' => $stkpayment->ReceiptNumber,
            ]);
        } else {
            return response()->json([
                'success' => false,
                'message' => "Failed, no record matching the transaction",
            ]);
        }
    }

    public function initiateBuniStk(Request $request)
    {
        try {

            DB::beginTransaction();
            ActivityLogger::log('KCB Top Up', Auth::user()->name . ' Initiates KCB top up');
            $amount = $request->amount;
            $branch = $request->kra_pin_number;
            $phone = formatPhoneNumber($request->mpesa_number);
            $orgShortCode = env('BUNI_SHORTCODE');
            $orgPassKey = env('BUNI_PASS_KEY');
            $AccountReference = $branch;
            # M-PESA endpoint urls
            $initiate_url = 'https://api.buni.kcbgroup.com/mm/api/request/1.0.0/stkpush';

            # callback url
            $CallBackURL = env('BUNI_CALLBACK_URL');

            $access_token = $this->generateBuniToken();

            # header for stk push
            $stkheader = ['Content-Type:application/json', 'Authorization:Bearer ' . $access_token];

            # initiating the transaction
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $initiate_url);
            curl_setopt($curl, CURLOPT_HTTPHEADER, $stkheader); //setting custom header

            $curl_post_data = array(
                //Fill in the request parameters with valid values
                'amount' => $amount,
                'phoneNumber' => $phone,
                'callbackUrl' => $CallBackURL,
                "invoiceNumber" => "$AccountReference",
                // 'AccountReference' => $AccountReference,
                "sharedShortCode" => true,
                "orgShortCode" => $orgShortCode,
                "orgPassKey" => $orgPassKey,
                'transactionDescription' => 'SMS Payment'
            );


            $data_string = json_encode($curl_post_data);
            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data_string);
            $curl_response = curl_exec($curl);

            //Decode the json to get a response code
            $data_m = json_decode($curl_response, true);

            if (isset($data_m['errorCode'])) {
                return response()->json([
                    'success' => false,
                    'status' => 'error',
                    'message' => $data_m['errorMessage'],
                    'account' => $branch,
                    'method' => 'kcb_mpesa',
                ]);
            }

            if (isset($data_m['response']['MerchantRequestID']) && $data_m['response']['ResponseCode'] == '0') {

                // Retrieve values from the response
                $merchantRequestID = $data_m['response']['MerchantRequestID'];
                $responseCode = $data_m['response']['ResponseCode'];
                $customerMessage = $data_m['response']['CustomerMessage'];
                $checkoutRequestID = $data_m['response']['CheckoutRequestID'];
                $responseDescription = $data_m['response']['ResponseDescription'];

                $data = [
                    'CheckoutRequestID' => $checkoutRequestID,
                    'MerchantRequestID' => $merchantRequestID,
                    'account' => $branch,
                    'amount' => $amount,
                    'PhoneNumber' => $phone,
                    'method' => 'STK-Push',
                    'channel' => 'MPESA',
                    'status' => 'pending',
                    'created_by' => Auth::user()->id,
                ];
                // Log::info($data);
                $stkpayment = StkPush::create($data);

                DB::commit();

                return response()->json([
                    'status' => 'ok',
                    'success' => true,
                    'message' => $responseDescription,
                    'account' => $branch,
                    'method' => 'kcb_mpesa',
                    'CheckoutRequestID' => $checkoutRequestID,
                    'MerchantRequestID' => $merchantRequestID,
                ]);
            } else {
                $errorMessage = $data_m['header']['statusDescription'];
                echo json_encode(array('status' => 'error', 'message' => $errorMessage));
                return response()->json([
                    'status' => 'error',
                    'success' => false,
                    'message' => $errorMessage,
                    'error' => $errorMessage,
                ]);
            }
        } catch (Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Failed. We could not process your payment',
                'success' => false,
                'error' => $e->getMessage()
            ], 200);
        }
    }

    /**
     * Generate BUNI API Token
     */
    public function generateBuniToken()
    {
        //live
        $consumerKey = env('BUNI_CONSUMER_KEY');
        $consumerSecret = env('BUNI_SECRET');

        $url = 'https://api.buni.kcbgroup.com/token?grant_type=client_credentials';

        $credentials = base64_encode("$consumerKey:$consumerSecret");

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => $url,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'POST',
            CURLOPT_HTTPHEADER => array(
                "Authorization: Basic $credentials",
            ),
        ));

        $response = curl_exec($curl);
        $status = curl_getinfo($curl, CURLINFO_HTTP_CODE);
        $response = json_decode($response);

        $access_token = $response->access_token;

        curl_close($curl);
        return $access_token;
    }

    function c2bValidation()
    {
        header("Content-Type: application/json");

        //   	$response = ' {
        //     "TransactionType":"",
        //     "TransID":"LGR219G3EY",
        //     "TransTime":"20170727104247",
        //     "TransAmount":"10.00",
        //     "BusinessShortCode":"600134",
        //     "BillRefNumber":"xyz",
        //     "InvoiceNumber":"",
        //     "OrgAccountBalance":"",
        //     "ThirdPartyTransID":"",
        //     "MSISDN":"254708374149",
        //     "FirstName":"John",
        //     "MiddleName":"Doe",
        //     "LastName":""
        //   }';

        // Save the M-PESA input stream. 
        $mpesaResponse = file_get_contents('php://input');

        /* If we have any validation, we will do it here then change the $response if we reject the transaction */
        // Your Validation
        // $response = '{  "ResultCode": 1, "ResultDesc": "Transaction Rejected."  }';
        /* Ofcourse we will be checking for amount, account number(incase of paybill), invoice number and inventory.*/



        // will be used when we want to save the response to database for our reference
        $jsonMpesaResponse = json_decode($mpesaResponse, true);
    }

    public function c2bProcessor(Request $request)
    {
        $data = $request->all();
        Log::channel('mpesa_payments')->info('M-Pesa Callback Data: ', $data);

        // 1. Safely extract variables directly from the M-Pesa payload
        $invoiceNumber = $data['BillRefNumber'] ?? null; // What the customer typed as Account No
        $amount        = $data['TransAmount'] ?? 0;
        $receiptNumber = $data['TransID'] ?? null;
        $phoneNumber   = $data['MSISDN'] ?? null;

        // If Safaricom sent bad data, reject it gracefully without throwing a 302 redirect
        if (!$invoiceNumber || !$amount || !$receiptNumber) {
            return response()->json([
                "ResultCode" => 1,
                "ResultDesc" => "Rejected: Missing required fields"
            ]);
        }

        // 2. Lock Invoice row to prevent race conditions
        // Match invoice
        $cleanNumber = preg_replace('/[^0-9]/', '', $invoiceNumber);
        $pureId = ltrim($cleanNumber, '0');
        $formattedInvoiceNumber = 'INV-' . str_pad($pureId, 5, '0', STR_PAD_LEFT);

        $invoice = Invoice::where('invoice_number', $invoiceNumber)
            ->orWhere('invoice_number', $formattedInvoiceNumber)
            ->first();

        if ($invoice) {
            // Create the Payment Record
            $payment = Payment::create([
                'branch_id'  => $invoice->branch_id,
                'invoice_id' => $invoice->id,
                'amount'     => $amount,
                'method'     => 'M-Pesa',
                'reference'  => $receiptNumber,
                'status'     => 'completed',
            ]);

            // Update Invoice Totals
            $newAmountPaid = $invoice->amount_paid + $amount;
            $newStatus = $invoice->status; // Keep current status by default

            // Prevent downgrading logistics statuses! 
            // Only auto-update status if it is currently in a financial-holding state.
            if (in_array($invoice->status, ['processing', 'pending', 'partially_paid'])) {
                if ($newAmountPaid >= $invoice->total_amount) {
                    $newStatus = 'paid';
                } else {
                    $newStatus = 'partially_paid';
                }
            }

            $invoice->update([
                'amount_paid' => $newAmountPaid,
                'status'      => $newStatus,
            ]);

            // AUTO-WALLET TRANSFER
            $excess = $invoice->amount_paid - $invoice->total_amount;

            if ($excess > 0) {
                // Credit Wallet
                $invoice->customer->increment('wallet_balance', $excess);

                // Adjust Invoice Paid Amount
                $invoice->amount_paid = $invoice->total_amount;
                $invoice->save();

                // Create Balancing Ledger
                \App\Models\Payment::create([
                    'branch_id' => $invoice->branch_id,
                    'invoice_id' => $invoice->id,
                    'amount' => -$excess,
                    'method' => 'Wallet Transfer (Auto)',
                    'reference' => 'AUTO-WALLET-' . time(),
                    'status' => 'completed',
                ]);
            }

            // Trigger your PaymentReceived Event for the SMS!
            event(new PaymentReceived($payment));

            ActivityLogger::log(
                'Success MPESA Top Up',
                "Transaction was successful. Amount: KES {$amount} REF: {$receiptNumber} Phone: {$phoneNumber}",
                null,
                null,
                $invoice->branch_id
            );
        }

        // MUST send success response back to Safaricom to stop them from retrying!
        return response()->json([
            "ResultCode" => 0,
            "ResultDesc" => "Accepted"
        ]);
    }

    public function kcbIpn(
        Request $request,
        KcbIpnAdapter $adapter,
        PaymentProcessingService $service
    ) {
        $payload = json_decode($request->getContent(), true);

        Log::channel('kcb_payments')->info(json_encode($request->all()));

        $transactionRef = $payload['transactionReference'] ?? null;

        // 🔒 RAW IPN DUPLICATE CHECK
        if (
            $transactionRef &&
            KcbTransaction::where('transaction_reference', $transactionRef)->exists()
        ) {
            return response()->json([
                "transactionID"  => $transactionRef,
                "statusCode"     => 0,
                "statusMessage"  => "Duplicate transaction"
            ], 200);
        }

        // Store raw KCB IPN
        $kcbTransaction = KcbTransaction::create([
            'transaction_reference'     => $transactionRef,
            'request_id'                => $payload['requestId'] ?? null,
            'channel_code'              => $payload['channelCode'] ?? null,
            'timestamp'                 => $payload['timestamp'] ?? null,
            'transaction_amount'        => $payload['transactionAmount'] ?? null,
            'currency'                  => $payload['currency'] ?? null,
            'customer_reference'        => $payload['customerReference'] ?? null,
            'customer_name'             => $payload['customerName'] ?? null,
            'customer_mobile_number'    => $payload['customerMobileNumber'] ?? null,
            'narration'                 => $payload['narration'] ?? null,
            'credit_branch_identifier' => $payload['creditAccountIdentifier'] ?? null,
            'organization_short_code'   => $payload['organizationShortCode'] ?? null,
            'till_number'               => $payload['tillNumber'] ?? null,
        ]);

        // Normalize payload → DTO
        $paymentData = $adapter->fromPayload($payload);

        // 🔒 BUSINESS-LEVEL DUPLICATE CHECK (inside service)
        $service->processTopUp($paymentData);

        return response()->json([
            "transactionID"  => $transactionRef,
            "statusCode"     => 0,
            "statusMessage"  => "Notification received"
        ], 200);
    }

    function splitName($fullName)
    {
        // Split the full name by spaces
        $nameParts = explode(' ', $fullName);

        // The first part is the first name
        $firstName = $nameParts[0];

        // The remaining parts are the last name
        // If there's only one part, it becomes the last name as well
        $lastName = count($nameParts) > 1 ? implode(' ', array_slice($nameParts, 1)) : '';

        return [
            'first_name' => $firstName,
            'last_name' => $lastName
        ];
    }
}
