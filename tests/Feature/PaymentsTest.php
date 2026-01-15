<?php

namespace Tests\Feature;

use Tests\TestCase;
use Illuminate\Foundation\Testing\RefreshDatabase;

class PaymentsTest extends TestCase
{
    use RefreshDatabase;

    public function test_payment_integration_not_implemented_yet()
    {
        $this->markTestIncomplete('Payment integration endpoints are not implemented yet. This test will be completed when a PaymentService/controller exists.');
    }

    public function test_mock_payment_success_scenario_placeholder()
    {
        $this->markTestIncomplete('Add a mocked payment gateway and test success flow (redirect/cron/webhook)');
    }
}
