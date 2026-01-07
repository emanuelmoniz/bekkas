<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['user', 'address']);

        if ($request->filled('order_number')) {
            $query->where('order_number', 'like', '%' . $request->order_number . '%');
        }

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

        if ($request->filled('from_date')) {
            $query->whereDate('created_at', '>=', $request->from_date);
        }

        if ($request->filled('to_date')) {
            $query->whereDate('created_at', '<=', $request->to_date);
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

        // Restore stock when order is canceled (only if not already canceled)
        if ($request->boolean('is_canceled') && !$order->is_canceled) {
            DB::transaction(function () use ($order) {
                foreach ($order->items as $item) {
                    $product = $item->product;
                    if ($product) {
                        $product->increment('stock', $item->quantity);
                    }
                }
            });
        }

        $order->update([
            'status' => $request->status,
            'is_paid' => $request->boolean('is_paid'),
            'is_canceled' => $request->boolean('is_canceled'),
            'is_refunded' => $request->boolean('is_refunded'),
            'tracking_number' => $request->tracking_number,
        ]);

        return redirect()->route('admin.orders.show', $order)->with('success', 'Order updated successfully!');
    }
}
