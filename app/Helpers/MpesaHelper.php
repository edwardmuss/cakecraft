<?php

namespace App\Helpers;

use Exception;
use App\Models\User;
use Illuminate\Support\Facades\Log;

class MpesaHelper
{

    private $base_url;
    /**
     * The consumer key
     * @var string $consumer_key
     */
    public $consumer_key;
    /**
     * The consumer key secret
     * @var string $consumer
     * _secret
     */
    public $consumer_secret;
    /**
     * The MPesa Paybill number
     * @var int $paybill
     */
    public $paybill;
    /**
     * The Lipa Na MPesa till number
     * @var int $lipa_na_mpesa
     */
    public $tillnumber;
    /**
     * The Lipa Na MPesa paybill number
     * @var int $lipa_na_mpesa
     */

    public $lipa_na_mpesa;
    /**
     * The Lipa Na MPesa paybill number SAG Key
     * @var string $lipa_na_mpesa_key
     */
    public $lipa_na_mpesa_key;
    /**
     * The Mpesa portal Username
     * @var string $initiator_username
     */
    public $initiator_username;
    /**
     * The Mpesa portal Password
     * @var string $initiator_password
     */
    public $initiator_password;
    /**
     * The Callback common part of the URL eg "https://domain.com/callbacks/"
     * @var string $initiator_password
     */
    private $callback_baseurl;
    /**
     * The test phone number provided by safaricom. For developers
     * @var string $test_msisdn
     */
    private $test_msisdn;
    /**
     * The signed API credentials
     * @var string $cred
     */
    private $cred;
    private $access_token;

    /*Callbacks*/
    public $bctimeout;
    public $bcresult;
    public $bbtimeout;
    public $bbresult;
    public $baltimeout;
    public $balresult;
    public $statustimeout;
    public $statusresult;
    public $reversetimeout;
    public $reverseresult;
    public $cbvalidate;
    public $cbconfirm;
    public $lnmocallback;

    public function __construct()
    {
        $this->base_url = 'https://api.safaricom.co.ke';

        $this->consumer_key = env('MPESA_CONSUMER_KEY'); #for c2b
        $this->consumer_secret = env('MPESA_SECRET'); #for c2b
        $this->paybill = env('MPESA_SHORTCODE'); #for c2b
        $this->lipa_na_mpesa_key = env('MPESA_PASS_KEY');

        $this->lipa_na_mpesa = env('MPESA_SHORTCODE'); #for b2c
        $this->initiator_username = "benson";
        $this->cred = "NydNWaWjC6hzxNSMnQux5C5dg8bmIf78+VDNilBCXyMkiZz936SQLYqOAVF4c2ewcO7LfHMiXMHzIPp26VviIHxYYdSrrzgMuihCDYfOcCqpT3US/1iDy7HbAHjvmWFggVhfIdqVzfP9Sq+iVY9oydkBn2zeZ76+6Opr16ea2Qm4ofE6/Ls4Bn2YdTDr9jgv/zGBRv9/OfIsschRTuuRh9RMWRaDxUgy3XOE17so/Z+tA8kp7WtQN24yW6Dq6yQWr3OEJgWZmv8ykCR1Abd6vAl2w3Oyl51VJmMFn1fGq+XFU2P2NLvf4pPS+a6r+xLRncgbfBytQUOrsgZ/l7Tc+Q==";
        $this->initiator_password = "NydNWaWjC6hzxNSMnQux5C5dg8bmIf78+VDNilBCXyMkiZz936SQLYqOAVF4c2ewcO7LfHMiXMHzIPp26VviIHxYYdSrrzgMuihCDYfOcCqpT3US/1iDy7HbAHjvmWFggVhfIdqVzfP9Sq+iVY9oydkBn2zeZ76+6Opr16ea2Qm4ofE6/Ls4Bn2YdTDr9jgv/zGBRv9/OfIsschRTuuRh9RMWRaDxUgy3XOE17so/Z+tA8kp7WtQN24yW6Dq6yQWr3OEJgWZmv8ykCR1Abd6vAl2w3Oyl51VJmMFn1fGq+XFU2P2NLvf4pPS+a6r+xLRncgbfBytQUOrsgZ/l7Tc+Q==";                 //Initiator password. I dont know where to get this either.

        $this->callback_baseurl = "";
        $this->lnmocallback = env('MPESA_CALLBACK_URL');
        $this->test_msisdn = "254741226412";
        Log::info($this->lnmocallback);
        // c2b the urls
        $this->cbvalidate = env('MPESA_CALLBACK_URL') . '/api/payment-confirmation-Callback';
        $this->cbconfirm = env('MPESA_CALLBACK_URL') . '/api/payment-confirmation-Callback';

        $this->access_token = $this->getAccessToken(); //Set up access token
    }

