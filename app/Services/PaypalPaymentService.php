<?php

namespace App\Services;

use App\Jobs\PaymentJob;
use App\Models\Cart;
use App\Models\EmailTemplate;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Srmklive\PayPal\Services\PayPal as PayPalClient;
use Exception;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class PaypalPaymentService
{

    public function createPayment(array $data)
    {
        try {
            $user = auth()->user();
            $cart = Cart::where('user_id', $user->id)->with('itemable')->get();
            $names = $cart->map(fn($item) => $item->itemable->name)->join(', ');
            $totalAmount = 0;
            foreach ($cart as $item) {
                $totalAmount += ($item->itemable->price - $item->itemable->discount);
            }
            $provider = new PayPalClient();
            $provider->setApiCredentials(config('paypal'));
            $paypalToken = $provider->getAccessToken();
            $response = $provider->createOrder([
                "intent" => "CAPTURE",
                "application_context" => [
                    "return_url" => route('paypal.payment.success'),
                    "cancel_url" => route('view-cart'),
                ],
                "purchase_units" => [
                    0 => [
                        "amount" => [
                            "currency_code" => "USD",
                            "value" => $totalAmount
                        ],
                    ]
                ]
            ]);
            if (isset($response['id']) && $response['id'] != null) {
                foreach ($response['links'] as $links) {
                    if ($links['rel'] == 'approve') {
                        return $links['href'];
                    }
                }
                throw new NotFoundHttpException();
            } else {
                throw new NotFoundHttpException();
    
            }
        } catch (Exception $exception) {
            DB::rollBack();
            throw new Exception($exception->getMessage());
        }
    }

    public function paymentSuccess($request)
    {
        DB::beginTransaction();
        if ($request->get('token') != '') {
            $provider = new PayPalClient;
            $provider->setApiCredentials(config('paypal'));
            $paypalToken = $provider->getAccessToken();
            $response = $provider->capturePaymentOrder($request['token']);
            $user = auth()->user();
            $cart = Cart::where('user_id', $user->id)->with('itemable')->get();
            $totalAmount = 0;
            foreach ($cart as $item) {
                $totalAmount += ($item->itemable->price - $item->itemable->discount);
            }
            $payment = Payment::create([
                'payment_id' => $response['id'],
                'user_id' => $user->id,
                'amount' => $totalAmount,
                'status' => 'paid',
                'type' => 'full_payment',
                'method' => 'paypal',
                'response_json' => $response
            ]);

            $names = $cart->map(fn($item) => $item->itemable->name)->join(', ');
            $productIds = $cart->pluck('itemable.id')->toArray();
            $payment->products()->attach($productIds);
            $user->products()->attach($productIds);
            Cart::where('user_id', $user->id)->delete();
            DB::commit();
            $mailData = [
                'user_name'  => $user->name,
                'user_email' => $user->email,
                'body'       => 'You have successfully paid $'. $payment->amount .' (via PayPal) for product(s): ' . $names,
            ];
    
            try {
                $emailTemplateKeyUser = 'Payment';
                if ($emailTemplateKeyUser) {
                    $emailTemplate = EmailTemplate::where('key', $emailTemplateKeyUser)->first();
                    if ($emailTemplate && $emailTemplate->status) {
                        try {
                            PaymentJob::dispatch($mailData, $emailTemplate, 'PayPal Payment Success')->delay(now()->addSeconds(10));
                        } catch (Exception $e) {
                            //
                        }
                    }
                }
            } catch (Exception $e) {
                //
            }
            if (!$user->roles()->where('name', 'Admin')->exists()) {
                $admin = User::whereHas('roles', function ($query) {
                    $query->where('name', 'Admin');
                })->first();
                if ($admin) {
                    $mailData['user_name'] = $admin->name;
                    $mailData['user_email'] = $admin->email;
                    $mailData['body'] =  $user->name .' have been successfully paid $'. $payment->amount .' (via PayPal) for product(s): ' . $names;
                    try {
                        $emailTemplateKeyUser = 'Payment';
                        if ($emailTemplateKeyUser) {
                            $emailTemplate = EmailTemplate::where('key', $emailTemplateKeyUser)->first();
                            if ($emailTemplate && $emailTemplate->status) {
                                try {
                                    PaymentJob::dispatch($mailData, $emailTemplate, 'PayPal Payment Success')->delay(now()->addSeconds(10));
                                } catch (Exception $e) {
                                    //
                                }
                            }
                        }
                    } catch (Exception $e) {
                        //
                    }
                }
            }

        }
        DB::commit();

    }
}
