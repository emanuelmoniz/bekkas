<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Configuration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ConfigurationController extends Controller
{
    public function index()
    {
        $last = Configuration::latest()->first();

        return view('admin.configurations.index', ['config' => $last]);
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'app_name' => 'nullable|string',
            'default_locale' => 'nullable|string|max:10|in:' . implode(',', array_keys(config('app.locales', []))),
            'store_enabled' => 'nullable|boolean',
            'send_mails_enabled' => 'nullable|boolean',
            'easypay_enabled' => 'nullable|boolean',
            'tax_enabled' => 'nullable|boolean',

            // Google socialite
            'google_socialite_enabled' => 'nullable|boolean',
            'google_client_id' => 'nullable|string',
            'google_client_secret' => 'nullable|string',
            'google_redirect' => 'nullable|string',

            // Microsoft socialite
            'microsoft_socialite_enabled' => 'nullable|boolean',
            'microsoft_client_id' => 'nullable|string',
            'microsoft_client_secret' => 'nullable|string',
            'microsoft_redirect' => 'nullable|string',
            'microsoft_tenant' => 'nullable|string',

            'mail_admin' => 'nullable|string',
            'mail_contact' => 'nullable|string',
            'smtp_server_host' => 'nullable|string',
            'smtp_server_port' => 'nullable|string',
            'smtp_username' => 'nullable|string',
            'smtp_password' => 'nullable|string',
            'smtp_encryptation' => 'nullable|string',
            'smtp_mail_from' => 'nullable|string',
            'google_recaptcha_site_key' => 'nullable|string',
            'google_recaptcha_secret_key' => 'nullable|string',
            'easypay_api_key' => 'nullable|string',
            'easypay_id' => 'nullable|string',
            'easypay_webhook_secret' => 'nullable|string',
            'easypay_webhook_header' => 'nullable|string',
            'easypay_webhook_user' => 'nullable|string',
            'easypay_webhook_pass' => 'nullable|string',
            'easypay_url_url' => 'nullable|string',
            'easypay_sdk_url' => 'nullable|string',
            'easypay_payment_methods' => 'nullable|string',
            'easypay_session_ttl' => 'nullable|integer',
            'easypay_mb_ttl' => 'nullable|integer',
        ]);

        // Normalize boolean fields (checkboxes may be absent)
        $data['store_enabled'] = $request->has('store_enabled') ? (bool) $request->input('store_enabled') : false;
        $data['send_mails_enabled'] = $request->has('send_mails_enabled') ? (bool) $request->input('send_mails_enabled') : false;
        $data['easypay_enabled'] = $request->has('easypay_enabled') ? (bool) $request->input('easypay_enabled') : false;
        $data['tax_enabled'] = $request->has('tax_enabled') ? (bool) $request->input('tax_enabled') : false;
        $data['google_socialite_enabled'] = $request->has('google_socialite_enabled') ? (bool) $request->input('google_socialite_enabled') : false;
        $data['microsoft_socialite_enabled'] = $request->has('microsoft_socialite_enabled') ? (bool) $request->input('microsoft_socialite_enabled') : false;

        $last = Configuration::latest()->first();

        // Compare arrays — if identical, do nothing
        $compareFields = array_intersect_key($data, array_flip((new Configuration())->getFillable()));

        $identical = false;
        if ($last) {
            $lastData = $last->only(array_keys($compareFields));
            // stringify null vs empty for fair comparison
            $normalizedLast = array_map(fn($v) => $v === null ? '' : (string) $v, $lastData);
            $normalizedNew = array_map(fn($v) => $v === null ? '' : (string) $v, $compareFields);
            $identical = $normalizedLast === $normalizedNew;
        }

        if ($identical) {
            // nothing changed — do nothing
            return redirect()->back();
        }

        $compareFields['user_id'] = Auth::id();

        Configuration::create($compareFields);

        return redirect()->back()->with('success', 'Configuration saved.');
    }
}
