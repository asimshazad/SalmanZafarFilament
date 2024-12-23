<?php

namespace App\Models;

use App\Traits\SettingPhoto;
use Illuminate\Database\Eloquent\Model;

class Setting extends Model
{
    use SettingPhoto;

    protected $fillable = [
        'name',
        'email',
        'site_title',
        'site_logo',
        'footer_logo',
        'favicon',
        'mail_mailer',
        'mail_host',
        'mail_port',
        'mail_username',
        'mail_password',
        'mail_from_name',
        'mail_from_address',
        'paypal_mode',
        'paypal_client_id',
        'paypal_client_secret',
        'paypal_webhook_secret',
        'paypal_app_id',
        'stripe_key',
        'stripe_secret',
        'stripe_webhook_secret',
        'open_api_assistant_id',
        'open_api_key',
        'open_api_organiztion',
        'gemini_api_key',
    ];

    protected $appends = [
        'site_logo_url',
        'site_footer_logo_url',
        'favicon_url',
    ];
}
