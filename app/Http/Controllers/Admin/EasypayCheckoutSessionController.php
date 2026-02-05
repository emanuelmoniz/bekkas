<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EasypayCheckoutSession;
use Illuminate\Http\Request;

class EasypayCheckoutSessionController extends Controller
{
    /**
     * Display a listing of Easypay checkout sessions (admin).
     */
    public function index(Request $request)
    {
        $query = EasypayCheckoutSession::with(['order.user', 'payload']);

        if ($request->filled('order_number')) {
            $query->whereHas('order', function ($q) use ($request) {
                $q->where('order_number', 'like', '%' . $request->order_number . '%');
            });
        }

        if ($request->filled('from_order_date')) {
            $query->whereHas('order', function ($q) use ($request) {
                $q->whereDate('created_at', '>=', $request->from_order_date);
            });
        }

        if ($request->filled('to_order_date')) {
            $query->whereHas('order', function ($q) use ($request) {
                $q->whereDate('created_at', '<=', $request->to_order_date);
            });
        }

        if ($request->filled('from_session_date')) {
            $query->whereDate('created_at', '>=', $request->from_session_date);
        }

        if ($request->filled('to_session_date')) {
            $query->whereDate('created_at', '<=', $request->to_session_date);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $sessions = $query->latest('created_at')->get();

        return view('admin.orders.checkouts.index', compact('sessions'));
    }

    /**
     * Display a single Easypay checkout session (admin).
     */
    public function show(EasypayCheckoutSession $session)
    {
        $session->load(['order.user', 'payload']);
        return view('admin.orders.checkouts.show', ['session' => $session]);
    }
}
