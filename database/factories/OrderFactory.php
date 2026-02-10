<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OrderFactory extends Factory
{
    protected $model = Order::class;

    /**
     * Add helper to conveniently add items to an order:
     * Order::factory()->hasItems(2) or Order::factory()->hasItems(1, ['product_id'=>1])
     */
    public function hasItems(int $count = 1, array $attributes = [])
    {
        $factory = \Database\Factories\OrderItemFactory::new()->count($count);
        if (! empty($attributes)) {
            $factory->state($attributes);
        }

        return $this->has($factory, 'items');
    }

    public function definition()
    {
        return [
            'user_id' => User::factory(),
            'address_id' => Address::factory(),

            // Address snapshot (required non-null columns)
            'address_title' => 'Home',
            'address_nif' => null,
            'address_line_1' => 'Address 1',
            'address_line_2' => null,
            'address_postal_code' => '0000-000',
            'address_city' => 'City',
            'address_country' => 'Portugal',

            'status' => 'PROCESSING',
            'is_paid' => false,
            'is_canceled' => false,
            'is_refunded' => false,
            'products_total_net' => 10.00,
            'products_total_tax' => 2.30,
            'products_total_gross' => 12.30,
            'shipping_net' => 0.00,
            'shipping_tax' => 0.00,
            'shipping_gross' => 0.00,
            'total_net' => 10.00,
            'total_tax' => 2.30,
            'total_gross' => 12.30,
        ];
    }

    public function configure()
    {
        return $this->afterCreating(function (Order $order) {
            // Ensure address snapshot fields are populated from address if present
            $address = $order->address;
            if ($address) {
                $order->update([
                    'address_title' => $address->title ?? 'Home',
                    'address_nif' => $address->nif ?? null,
                    'address_line_1' => $address->address_line_1 ?? 'Address 1',
                    'address_line_2' => $address->address_line_2 ?? null,
                    'address_postal_code' => $address->postal_code ?? '0000-000',
                    'address_city' => $address->city ?? 'City',
                    'address_country' => optional($address->country)->name_pt ?? 'Portugal',
                ]);
            } else {
                $order->update([
                    'address_title' => 'Home',
                    'address_line_1' => 'Address 1',
                    'address_postal_code' => '0000-000',
                    'address_city' => 'City',
                    'address_country' => 'Portugal',
                ]);
            }

            // Defensive: ensure tests/factories do not leave Easypay rows when the gateway is disabled
            if (! config('easypay.enabled', false)) {
                \App\Models\EasypayPayload::where('order_id', $order->id)->delete();
                \App\Models\EasypayCheckoutSession::where('order_id', $order->id)->delete();
                \App\Models\EasypayPayment::where('order_id', $order->id)->delete();
            }
        });
    }
}
