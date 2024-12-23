<?php

namespace App\Http\Controllers;


use App\Jobs\SubscriptionJob;
use App\Models\EmailTemplate;
use App\Models\Payment;
use App\Models\Plan;
use App\Models\Subscription;
use App\Models\User;
use App\Services\SubscriptionService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Stripe\StripeClient;
use Stripe\Event;
use Laravel\Cashier\Events\WebhookReceived;
use Stripe\Webhook;

class SubscriptionController extends Controller
{

    private SubscriptionService $subscriptionService;
    protected StripeClient $stripeClient;
 
    public function __construct(SubscriptionService $subscriptionService, StripeClient $stripeClient)
    {
        $this->subscriptionService = $subscriptionService;
        $this->stripeClient = $stripeClient;
    }

    public function new(Plan $plan)
    {
        $response = $this->subscriptionService->new($plan); 
        return response()->json(['sessionURL' => $response]);
    }

    public function process(Request $request)
    {
        $this->subscriptionService->process($request); 
        return redirect()->route('home', ['message' => 'thankyou']);
       // return Inertia::render('Plan/Success');

    }

    public function cancel(Subscription $subscription)
    {
       $result = $this->subscriptionService->cancel($subscription);
    }


    public function history()
    {
        $subscriptions = Subscription::with('plan')->where('user_id',auth()->id())->get();

        return Inertia::render('Subscriptions/History',[
            'subscriptions' => $subscriptions
        ]);
    }

    public function downloadInvoice($id)
    {
        $response = $this->subscriptionService->downloadInvoice($id);
        return $response;
    }

    public function handleWebhook(Request $request)
    {
        $endpointSecret = env('STRIPE_WEBHOOK_SECRET');

        $payload = $request->getContent();
        $sigHeader = $request->header('Stripe-Signature');
        try {
            $event = Webhook::constructEvent(
                $payload, $sigHeader, $endpointSecret
            );
        } catch (\UnexpectedValueException $e) {
            return response('Invalid signature', 400);
        }
        switch ($event->type) {
            case 'invoice.payment_succeeded':
                \Log::info('invoice.payment_succeeded');
                $this->handleSubscriptionPayment($event->data->object);
               break;
            case 'customer.subscription.deleted':
                \Log::info('customer.subscription.deleted');
                $this->handleSubscriptionDeleted($event->data->object);
                break;
            case 'invoice.payment_failed':
                \Log::info('invoice.payment_failed');
                $this->handleSubscriptionExpired($event->data->object);
                break;
            default:
                break;
        }

        return response('Success', 200);
    }

