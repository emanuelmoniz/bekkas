<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['user', 'address']);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('is_paid')) {
            $query->where('is_paid', (bool) $request->is_paid);
        }

        if ($request->filled('user')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->user . '%');
            });
        }

        if ($request->filled('email')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('email', 'like', '%' . $request->email . '%');
            });
        }

        if ($request->filled('nif')) {
            $query->whereHas('address', function ($q) use ($request) {
                $q->where('nif', 'like', '%' . $request->nif . '%');
            });
        }

        $orders = $query->latest()->get();

        return view('admin.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $order->load(['items.product', 'user', 'address']);

        return view('admin.orders.show', compact('order'));
    }

    public function update(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|string',
            'is_paid' => 'nullable|boolean',
            'is_canceled' => 'nullable|boolean',
            'is_refunded' => 'nullable|boolean',
            'tracking_number' => 'nullable|string|max:255',
        ]);

        if ($request->filled('is_canceled') && $request->is_canceled) {
            $order->status = 'CANCELED';
            $order->is_canceled = true;
        }

        $order->update([
            'status' => $request->status,
            'is_paid' => $request->has('is_paid'),
            'is_refunded' => $request->has('is_refunded'),
            'tracking_number' => $request->tracking_number,
        ]);

        return redirect()->route('admin.orders.show', $order);
    }
}
