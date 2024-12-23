<?php

use App\Http\Controllers\CartController;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OpenAIController;
use App\Http\Controllers\PaypalPaymentController;
use App\Http\Controllers\StripePaymentController;
use App\Http\Controllers\SubscriptionController;
use Filament\Http\Middleware\Authenticate;
use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Inertia\Inertia;

require __DIR__ . '/settings.php';

Route::get('/', function () {
    return view('welcome');
});

Route::post('/chat', [OpenAIController::class, 'chat']);

Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::post('/contact', [HomeController::class, 'storeInquiry'])->name('contact-store');

Route::post('/stripe/webhook', [SubscriptionController::class, 'handleWebhook']);

Route::group(['middleware' => [Authenticate::class]], function () {

    Route::get('new-subscription/{plan}', [SubscriptionController::class, 'new'])->name('new-subscription');
    Route::get('process-subscription', [SubscriptionController::class, 'process'])->name('process-subscription');
    Route::get('cancel-subscription/{subscription}', [SubscriptionController::class, 'cancel'])->name('cancel-subscription');
    Route::get('subscription-history', [SubscriptionController::class, 'history'])->name('subscription-history');
    Route::get('download-invoice/{id}', [SubscriptionController::class, 'downloadInvoice'])->name('download-invoice');

    Route::post('/cart/add', [CartController::class, 'addToCart'])->name('add-to-cart');
    Route::post('/cart/remove/{cart}', [CartController::class, 'removeFromCart'])->name('remove-from-cart');
    Route::get('/cart', [CartController::class, 'viewCart'])->name('view-cart');

    Route::post('/stripe-payment', [StripePaymentController::class, 'create'])->name('stripe.payment');
    Route::get('/stripe-payment-success', [StripePaymentController::class, 'paymentSuccess'])->name('stripe.payment.success');

    Route::post('paypal-payment', [PaypalPaymentController::class, 'create'])->name('paypal.payment');
    Route::get('paypal-payment-success', [PaypalPaymentController::class, 'paymentSuccess'])->name('paypal.payment.success');
});

//Error page added
Route::fallback(function (Request $request) {
    return Inertia::render('ErrorPage', [
        'status' => $request->query('code'),
        'message' => $request->query('message') ? $request->query('message') : 'Page not found!',
    ]);
})->name('error.page');
