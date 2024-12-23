<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Config;
use Stripe\StripeClient;
use Illuminate\Support\Facades\View;
use App\Models\Setting;
use Inertia\Inertia;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->singleton(StripeClient::class, function() {
            return new StripeClient(Setting::first()->stripe_secret);
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Schema::defaultStringLength(191);

        $setting = Setting::first();

        if ($setting) {
            Config::set('services.stripe.key', $setting->stripe_key);
            Config::set('services.stripe.secret', $setting->stripe_secret);

            Config::set('aiclass.open_api_assistant_id', $setting->open_api_assistant_id);
            Config::set('aiclass.open_api_key', $setting->open_api_key);
            Config::set('aiclass.open_api_organiztion', $setting->open_api_organiztion);
            Config::set('aiclass.gemini_api_key', $setting->gemini_api_key);


            Config::set('cashier.key', $setting->stripe_key);
            Config::set('cashier.secret', $setting->stripe_secret);
            Config::set('cashier.webhook.secret', $setting->stripe_webhook_secret);

            Config::set('paypal.mode', $setting->paypal_mode);
            Config::set("paypal.$setting->paypal_mode.client_id", $setting->paypal_client_id);
            Config::set("paypal.$setting->paypal_mode.client_secret", $setting->paypal_client_secret);
            Config::set("paypal.$setting->paypal_mode.app_id", $setting->paypal_app_id);
            
            Config::set('mail.default', $setting->mail_mailer);
            if ($setting->mail_mailer == 'smtp') {
                Config::set("mail.mailers.$setting->mail_mailer.host", $setting->mail_host);
                Config::set("mail.mailers.$setting->mail_mailer.port", $setting->mail_port);
                Config::set("mail.mailers.$setting->mail_mailer.username", $setting->mail_username);
                Config::set("mail.mailers.$setting->mail_mailer.password", $setting->mail_password);
                Config::set("mail.mailers.$setting->mail_mailer.password", $setting->mail_password);
                Config::set("mail.mailers.$setting->mail_mailer.password", $setting->mail_password);
            }
            Config::set("mail.from.address", $setting->mail_from_address);
            Config::set("mail.from.name", $setting->mail_from_name);
        }

        // View::share('setting', $setting);

        // Inertia::share([
        //     'setting', $setting
        // ]);
    }
}
