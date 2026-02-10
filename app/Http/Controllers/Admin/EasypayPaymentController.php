<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EasypayPayment;
use Illuminate\Http\Request;

class EasypayPaymentController extends Controller
{
    /**
     * Display a listing of Easypay payments (admin).
     */
    public function index(Request $request)
    {
        $query = EasypayPayment::with(['order.user', 'checkoutSession']);

        if ($request->filled('order_number')) {
            $query->whereHas('order', function ($q) use ($request) {
                $q->where('order_number', 'like', '%'.$request->order_number.'%');
            });
        }

        if ($request->filled('from_paid_date')) {
            // allow filtering by paid_at (preferred) or fall back to created_at for older rows
            $query->where(function ($q) use ($request) {
                $q->whereDate('paid_at', '>=', $request->from_paid_date)
                    ->orWhereDate('created_at', '>=', $request->from_paid_date);
            });
        }

        if ($request->filled('to_paid_date')) {
            $query->where(function ($q) use ($request) {
                $q->whereDate('paid_at', '<=', $request->to_paid_date)
                    ->orWhereDate('created_at', '<=', $request->to_paid_date);
            });
        }

        if ($request->filled('payment_status')) {
            $query->where('payment_status', $request->payment_status);
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        $payments = $query->latest('created_at')->get();

        return view('admin.orders.payments.index', compact('payments'));
    }

    /**
     * Display a single Easypay payment (admin).
     */
    public function show(EasypayPayment $payment)
    {
        $payment->load(['order.user', 'checkoutSession']);

        return view('admin.orders.payments.show', ['payment' => $payment]);
    }
}
