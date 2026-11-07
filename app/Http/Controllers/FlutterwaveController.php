<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Notifications;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use EdwardMuss\Rave\Facades\Rave as Flutterwave;
use auth;

class FlutterwaveController extends Controller
{
    /**
     * Initialize Rave payment process
     * @return void
     */
    public function initialize(Request $request)
    {
        //This generates a payment reference
        $reference = Flutterwave::generateReference();
        $phone = $formattedContact = formatPhoneNumber($request->phone);
        $amount = $request->amount;

        // Enter the details of the payment
        $data = [
            'payment_options' => 'card,banktransfer,mpesa',
            'amount' => $amount,
            'email' => auth()->user()->email,
            'tx_ref' => $reference,
            'currency' => "KES",
            'redirect_url' => route('ravecallback'),
            'customer' => [
                'email' => auth()->user()->email,
                "phone_number" => $phone,
                "name" => auth()->user()->first_name
            ],

            "customizations" => [
                "title" => 'Buy Book',
                "description" => "Complete Transaction",
                "logo" => asset('assets/frontend/images/logo.png')
            ]
        ];

        $payment = Flutterwave::initializePayment($data);

        // return $data;
        if ($payment['status'] !== 'success') {
            // notify something went wrong
            return redirect()->back()->with('fail', 'Something went wrong, to complete transaction');
        }
        return redirect($payment['data']['link']);
    }

    /**
     * Obtain Rave callback information
     * @return void
     */
    public function callback()
    {

        $commonHelper = new CommonHelper();
        $user = auth()->user();
        $status = request()->status;
        // return $status;

        //if payment is successful
        if ($status ==  'successful') {

            $transactionID = Flutterwave::getTransactionIDFromCallback();
            $data = Flutterwave::verifyTransaction($transactionID);
            // Order::where('reference', $data['data']['tx_ref'])
            //     ->update([
            //         'transaction_id' => $data['data']['id'],
            //         'app_fee' => $data['data']['app_fee'],
            //         'merchant_fee' => $data['data']['merchant_fee'],
            //         'payment_type' => $data['data']['payment_type'],
            //         'status' => $data['data']['status']
            //     ]);

            $orderid = $data['data']['tx_ref'];
            $purchase = BookPurchases::where('reference_id', $orderid)->get();
            // DB::beginTransaction();
            if (auth()->check()) {
                $carts = BooksCart::where('user_id', auth()->user()->id)->get();
            } else {
                $carts = BooksCart::where('session_id', session()->getId())->get();
            }
            $amount = $carts->sum('total_price');
            // $code = $this->getRef(5);

            $utits = new Utils();

            foreach ($carts as $ct) {
                $book = Books::find($ct->book_id);

                $purchase = BookPurchases::create([
                    'status' => 'pending',
                    'amount' => $book->cost,
                    'book_id' => $book->id,
                    'user_id' => auth()->user()->id,
                    'payment_method' => 'flutterwave',
                    'reference_id' => $$orderid
                ]);
                $notification = Notifications::create([
                    'message' => 'You have one download. Click to view',
                    'user_id' => auth()->user()->id,
                    'narration' => 'purchase',
                    'reference' => $book->id
                ]);
            }

            foreach ($purchase as $pur) {

                $pur->update([
                    'status' => 'paid',
                    'payment_method' => "flutterwave-" . $data['data']['payment_type'],
                    'reference_id' => $data['data']['id']
                ]);
            }
            foreach ($carts as $ct) {
                $ct->delete();
            }

            $to = $user->email;
            $to_name = $user->first_name . ' ' . $user->last_name;
            $subject = "New Order Received";
            $email_data = (object) array(
                'title' => "Dear <b>$to_name</b>,",
                'link' => 'https://africaninkpublishers.com/account',
                'link_text' => "My Account",
                'date' => date('d F, Y', strtotime(date('Y-m-d'))),
                'body' => "<p><b>We have received your order #$orderid amount: $amount</b>, <p>We will notify you once the payment is done."
            );

            try {
                $email = $commonHelper->sendDefaultMail($email_data, $to, $to_name, $subject);
            } catch (Exeception $e) {
                // return redirect()->back()->with('fail', $e->getMessage());
            }

            // dd($data['data']['tx_ref']);
            return redirect()->route('checkout')->with('success', 'Your order is confirmed');
        } elseif ($status ==  'cancelled') {

            return redirect()->route('checkout')->with('fail', 'You cancelled the order');
        } else {

            return redirect()->route('checkout')->withErrors(__('Something went wrong.'));
        }
    }

    public function checkAccount(Request $request)
    {

        // $user = User::where('phone', $request['phone'])->first();
        // if ($user) {
        //     return $user;
        // }
        $user = User::where('email', $request['email'])->first();
        if ($user) {
            return $user;
        }

        //Validate Inputs
        $request->validate([
            'first_name' => 'required|min:3',
            'last_name' => 'required|min:3',
            'phone' => 'required|min:7',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|min:6|max:30',
            'confirm_password' => 'required|min:6|max:30|same:password',
            // 'g-recaptcha-response' => ['required', new ReCaptcha]
        ]);

        $user = User::create([
            'first_name' => $request['first_name'],
            'last_name' => $request['last_name'],
            'email' => $request['email'],
            'password' => $request['password'],
            'phone' => $request['phone'],
            'id_number' => 'none',
            'photo' => 'none',
            'isActive' => true,
            'dob' => 'N/A',
            'gender' => 'N/A',
            'device_token' => 'N/A',
            'about' => 'N/A',
            'isWritter' => false,
            'isStaff' => false,
        ]);
        auth()->login($user);
        return $user;
    }
}
