<?php

namespace App\Http\Controllers;

use App\Services\StripePaymentService;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Stripe\Stripe;

class StripePaymentController extends Controller
{
    private StripePaymentService $stripePaymentService;

    public function __construct(StripePaymentService $stripePaymentService)
    {
        $this->stripePaymentService = $stripePaymentService;
        Stripe::setApiKey(config('services.stripe.secret'));
    }

    public function create(Request $request) {
        $response = $this->stripePaymentService->createPayment($request->all());
        return response()->json(['sessionURL' => $response]);
    }

    public function paymentSuccess(Request $request) {
        $response = $this->stripePaymentService->paymentSuccess($request);
        return redirect()->route('home', ['message' => 'thankyou']);
       // return Inertia::render('Plan/Success');
    }
}
