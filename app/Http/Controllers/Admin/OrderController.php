<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderStatus;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrderController extends Controller
{
    public function index(Request $request)
    {
        $query = Order::with(['user', 'address']);

        if ($request->filled('order_number')) {
            $query->where('order_number', 'like', '%'.$request->order_number.'%');
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('is_paid')) {
            $query->where('is_paid', (bool) $request->is_paid);
        }

        if ($request->filled('user')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->user.'%');
            });
        }

        if ($request->filled('email')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('email', 'like', '%'.$request->email.'%');
            });
        }

        if ($request->filled('nif')) {
            $query->whereHas('address', function ($q) use ($request) {
                $q->where('nif', 'like', '%'.$request->nif.'%');
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
        $order->load(['items.product', 'user', 'address', 'easypayPayload']);
        $statuses = OrderStatus::with('translations')->orderBy('sort_order')->get();

        return view('admin.orders.show', compact('order', 'statuses'));
    }

    public function update(Request $request, Order $order)
    {
        $request->validate([
            'status' => 'required|string',
            'is_paid' => 'nullable|boolean',
            'is_refunded' => 'nullable|boolean',
            'tracking_number' => 'nullable|string|max:255',
            'tracking_url' => 'nullable|url|max:500',
        ]);

        $oldStatus = $order->status;
        $oldIsPaid = $order->is_paid;

        DB::transaction(function () use ($request, $order, $oldIsPaid) {
            // Restore stock when order status changes to CANCELED (only if not already canceled)
            // Only restore stock for items that were NOT backordered (had stock when ordered)
            if ($request->status === 'CANCELED' && $order->status !== 'CANCELED') {
                foreach ($order->items as $item) {
                    $product = $item->product;
                    // Only restore stock if this item was NOT backordered
                    if ($product && ! $item->was_backordered) {
                        $product->increment('stock', $item->quantity);
                    }
                }
            }

            // Decrement stock when order status changes from CANCELED to another status
            if ($order->status === 'CANCELED' && $request->status !== 'CANCELED') {
                foreach ($order->items as $item) {
                    $product = $item->product;
                    // Only decrement if item was NOT backordered originally
                    if ($product && ! $item->was_backordered) {
                        if ($product->stock >= $item->quantity) {
                            $product->decrement('stock', $item->quantity);
                        } elseif ($product->stock > 0) {
                            // Partial stock available
                            $product->update(['stock' => 0]);
                        }
                    }
                }
            }

            // Decide paid transition explicitly so we can use the audit-friendly helper.
            $shouldMarkPaid = (! $oldIsPaid && $request->boolean('is_paid'));

            // Apply mass-updates but do NOT flip is_paid here when admin is marking as paid —
            // we handle that below through the helper to ensure status/audit hooks run.
            $order->update([
                'status' => $request->status,
                'is_refunded' => $request->boolean('is_refunded'),
                'tracking_number' => $request->tracking_number,
                'tracking_url' => $request->tracking_url,
            ]);

            if ($shouldMarkPaid) {
                $order->markAsPaidManually(auth()->id() ?: null);
            } elseif ($request->has('is_paid') && ! $request->boolean('is_paid')) {
                // Admin explicitly un-marked payment; allow that through a direct update
                $order->update(['is_paid' => false]);
            }
        });

        // If status changed notify the customer
        $order->refresh();
        if ($oldStatus !== $order->status && $order->user && $order->user->email) {
            // Use user's language for customer email
            $locale = $order->user->language ?? app()->getLocale();

            // Resolve status translation in customer's locale
            $statusObj = \App\Models\OrderStatus::where('code', $order->status)->first();
            $statusLabel = $statusObj?->translation($locale)?->name ?? $order->status;

            $eventLabel = t('orders.email.event.status_changed', ['status' => $statusLabel]) ?: ("Order status changed to {$statusLabel}");

            \Illuminate\Support\Facades\Mail::to($order->user->email)->locale($locale)->queue(new \App\Mail\OrderNotification($order, $eventLabel, $order->user->name, $statusLabel));
        }

        return redirect()->route('admin.orders.show', $order)->with('success', 'Order updated successfully!');
    }
}
