<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Configuration extends Model
{
    use HasFactory;

    protected $fillable = [
        'app_name',
        'default_locale',
        'store_enabled',
        'send_mails_enabled',
        'easypay_enabled',
        'tax_enabled',
        'mail_admin',
        'mail_contact',
        'smtp_server_host',
        'smtp_server_port',
        'smtp_username',
        'smtp_password',
        'smtp_encryptation',
        'smtp_mail_from',
        'google_recaptcha_site_key',
        'google_recaptcha_secret_key',

        // Google social login (DB-backed)
        'google_socialite_enabled',
        'google_client_id',
        'google_client_secret',
        'google_redirect',

        // Microsoft social login (DB-backed)
        'microsoft_socialite_enabled',
        'microsoft_client_id',
        'microsoft_client_secret',
        'microsoft_redirect',
        'microsoft_tenant',

        'easypay_api_key',
        'easypay_id',
        'easypay_webhook_secret',
        'easypay_webhook_header',
        'easypay_webhook_user',
        'easypay_webhook_pass',
        'easypay_url_url',
        'easypay_sdk_url',
        'easypay_payment_methods',
        'easypay_session_ttl',
        'easypay_mb_ttl',
        'user_id',
    ];

    protected $casts = [
        'store_enabled' => 'boolean',
        'send_mails_enabled' => 'boolean',
        'easypay_enabled' => 'boolean',
        'tax_enabled' => 'boolean',
        'google_socialite_enabled' => 'boolean',
        'microsoft_socialite_enabled' => 'boolean',
        'easypay_session_ttl' => 'integer',
        'easypay_mb_ttl' => 'integer',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
