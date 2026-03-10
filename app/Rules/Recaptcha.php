<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class Recaptcha implements ValidationRule
{
    public function validate(string $attribute, mixed $value, Closure $fail): void
    {
        if (empty(config('services.recaptcha.secret_key'))) {
            return; // Skip validation if reCAPTCHA is not configured
        }

        $response = Http::asForm()->post('https://www.google.com/recaptcha/api/siteverify', [
            'secret' => config('services.recaptcha.secret_key'),
            'response' => $value,
            'remoteip' => request()->ip(),
        ]);

        if (! $response->successful() || ! $response->json('success')) {
            $body = $response->body();
            $errorCodes = $response->json('error-codes') ?? [];
            Log::warning('reCAPTCHA verification failed', [
                'status' => $response->status(),
                'success' => $response->json('success'),
                'error_codes' => $errorCodes,
                'body' => $body,
            ]);

            $fail('The reCAPTCHA verification failed. Please try again.');
        }
    }
}
