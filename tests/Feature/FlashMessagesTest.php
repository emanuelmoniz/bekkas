<?php

namespace Tests\Feature;

use Tests\TestCase;

class FlashMessagesTest extends TestCase
{
    public function test_server_flash_renders_for_each_type()
    {
        $types = [
            'success' => 'text-status-success',
            'error' => 'text-status-error',
            'warning' => 'text-status-warning',
            'info' => 'text-status-info',
        ];

        foreach ($types as $type => $expectedClass) {
            // use deterministic test helper that sets a server flash and renders the welcome view
            $url = '/__cypress/flash?type='.$type.'&message='.urlencode("msg-{$type}");
            $response = $this->get($url);
            $response->assertStatus(200);
            $response->assertSeeText("msg-{$type}");
            $response->assertSee($expectedClass);
            $response->assertSee('role="alert"', false);
        }

        // Regression guard: ensure no placeholder (eg. literal 'null') is rendered on an unrelated page when no flash exists
        $this->get('/')->assertDontSee('null');
    }
}
