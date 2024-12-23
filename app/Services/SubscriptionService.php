<?php

namespace App\Services;

use App\Jobs\SubscriptionJob;
use App\Models\EmailTemplate;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use Exception;
use Illuminate\Support\Facades\DB;
use Stripe\StripeClient;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class SubscriptionService
{

     protected StripeClient $stripeClient;

    public function __construct(StripeClient $stripeClient)
    {
        $this->stripeClient = $stripeClient;
    }

    public function new(Plan $plan)
    {
        $user = auth()->user();
        $user->createOrGetStripeCustomer();
        
        $session = $this->stripeClient->checkout->sessions->create([
            'customer' => $user->stripe_id,
            'success_url' => route('process-subscription') . '?session_id={CHECKOUT_SESSION_ID}&plan=' . $plan->id,
            'mode' => 'subscription',
            'payment_method_types' => ['card'],
            'line_items' => [[
                'price' => $plan->stripe_plan,
                'quantity' => 1,
            ]],
        ]);

        Payment::create([
            'payment_id' => $session->id,
            'user_id' => auth()->user()->id,
            'plan_id' => $plan->id,
            'amount' => $plan->price,
            'status' => 'unpaid',
        ]);

        return $session['url'];

       
    }


    public function process($request)
    {
        DB::beginTransaction();
        if ($request->get('session_id') != '') {
            $sessionId = $request->get('session_id');
            $session = $this->stripeClient->checkout->sessions->retrieve($sessionId);

            if (!$session) {
                throw new NotFoundHttpException;
            }

            $oldSubscription = Subscription::where('stripe_id', $session->subscription)->first();
            if ($oldSubscription) {
                return;
            }

            $order = Payment::where('payment_id', $session->id)->first();

    
            if (!$order) {
                throw new NotFoundHttpException();
            }
            if ($order->status === 'unpaid') {
                $order->status = $session->payment_status;
                $order->save();
            }
            $user = auth()->user();
            $plan = Plan::find($request->get('plan'));
            if ($session->subscription) {
                $current_subscription =  Subscription::where('user_id', auth()->id())
                    ->where(function ($query) {
                        $query->where('stripe_status', 'complete')
                            ->orWhere('stripe_status', 'active');
                    })
                    ->get();

                if(count($current_subscription) > 0) {

                    foreach ($current_subscription as $sub) {

                        $result = $this->stripeClient->subscriptions->cancel($sub->stripe_id, []);

                        if ($result) {

                            $sub->stripe_status = $result->status;
                            $sub->save();
                        }
                    }
                }
                $billingCycle = $session->expires_at;
                if (isset($session->subscription)) {
                    $subscription = $this->stripeClient->subscriptions->retrieve($session->subscription);
                    $billingCycle = $subscription->current_period_end;
                }
                $subscription = new Subscription();
                $subscription->user_id = $user->id;
                $subscription->type = $plan->product_id;
                $subscription->plan_id = $plan->id;
                $subscription->stripe_id = $session->subscription;
                $subscription->stripe_status = $session->status;
                $subscription->stripe_price = $plan->stripe_plan;
                $subscription->quantity = 1;
                $subscription->ends_at = date('Y-m-d h:i:s', $billingCycle);
                $subscription->invoice = $session->invoice;
                $subscription->save();

                $order->subscription_id = $subscription->id;
                $order->save();

                DB::commit();
                
                $mailData = [
                    'user_name'  => $user->name,
                    'user_email' => $user->email,
                    'body'       => 'You have been successfully subscribed to plan (' .$plan->name. ')',
                ];
        
                try {
                    $emailTemplateKeyUser = 'Subscription';
                    if ($emailTemplateKeyUser) {
                        $emailTemplate = EmailTemplate::where('key', $emailTemplateKeyUser)->first();
                        if ($emailTemplate && $emailTemplate->status) {
                            try {
                                SubscriptionJob::dispatch($mailData, $emailTemplate, 'Confirmed')->delay(now()->addSeconds(10));
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
                        $mailData['body'] =  $user->name .' have been successfully subscribed to plan ('.$plan->name.')';
                        try {
                            $emailTemplateKeyUser = 'Subscription';
                            if ($emailTemplateKeyUser) {
                                $emailTemplate = EmailTemplate::where('key', $emailTemplateKeyUser)->first();
                                if ($emailTemplate && $emailTemplate->status) {
                                    try {
                                        SubscriptionJob::dispatch($mailData, $emailTemplate, 'Confirmed')->delay(now()->addSeconds(10));
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

        }
        DB::commit();

    }

    public function cancel(Subscription $subscription)
    {
        $result = $this->stripeClient->subscriptions->cancel($subscription->stripe_id, []);

        if ($result) {

            $subscription->stripe_status = $result->status;
            $subscription->save();

            return redirect()->back();
        } else {

            return redirect()->back();
        }
    }

    public function downloadInvoice($id)
    {
        $result = $this->stripeClient->invoices->retrieve($id, []);

        if($result) {
            
            return response()->json(['invoice' => $result->invoice_pdf]);

        } else {
            
            return redirect()->back();
        }
    }
}
