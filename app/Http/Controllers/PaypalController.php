<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\User;

class PaypalController extends Controller
{
    public function paypal($precio)
    {
        dd($precio);
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $paypalToken = $provider->getAccessToken();
        $response = $provider->createOrder([
            "intent" => "CAPTURE",
            "application_context" => [
                "return_url" => route('success'),
                "cancel_url" => route('cancel')
            ],
            "purchase_units" => [
                [
                    "amount" => [
                        "currency_code" => "USD",
                        "value" => $precio
                    ]
                ]
            ]
        ]);

        dd($response);

        // if (isset($response['id']) && $response['id'] != null) {
        //     foreach ($response['links'] as $link) {
        //         if ($link['rel'] === 'approve') {
        //             session()->put('product_name', $request->product_name);
        //             session()->put('quantity', $request->quantity);
        //             return redirect()->away($link['href']);
        //         }
        //     }
        // } else {
        //     return redirect()->route('cancel');
        // }
    }
    public function success(Request $request)
    {
        $provider = new PayPalClient;
        $provider->setApiCredentials(config('paypal'));
        $paypalToken = $provider->getAccessToken();
        $response = $provider->capturePaymentOrder($request->token);
        //dd($response);
        if (isset($response['status']) && $response['status'] == 'COMPLETED') {
            
            // Insert data into database
            $payment = new Payment;
            $payment->payment_id = $response['id'];
            $payment->product_name = session()->get('product_name');
            $payment->quantity = session()->get('quantity');
            $payment->user_id = session()->get('user_id');
            $payment->amount = $response['purchase_units'][0]['payments']['captures'][0]['amount']['value'];
            $payment->currency = $response['purchase_units'][0]['payments']['captures'][0]['amount']['currency_code'];
            $payment->payer_name = $response['payer']['name']['given_name']." ".$response['payer']['name']['surname'];
            $payment->payer_email = $response['payer']['email_address'];
            $payment->payment_status = $response['status'];
            $payment->payment_method = "PayPal";
            $payment->save();

            unset($_SESSION['product_id']);
            unset($_SESSION['product_name']);
            unset($_SESSION['quantity']);
            unset($_SESSION['user_id']);

            //activar plan

            $plan = Plan::find(session()->get('product_id'));
            $subscriber = User::find(session()->get('user_id'));
            $subscriber->subscribeTo($plan);

            return "Payment is successful and subscription success";
        } else {
            return redirect()->route('cancel');
        }
    }
    public function cancel()
    {
        return "Payment is cancelled.";
    }
}
