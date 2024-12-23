<?php

namespace App\Services;

use App\Jobs\PaymentJob;
use App\Models\Cart;
use App\Models\EmailTemplate;
use App\Models\Payment;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Stripe\StripeClient;
use Stripe\Checkout\Session;
use Exception;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class StripePaymentService
{
    protected StripeClient $stripeClient;

    public function __construct(StripeClient $stripeClient)
    {
        $this->stripeClient = $stripeClient;
    }

    public function createPayment($data)
    {
        try {
            $user = auth()->user();
            $cart = Cart::where('user_id', $user->id)->with('itemable')->get();
            if (!$user->stripe_id) {
                $user->createAsStripeCustomer();
            } else {
                try {
                    $this->stripeClient->customers->retrieve(
                        $user->stripe_id,
                        []
                    );
                } catch (\Stripe\Exception\InvalidRequestException $e) {
                    $user->stripe_id = null;
                    $user->createAsStripeCustomer();
                }
            }

            $totalAmount = 0;
            foreach ($cart as $item) {
                $line_items[] = [
                    'price_data' => [
                        'currency' => 'usd',
                        'product_data' => [
                            'name' => $item->itemable->name,
                        ],
                        'unit_amount' => ($item->itemable->price - $item->itemable->discount) * 100,
                    ],
                    'quantity' => 1,

                ];
                $totalAmount += ($item->itemable->price - $item->itemable->discount);
            }
            $stripeSession = Session::create([
                'customer' => $user->stripe_id,
                'payment_method_types' => ['card'],
                'line_items' => $line_items,
                'mode' => 'payment',
                'success_url' => route('stripe.payment.success') . "?session_id={CHECKOUT_SESSION_ID}",
                'cancel_url' => route('view-cart'),
            ]);
            $payment = Payment::create([
                'payment_id' => $stripeSession->id,
                'user_id' => $user->id,
                'amount' => $totalAmount,
                'status' => 'unpaid',
                'type' => 'full_payment',
                'method' => 'stripe',
            ]);

            return $stripeSession['url'];
        } catch (Exception $exception) {
            //db transaction rollback
            DB::rollBack();

            //throw exception
            throw new Exception($exception->getMessage());
        }
    }

    public function paymentSuccess($request)
    {
        DB::beginTransaction();
        if ($request->get('session_id') != '') {
            $sessionId = $request->get('session_id');
            $session = $this->stripeClient->checkout->sessions->retrieve($sessionId);

            if (!$session) {
                throw new NotFoundHttpException();
            }

            $payment = Payment::where('payment_id', $session->id)->first();

    
            if (!$payment) {
                throw new NotFoundHttpException();
            }
            if ($payment->status === 'unpaid') {
                $payment->status = $session->payment_status;
                $payment->response_json = $session;
                $payment->save();
            }
            $user = auth()->user();
            $cart = Cart::where('user_id', $user->id)->with('itemable')->get();
            $names = $cart->map(fn($item) => $item->itemable->name)->join(', ');
            $productIds = $cart->pluck('itemable.id')->toArray();
            $payment->products()->attach($productIds);
            $user->products()->attach($productIds);
            Cart::where('user_id', $user->id)->delete();
            DB::commit();
            $mailData = [
                'user_name'  => $user->name,
                'user_email' => $user->email,
                'body'       => 'You have successfully paid $'. $payment->amount .' (via stripe) for product(s): ' . $names,
            ];
    
            try {
                $emailTemplateKeyUser = 'Payment';
                if ($emailTemplateKeyUser) {
                    $emailTemplate = EmailTemplate::where('key', $emailTemplateKeyUser)->first();
                    if ($emailTemplate && $emailTemplate->status) {
                        try {
                            PaymentJob::dispatch($mailData, $emailTemplate, 'Stripe Payment Success')->delay(now()->addSeconds(10));
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
                    $mailData['body'] =  $user->name .' have been successfully paid $'. $payment->amount .' (via stripe) for product(s): ' . $names;
                    try {
                        $emailTemplateKeyUser = 'Payment';
                        if ($emailTemplateKeyUser) {
                            $emailTemplate = EmailTemplate::where('key', $emailTemplateKeyUser)->first();
                            if ($emailTemplate && $emailTemplate->status) {
                                try {
                                    PaymentJob::dispatch($mailData, $emailTemplate, 'Stripe Payment Success')->delay(now()->addSeconds(10));
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
