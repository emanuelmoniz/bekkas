<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Application Configuration</h2>
    </x-slot>

    <div class="py-6 max-w-5xl mx-auto sm:px-6 lg:px-8">
        <form method="POST" action="{{ route('admin.configurations.update') }}" class="bg-white p-6 rounded shadow space-y-6">
            @csrf
            @method('PUT')

            @php $c = $config ?? null; @endphp

            <div>
                <label class="block font-semibold mb-2">App Name</label>
                <input name="app_name" value="{{ old('app_name', $c->app_name ?? config('app.name')) }}" class="w-full border rounded px-3 py-2">
            </div>

            <div class="grid grid-cols-4 gap-4">
                <label class="inline-flex items-center">
                    <input type="checkbox" name="store_enabled" value="1" @checked(old('store_enabled', $c->store_enabled ?? true)) class="mr-2">
                    <span>Store enabled</span>
                </label>

                <label class="inline-flex items-center">
                    <input type="checkbox" name="send_mails_enabled" value="1" @checked(old('send_mails_enabled', $c->send_mails_enabled ?? true)) class="mr-2">
                    <span>Send mails enabled</span>
                </label>

                <label class="inline-flex items-center">
                    <input type="checkbox" name="easypay_enabled" value="1" @checked(old('easypay_enabled', $c->easypay_enabled ?? false)) class="mr-2">
                    <span>Easypay enabled</span>
                </label>

                <label class="inline-flex items-center">
                    <input type="checkbox" name="tax_enabled" value="1" @checked(old('tax_enabled', $c->tax_enabled ?? false)) class="mr-2">
                    <span>Tax enabled</span>
                </label>
            </div>

            <hr>

            <h3 class="font-semibold">Email</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold mb-2">Admin e‑mail</label>
                    <input name="mail_admin" value="{{ old('mail_admin', $c->mail_admin ?? config('mail.admin_address')) }}" class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block font-semibold mb-2">Contact e‑mail</label>
                    <input name="mail_contact" value="{{ old('mail_contact', $c->mail_contact ?? '') }}" class="w-full border rounded px-3 py-2">
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 mt-4">
                <div>
                    <label class="block font-semibold mb-2">SMTP host</label>
                    <input name="smtp_server_host" value="{{ old('smtp_server_host', $c->smtp_server_host ?? '') }}" class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block font-semibold mb-2">SMTP port</label>
                    <input name="smtp_server_port" value="{{ old('smtp_server_port', $c->smtp_server_port ?? '') }}" class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block font-semibold mb-2">SMTP username</label>
                    <input name="smtp_username" value="{{ old('smtp_username', $c->smtp_username ?? '') }}" class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block font-semibold mb-2">SMTP password</label>
                    <input name="smtp_password" value="{{ old('smtp_password', $c->smtp_password ?? '') }}" class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block font-semibold mb-2">SMTP encryption</label>
                    <input name="smtp_encryptation" value="{{ old('smtp_encryptation', $c->smtp_encryptation ?? '') }}" class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block font-semibold mb-2">SMTP mail from</label>
                    <input name="smtp_mail_from" value="{{ old('smtp_mail_from', $c->smtp_mail_from ?? '') }}" class="w-full border rounded px-3 py-2">
                </div>
            </div>

            <hr>

            <h3 class="font-semibold mt-6">Google recaptcha</h3>
            <div class="grid grid-cols-2 gap-4 mt-2">
                <div>
                    <label class="block font-semibold mb-2">Recaptcha site key</label>
                    <input name="google_recaptcha_site_key" value="{{ old('google_recaptcha_site_key', $c->google_recaptcha_site_key ?? '') }}" class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block font-semibold mb-2">Recaptcha secret key</label>
                    <input name="google_recaptcha_secret_key" value="{{ old('google_recaptcha_secret_key', $c->google_recaptcha_secret_key ?? '') }}" class="w-full border rounded px-3 py-2">
                </div>
            </div>

            <hr>

            <h3 class="font-semibold mt-6">Social logins</h3>
            <p class="text-sm text-gray-600 mb-3">Control Google / Microsoft OAuth credentials here. DB values take precedence; the app will fallback to .env when DB values are empty.</p>

            <div class="grid grid-cols-2 gap-4">
                <div class="col-span-2 font-semibold">Google</div>

                <label class="inline-flex items-center col-span-2">
                    <input type="checkbox" name="google_socialite_enabled" value="1" @checked(old('google_socialite_enabled', $c->google_socialite_enabled ?? config('services.google.enabled'))) class="mr-2">
                    <span>Enable Google login</span>
                </label>

                <div>
                    <label class="block font-semibold mb-2">Client ID</label>
                    <input name="google_client_id" value="{{ old('google_client_id', $c->google_client_id ?? config('services.google.client_id')) }}" class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block font-semibold mb-2">Client secret</label>
                    <input name="google_client_secret" value="{{ old('google_client_secret', $c->google_client_secret ?? config('services.google.client_secret')) }}" class="w-full border rounded px-3 py-2">
                </div>
                <div class="col-span-2">
                    <label class="block font-semibold mb-2">Redirect URL</label>
                    <input name="google_redirect" value="{{ old('google_redirect', $c->google_redirect ?? config('services.google.redirect')) }}" class="w-full border rounded px-3 py-2">
                </div>

                <div class="col-span-2 mt-4 font-semibold">Microsoft</div>

                <label class="inline-flex items-center col-span-2">
                    <input type="checkbox" name="microsoft_socialite_enabled" value="1" @checked(old('microsoft_socialite_enabled', $c->microsoft_socialite_enabled ?? config('services.microsoft.enabled'))) class="mr-2">
                    <span>Enable Microsoft login</span>
                </label>

                <div>
                    <label class="block font-semibold mb-2">Client ID</label>
                    <input name="microsoft_client_id" value="{{ old('microsoft_client_id', $c->microsoft_client_id ?? config('services.microsoft.client_id')) }}" class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block font-semibold mb-2">Client secret</label>
                    <input name="microsoft_client_secret" value="{{ old('microsoft_client_secret', $c->microsoft_client_secret ?? config('services.microsoft.client_secret')) }}" class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block font-semibold mb-2">Redirect URL</label>
                    <input name="microsoft_redirect" value="{{ old('microsoft_redirect', $c->microsoft_redirect ?? config('services.microsoft.redirect')) }}" class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block font-semibold mb-2">Tenant</label>
                    <input name="microsoft_tenant" value="{{ old('microsoft_tenant', $c->microsoft_tenant ?? config('services.microsoft.tenant')) }}" class="w-full border rounded px-3 py-2">
                </div>
            </div>

            <hr>

            <h3 class="font-semibold">Easypay</h3>
            <div class="grid grid-cols-2 gap-4">
                <div>
                    <label class="block font-semibold mb-2">Easypay API key</label>
                    <input name="easypay_api_key" value="{{ old('easypay_api_key', $c->easypay_api_key ?? '') }}" class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block font-semibold mb-2">Easypay ID</label>
                    <input name="easypay_id" value="{{ old('easypay_id', $c->easypay_id ?? '') }}" class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block font-semibold mb-2">Webhook secret</label>
                    <input name="easypay_webhook_secret" value="{{ old('easypay_webhook_secret', $c->easypay_webhook_secret ?? '') }}" class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block font-semibold mb-2">Webhook header</label>
                    <input name="easypay_webhook_header" value="{{ old('easypay_webhook_header', $c->easypay_webhook_header ?? '') }}" class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block font-semibold mb-2">Webhook user</label>
                    <input name="easypay_webhook_user" value="{{ old('easypay_webhook_user', $c->easypay_webhook_user ?? '') }}" class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block font-semibold mb-2">Webhook pass</label>
                    <input name="easypay_webhook_pass" value="{{ old('easypay_webhook_pass', $c->easypay_webhook_pass ?? '') }}" class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block font-semibold mb-2">Easypay URL</label>
                    <input name="easypay_url_url" value="{{ old('easypay_url_url', $c->easypay_url_url ?? '') }}" class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block font-semibold mb-2">Easypay SDK URL</label>
                    <input name="easypay_sdk_url" value="{{ old('easypay_sdk_url', $c->easypay_sdk_url ?? '') }}" class="w-full border rounded px-3 py-2">
                </div>
                <div class="col-span-2">
                    <label class="block font-semibold mb-2">Payment methods (comma separated)</label>
                    <input name="easypay_payment_methods" value="{{ old('easypay_payment_methods', $c->easypay_payment_methods ?? '') }}" class="w-full border rounded px-3 py-2">
                </div>
                <div>
                    <label class="block font-semibold mb-2">Session TTL (seconds)</label>
                    <input name="easypay_session_ttl" value="{{ old('easypay_session_ttl', $c->easypay_session_ttl ?? '') }}" class="w-full border rounded px-3 py-2" type="number">
                </div>
                <div>
                    <label class="block font-semibold mb-2">MB TTL (seconds)</label>
                    <input name="easypay_mb_ttl" value="{{ old('easypay_mb_ttl', $c->easypay_mb_ttl ?? '') }}" class="w-full border rounded px-3 py-2" type="number">
                </div>
            </div>

            @if($c)
                <p class="text-sm text-gray-600">
                    Configurations in place from <strong>{{ $c->created_at->format('Y-m-d H:i:s') }}</strong> — edited by <strong>{{ optional($c->user)->name ?? 'System' }}</strong>.
                </p>
            @else
                <p class="text-sm text-gray-600">No configuration saved yet.</p>
            @endif

            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.dashboard') }}" class="bg-gray-300 hover:bg-gray-400 px-6 py-2 rounded">Cancel</a>
                <button type="submit" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded">Save Changes</button>
            </div>
        </form>
    </div>
</x-app-layout>
