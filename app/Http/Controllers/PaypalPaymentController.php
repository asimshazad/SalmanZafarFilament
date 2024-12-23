<?php

namespace App\Http\Controllers;

use App\Services\PaypalPaymentService;
use Illuminate\Http\Request;
use Inertia\Inertia;

class PaypalPaymentController extends Controller
{
    private PaypalPaymentService $paypalPaymentService;

    public function __construct(PaypalPaymentService $paypalPaymentService)
    {
        $this->paypalPaymentService = $paypalPaymentService;
    }

    public function create(Request $request) {
        $response = $this->paypalPaymentService->createPayment($request->all());
        return response()->json(['sessionURL' => $response]);
    }

    public function paymentSuccess(Request $request) {
        $this->paypalPaymentService->paymentSuccess($request);
        return redirect()->route('home', ['message' => 'thankyou']);
       // return Inertia::render('Plan/Success');
    }
}
