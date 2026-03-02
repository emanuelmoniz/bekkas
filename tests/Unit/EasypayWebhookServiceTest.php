<?php

namespace Tests\Unit;

use App\Services\EasypayWebhookService;
use Illuminate\Support\Facades\Log;
use Tests\TestCase;

class EasypayWebhookServiceTest extends TestCase
{
    public function test_handle_generic_logs_payload()
    {
        Log::shouldReceive('info')
            ->once()
            ->with('easypay.webhook.generic', \Mockery::on(function ($arg) {
                return is_array($arg) && array_key_exists('payload', $arg);
            }));

        $s = new EasypayWebhookService;
        $s->handleGeneric(['id' => '1'], ['ip' => '127.0.0.1']);

        $this->assertTrue(true); // nothing else to assert
    }
}
