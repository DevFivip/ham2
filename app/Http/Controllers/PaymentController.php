<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use Illuminate\Http\Request;
use Omnipay\Omnipay;

class PaymentController extends Controller
{
    private $gateway;

    public function __construct()
    {
        $this->gateway = Omnipay::create('PayPal_Rest');
        $this->gateway->setClientId("Af6almIftzu6V1ZolnBOrCnvEEHZsycvAsuEoonfiUMO5H2c_i2g_HtdYjfHAUAeVbG9f7nLAY0lUIlf");
        $this->gateway->setSecret("EOSV6a_bX4Qhe8cKWTBQ3sJHYzihV-ZUkPaLdXR3gANDKR36LpcX9F1OlcWWqB9C5UvCdnUJIruPmXvG");
        $this->gateway->setTestMode(true);
    }

    public function pay($total)
    {
        try {
            // dd($total);
            $response = $this->gateway->purchase(array(
                'amount' => $total,
                'currency' => 'USD',
                'returnUrl' => url('success'),
                'cancelUrl' => url('error')
            ))->send();

            // dd($response);
            // error_log(json_encode($response));
            // return $response;
            // dd($response);

             if ($response->isRedirect()) {

                return $response->redirect();

            }
            // else{
            //     return $response->getMessage();
            // }

        } catch (\Throwable $th) {
            error_log($th->getMessage());
            return $th->getMessage();
        }
    }

    public function success(Request $request)
    {
        if ($request->input('paymentId') && $request->input('PayerID')) {
            $transaction = $this->gateway->completePurchase(array(
                'payer_id' => $request->input('PayerID'),
                'transactionReference' => $request->input('paymentId')
            ));

            $response = $transaction->send();

            if ($response->isSuccessful()) {

                $arr = $response->getData();

                $payment = new Payment();
                $payment->payment_id = $arr['id'];
                $payment->payer_id = $arr['payer']['payer_info']['payer_id'];
                $payment->payer_email = $arr['payer']['payer_info']['email'];
                $payment->amount = $arr['transactions'][0]['amount']['total'];
                $payment->currency = env('PAYPAL_CURRENCY');
                $payment->payment_status = $arr['state'];

                $payment->save();

                return "Payment is Successfull. Your Transaction Id is : " . $arr['id'];
            } else {
                return $response->getMessage();
            }
        } else {
            return 'Payment declined!!';
        }
    }

    public function error()
    {
        return 'User declined the payment!';
    }
}
