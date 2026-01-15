<?php

namespace App\Rules;

use Closure;
use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Support\Facades\Http;

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

        if (!$response->successful() || !$response->json('success')) {
            $fail('The reCAPTCHA verification failed. Please try again.');
        }
    }
}
