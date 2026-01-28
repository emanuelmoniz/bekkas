<?php

return [
    'enabled' => env('EASYPAY_ENABLED', false),
    'env' => env('EASYPAY_ENV', 'test'),
    'api_key' => env('EASYPAY_API_KEY'),
    'id' => env('EASYPAY_ID'),
    'base_url' => rtrim(env('EASYPAY_BASE_URL', 'https://api.test.easypay.pt/2.0'), '/'),
    'payment_methods' => env('EASYPAY_PAYMENT_METHODS', '[]'),
    'session_ttl' => env('EASYPAY_SESSION_TTL', 1800),
    'mb_ttl' => env('EASYPAY_MB_TTL', 172800),
];
