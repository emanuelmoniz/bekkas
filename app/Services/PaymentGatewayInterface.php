<?php

namespace App\Services;

use App\Models\Order;

interface PaymentGatewayInterface
{
    /**
     * Process a payment for an order and return gateway response.
     * Expected to return an array with at least a `status` key (e.g. 'success', 'failed').
     */
    public function processPayment(Order $order, array $payload): array;

    /**
     * Validate a webhook payload sent from the gateway.
     */
    public function validateWebhook(array $payload): bool;
}