    protected function handleSubscriptionPayment($invoice)
    {
        if (isset($invoice->subscription)) {
            $subscriptionId = $invoice->subscription;

            $subscription = $this->stripeClient->subscriptions->retrieve($subscriptionId);
            if ($subscription) {
                $localSubscription = Subscription::with('plan')->where('stripe_id', $subscriptionId)->first();
                if ($localSubscription) {
                    $previousPayment = Payment::where('subscription_id', $localSubscription->id)->latest()->first();
                    if ($previousPayment && !$previousPayment->created_at->isSameDay(Carbon::now())) {
                        $newPayment = $previousPayment->replicate();
                        $newPayment->created_at = Carbon::now();
                        $newPayment->updated_at = Carbon::now();
                        $newPayment->save();

                        $localSubscription->stripe_status = $subscription->status;
                        $localSubscription->ends_at =  date('Y-m-d h:i:s', $subscription->current_period_end);
                        $localSubscription->updated_at = Carbon::now();
                        $localSubscription->save();

                        $user = User::find($localSubscription->user_id);

                        $mailData = [
                            'user_name'     => $user->name,
                            'user_email'    => $user->email,
                            'body'          => 'Recurring payment of amount $' . $previousPayment->amount . ' deducted from your account for plan: ' . $localSubscription->plan->name,
                        ];
                        try {
                            $emailTemplateKeyUser = 'Subscription';
                            if ($emailTemplateKeyUser) {
                                $emailTemplate = EmailTemplate::where('key', $emailTemplateKeyUser)->first();
                                if ($emailTemplate && $emailTemplate->status) {
                                    try {
                                        SubscriptionJob::dispatch($mailData, $emailTemplate, 'Repayment')->delay(now()->addSeconds(10));
                                    } catch (Exception $e) {
                                        //
                                    }
                                }
                            }
                        } catch (Exception $e) {
                            //
                        }

                        $admin = User::whereHas('roles', function ($query) {
                            $query->where('name', 'Admin');
                        })->first();

                        if ($admin) {
                            $mailData['user_name'] = $admin->name;
                            $mailData['user_email'] = $admin->email;
                            $mailData['body'] = 'Recurring payment of amount $' . $previousPayment->amount . ' received from ' . $user->name . ' for plan: ' . $localSubscription->plan->name;
                            try {
                                $emailTemplateKeyUser = 'Subscription';
                                if ($emailTemplateKeyUser) {
                                    $emailTemplate = EmailTemplate::where('key', $emailTemplateKeyUser)->first();
                                    if ($emailTemplate && $emailTemplate->status) {
                                        try {
                                            SubscriptionJob::dispatch($mailData, $emailTemplate, 'Repayment')->delay(now()->addSeconds(10));
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
        }
    }

    protected function handleSubscriptionDeleted($subscription)
    {
        if ($subscription) {
            $localSubscription = Subscription::where('stripe_id', $subscription->id)->first();
            if ($localSubscription) {
                $localSubscription->stripe_status = $subscription->status;
                $localSubscription->save();
                $user = User::find($localSubscription->user_id);
                $mailData = [
                    'user_name'     => $user->name,
                    'user_email' =>  $user->email,
                    'body'          => 'Your subscription has been cancelled against plan: ' . $localSubscription->plan->name,
                ];
                try {
                    $emailTemplateKeyUser = 'Subscription';
                    if ($emailTemplateKeyUser) {
                        $emailTemplate = EmailTemplate::where('key', $emailTemplateKeyUser)->first();
                        if ($emailTemplate && $emailTemplate->status) {
                            try {
                                SubscriptionJob::dispatch($mailData, $emailTemplate, 'Cancelled')->delay(now()->addSeconds(10));
                            } catch (Exception $e) {
                                //
                            }
                        }
                    }
                } catch (Exception $e) {
                    //
                }
                $admin = User::whereHas('roles', function ($query) {
                    $query->where('name', 'Admin');
                })->first();
                if ($admin) {
                    $mailData['user_name'] = $admin->name;
                    $mailData['user_email'] = $admin->email;
                    $mailData['body'] =   $user->name . ' cancelled the subscription againt plan: ' . $localSubscription->plan->name;
                    try {
                        $emailTemplateKeyUser = 'Subscription';
                        if ($emailTemplateKeyUser) {
                            $emailTemplate = EmailTemplate::where('key', $emailTemplateKeyUser)->first();
                            if ($emailTemplate && $emailTemplate->status) {
                                try {
                                    SubscriptionJob::dispatch($mailData, $emailTemplate, 'Cancelled')->delay(now()->addSeconds(10));
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

    protected function handleSubscriptionExpired($invoice)
    {
        if (isset($invoice->subscription)) {
            $subscriptionId = $invoice->subscription;

            $localSubscription = Subscription::with('plan')->where('stripe_id', $subscriptionId)->first();

            if ($localSubscription) {
                $localSubscription->stripe_status = 'expired';
                $localSubscription->save();

                $user = User::find($localSubscription->user_id);
                $mailData = [
                    'user_name' => $user->name,
                    'user_email' => $user->email,
                    'body' => 'Your subscription for plan: ' . $localSubscription->plan->name . ' has expired due to payment failure.',
                ];
                try {
                    $emailTemplateKeyUser = 'Subscription';
                    if ($emailTemplateKeyUser) {
                        $emailTemplate = EmailTemplate::where('key', $emailTemplateKeyUser)->first();
                        if ($emailTemplate && $emailTemplate->status) {
                            try {
                                SubscriptionJob::dispatch($mailData, $emailTemplate, 'Expired')->delay(now()->addSeconds(10));
                            } catch (Exception $e) {
                                //
                            }
                        }
                    }
                } catch (Exception $e) {
                    //
                }

                $admin = User::whereHas('roles', function ($query) {
                    $query->where('name', 'Admin');
                })->first();

                if ($admin) {
                    $mailData['user_name'] = $admin->name;
                    $mailData['user_email'] = $admin->email;
                    $mailData['body'] = $user->name . '\'s subscription for plan: ' . $localSubscription->plan->name . ' has expired due to payment failure.';
                    try {
                        $emailTemplateKeyUser = 'Subscription';
                        if ($emailTemplateKeyUser) {
                            $emailTemplate = EmailTemplate::where('key', $emailTemplateKeyUser)->first();
                            if ($emailTemplate && $emailTemplate->status) {
                                try {
                                    SubscriptionJob::dispatch($mailData, $emailTemplate, 'Expired')->delay(now()->addSeconds(10));
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


}