    // get access token
    public function getAccessToken()
    {
        $credentials = base64_encode($this->consumer_key . ':' . $this->consumer_secret);
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->base_url . '/oauth/v1/generate?grant_type=client_credentials');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Basic ' . $credentials, 'Content-Type: application/json'));
        $response = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);
        $response = json_decode($response);

        if ($info["http_code"] == 200) {
            $access_token = $response->access_token;
            $this->access_token = $access_token;
            return $access_token;
        } else {
            //throw new Exception("Invalid Consumer key or secret");
            return false;
        }
    }

    // Make request
    private function submit_request($url, $data)
    { // Returns cURL response

        if (isset($this->access_token)) {
            $access_token = $this->access_token;
        } else {
            $access_token = $this->getAccessToken();
        }

        if ($access_token != '' || $access_token !== false) {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Bearer ' . $access_token));

            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

            $response = curl_exec($curl);
            curl_close($curl);
            return $response;
        } else {
            return false;
        }
    }

    // STK Push Request
    public function express($amount, $phone, $ref, $desc = "Payment")
    {
        if (!is_numeric($amount) || $amount < 1) {
            throw new Exception("Invalid amount, amount should be 1 or more.");
            return false;
        }
        if (!is_numeric($amount) || $amount < 1 || !is_numeric($phone)) {
            throw new Exception("Invalid phone number, phone number should be in the format 254xxxxxxxx");
            return false;
        }
        $timestamp = date('YmdHis');
        $passwd = base64_encode($this->lipa_na_mpesa . $this->lipa_na_mpesa_key . $timestamp);
        $data = array(
            'BusinessShortCode' => $this->lipa_na_mpesa,
            'Password' => $passwd,
            'Timestamp' => $timestamp,
            'TransactionType' => 'CustomerPayBillOnline',
            'Amount' => $amount,
            'PartyA' => $phone,
            'PartyB' => $this->lipa_na_mpesa,
            'PhoneNumber' => $phone,
            'CallBackURL' => $this->lnmocallback,
            'AccountReference' => $ref,
            'TransactionDesc' => $desc,
        );
        // Log::info($data);
        $data = json_encode($data);
        $url = $this->base_url . '/mpesa/stkpush/v1/processrequest';
        $response = $this->submit_request($url, $data);

        if (isset($response)) {
            return $response;
        } else {
            return false;
        }
        print_r($response);
    }

    public function sanitizePhone($phone)
    {
        if (strlen($phone) >= 9) {
            if (strlen($phone) == 12) {
                return $phone;
            } else {
                if (str_starts_with($phone, '0')) {
                    return '254' . substr($phone, 1);
                } else if (str_starts_with($phone, '+')) {
                    return substr($phone, 1);
                } else {
                    return '254' . $phone;
                }
            }
        }
        return "error";
    }

    // C2B Register Url
    public function c2bRegisterUrls()
    {
        $request_data = array(
            'ShortCode' => $this->paybill,
            'ResponseType' => 'Completed',
            'ConfirmationURL' => $this->cbconfirm,
            'ValidationURL' => $this->cbvalidate
        );
        $data = json_encode($request_data);
        //header('Content-Type: application/json');

        $url = $this->base_url . '/mpesa/c2b/v2/registerurl';
        $response = $this->submit_request($url, $data);
        //\Log::info($response);
        return $response;
    }

    public function getToken()
    {
        $credentials = base64_encode('lNmsO56Yvr8zZdstB7Lu3wV1mg0Pm7eA:YnzRQq1b4el6JKmN');
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_URL, $this->base_url . '/oauth/v1/generate?grant_type=client_credentials');
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, array('Authorization: Basic ' . $credentials, 'Content-Type: application/json'));
        $response = curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);
        $response = json_decode($response);

        if ($info["http_code"] == 200) {
            $access_token = $response->access_token;
            $this->access_token = $access_token;
            return $access_token;
        } else {
            //throw new Exception("Invalid Consumer key or secret");
            return false;
        }
    }

    public function registerURL()
    {
        $request_data = array(
            'ShortCode' => '208719',
            'ResponseType' => 'Completed',
            'ConfirmationURL' => 'https://etechglobalofficesolution.com/payment-confirmation',
            'ValidationURL' => 'https://etechglobalofficesolution.com/payment-confirmation'
        );
        $data = json_encode($request_data);
        //header('Content-Type: application/json');

        $url = $this->base_url . '/mpesa/c2b/v2/registerurl';

        $access_token = $this->getToken();


        if ($access_token != '' || $access_token !== false) {
            $curl = curl_init();
            curl_setopt($curl, CURLOPT_URL, $url);
            curl_setopt($curl, CURLOPT_HTTPHEADER, array('Content-Type: application/json', 'Authorization: Bearer ' . $access_token));

            curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($curl, CURLOPT_POST, true);
            curl_setopt($curl, CURLOPT_POSTFIELDS, $data);

            $response = curl_exec($curl);
            curl_close($curl);
            return $response;
        } else {
            return false;
        }
    }

    // Simulate C2B
    public function simulateC2B($amount, $msisdn, $ref)
    {
        $data = array(
            'ShortCode' => $this->paybill,
            'CommandID' => 'CustomerPayBillOnline',
            'Amount' => $amount,
            'Msisdn' => $msisdn,
            'BillRefNumber' => $ref
        );
        $data = json_encode($data);
        $url = $this->base_url . '/c2b/v1/simulate';
        $response = $this->submit_request($url, $data);
        return $response;
    }


    // Check Balance
    public function check_balance()
    {
        $data = array(
            'CommandID' => 'AccountBalance',
            'PartyA' => $this->lipa_na_mpesa,
            'IdentifierType' => '4',
            'Remarks' => 'Balance',
            'Initiator' => $this->initiator_username,
            'SecurityCredential' => $this->cred,
            'QueueTimeOutURL' => $this->baltimeout,
            'ResultURL' => $this->balresult
        );
        $data = json_encode($data);
        $url = $this->base_url . '/mpesa/accountbalance/v1/query';
        $response = $this->submit_request($url, $data);
        return $response;
    }

    // Status Request
    public function status_request($transaction = 'LH7819VXPE')
    {
        $data = array(
            'CommandID' => 'TransactionStatusQuery',
            'PartyA' => $this->paybill,
            'IdentifierType' => 4,
            'Remarks' => 'Testing API',
            'Initiator' => $this->initiator_username,
            'SecurityCredential' => $this->cred,
            'QueueTimeOutURL' => $this->statustimeout,
            'ResultURL' => $this->statusresult,
            'TransactionID' => $transaction,
            'Occassion' => 'Test'
        );
        $data = json_encode($data);
        $url = $this->base_url . '/mpesa/transactionstatus/v1/query';
        $response = $this->submit_request($url, $data);
        return $response;
    }

    // Reverse Trans
    public function reverse_transaction($receiver, $trx_id, $amount)
    {
        $data = array(
            'CommandID' => 'TransactionReversal',
            'ReceiverParty' => $this->test_msisdn,
            'RecieverIdentifierType' => 1, //1=MSISDN, 2=Till_Number, 4=Shortcode
            'Remarks' => 'Testing',
            'Amount' => $amount,
            'Initiator' => $this->initiator_username,
            'SecurityCredential' => $this->cred,
            'QueueTimeOutURL' => $this->reversetimeout,
            'ResultURL' => $this->reverseresult,
            'TransactionID' => 'LIE81C8EFI'
        );
        $data = json_encode($data);
        $url = $this->base_url . '/mpesa/reversal/v1/request';
        $response = $this->submit_request($url, $data);
        return $response;
    }

    // LNM Query
    private function lnmoQuery($checkoutRequestID = null)
    {
        $timestamp = date('YmdHis');
        $passwd = base64_encode($this->lipa_na_mpesa . $this->lipa_na_mpesa_key . $timestamp);

        if ($checkoutRequestID == null || $checkoutRequestID == '') {
            //throw new Exception("Checkout Request ID cannot be null");
            return false;
        }

        $data = array(
            'BusinessShortCode' => $this->lipa_na_mpesa,
            'Password' => $passwd,
            'Timestamp' => $timestamp,
            'CheckoutRequestID' => $checkoutRequestID
        );
        $data = json_encode($data);
        $url = $this->base_url . '/mpesa/stkpushquery/v1/query';
        $response = $this->submit_request($url, $data);
        return $response;
    }
}
