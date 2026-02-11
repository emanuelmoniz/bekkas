<?php

namespace Tests\Feature;

use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class EasypayWebhookTest extends TestCase
{
    public function test_generic_webhook_requires_basic_auth_and_logs_payload()
    {
        Log::shouldReceive('info')
            ->once()
            ->withArgs(function ($message, $context) {
                return $message === 'easypay.webhook.generic'
                    && is_array($context)
                    && array_key_exists('payload', $context);
            });

        // configure expected creds + header
        config()->set('easypay.webhook_user', 'webhook-user');
        config()->set('easypay.webhook_pass', 'webhook-pass');
        config()->set('easypay.webhook_header', 'x-easypay-code');
        config()->set('easypay.webhook_secret', 'shh-secret');

        $payload = ['id' => 'abc', 'type' => 'capture', 'status' => 'success'];

        $response = $this->withHeaders([
            'PHP_AUTH_USER' => 'webhook-user',
            'PHP_AUTH_PW' => 'webhook-pass',
            'x-easypay-code' => 'shh-secret',
            'Content-Type' => 'application/json',
        ])->postJson('/webhooks/easypay', $payload);

        $response->assertStatus(200)->assertSeeText('OK');
    }

    public function test_generic_webhook_rejects_bad_auth()
    {
        config()->set('easypay.webhook_user', 'u');
        config()->set('easypay.webhook_pass', 'p');

        $response = $this->postJson('/webhooks/easypay', ['id' => 'x']);
        $response->assertStatus(401);
    }
}
