<?php

namespace Tests\Fakes;

use App\Models\Order;
use App\Services\PaymentGatewayInterface;

class PaymentGatewayFake implements PaymentGatewayInterface
{
    // nextResponse can be adjusted by tests to simulate failure/success
    public array $nextResponse = ['status' => 'success', 'transaction_id' => 'fake-123'];

    public function processPayment(Order $order, array $payload): array
    {
        $res = $this->nextResponse;
        $res['amount'] = $order->total_gross ?? 0;
        return $res;
    }

    public function validateWebhook(array $payload): bool
    {
        return ($payload['transaction_id'] ?? null) === ($this->nextResponse['transaction_id'] ?? null);
    }
}
