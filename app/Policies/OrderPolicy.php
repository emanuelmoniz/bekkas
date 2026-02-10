<?php

namespace App\Policies;

use App\Models\Order;
use App\Models\User;

class OrderPolicy
{
    /**
     * Determine whether the user can view the order.
     */
    public function view(User $user, Order $order): bool
    {
        // User can view their own order, or if they're admin
        return $user->id === $order->user_id || $user->isAdmin();
    }

    /**
     * Determine whether the user can update the order.
     */
    public function update(User $user, Order $order): bool
    {
        // Only admins can update orders
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can delete the order.
     */
    public function delete(User $user, Order $order): bool
    {
        // Only admins can delete orders
        return $user->isAdmin();
    }

    /**
     * Determine whether the user can cancel the order.
     */
    public function cancel(User $user, Order $order): bool
    {
        // User can cancel their own unpaid orders, or admin can cancel any
        $canCancelOwn = $user->id === $order->user_id && ! $order->is_paid;

        return $canCancelOwn || $user->isAdmin();
    }

    /**
     * Determine whether the user can refund the order.
     */
    public function refund(User $user, Order $order): bool
    {
        // Only admins can refund orders
        return $user->isAdmin();
    }
}
