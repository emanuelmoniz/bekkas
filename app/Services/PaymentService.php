<?php

namespace App\Services;

use App\Models\Order;
use Psr\Log\LoggerInterface;

class PaymentService
{
    protected PaymentGatewayInterface $gateway;
    protected LoggerInterface $logger;

    public function __construct(PaymentGatewayInterface $gateway, LoggerInterface $logger)
    {
        $this->gateway = $gateway;
        $this->logger = $logger;
    }

    public function processPayment(Order $order, array $payload): array
    {
        $response = $this->gateway->processPayment($order, $payload);

        if (isset($response['status']) && $response['status'] === 'success') {
            $order->update(['is_paid' => true]);
            $this->logger->info('Order marked as paid via PaymentService', ['order_id' => $order->id]);
        }

        return $response;
    }

    public function handleWebhook(Order $order, array $payload): bool
    {
        if (! $this->gateway->validateWebhook($payload)) {
            return false;
        }

        // In many gateways you would also verify payload details match the order.
        $order->update(['is_paid' => true]);
        $this->logger->info('Order marked as paid via webhook', ['order_id' => $order->id]);

        return true;
    }
}
