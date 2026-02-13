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
                $q->where('order_number', 'like', '%'.$request->order_number.'%');
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

    /**
     * Admin: create a checkout session for an order (calls Easypay /checkout).
     */
    public function store(\App\Models\Order $order)
    {
        if (! config('easypay.enabled', false)) {
            return redirect()->back()->with('error', 'Easypay is disabled in configuration');
        }

        $payload = \App\Services\EasypayService::createOrGetPayload($order);
        if (! $payload) {
            return redirect()->back()->with('error', 'Could not prepare Easypay payload for this order');
        }

        $session = \App\Services\EasypayService::createCheckoutSession($payload);

        return redirect()->route('admin.orders.checkouts.show', $session)->with('success', 'Checkout session created');
    }

    /**
     * Admin: refresh/fetch checkout details from Easypay for a given session.
     */
    public function refresh(EasypayCheckoutSession $session)
    {
        if (empty($session->checkout_id)) {
            return redirect()->back()->with('error', 'Session has no checkout_id to fetch');
        }

        $resp = \App\Services\EasypayService::fetchCheckout($session->checkout_id);

        if (! empty($resp['body'])) {
            $session->message = json_encode($resp['body']);
            // Prefer Easypay's nested `checkout.status` when available, otherwise fall back
            // to top-level `status` (keeps behaviour consistent with other services).
            $session->status = data_get($resp, 'body.checkout.status') ?? data_get($resp, 'body.status', $session->status);
        }

        $session->in_error = ! (bool) data_get($resp, 'ok', false);
        $session->error_code = data_get($resp, 'status', $session->error_code);
        $session->save();

        // Refresh any linked payments for the order (best-effort)
        try {
            if ($session->order) {
                app(\App\Services\EasypayPaymentRefreshService::class)->refreshLatestPaymentForOrder($session->order);
            }
        } catch (\Throwable $e) {
            logger()->warning('Admin checkout refresh: payment refresh failed', ['err' => $e->getMessage(), 'session_id' => $session->id]);
        }

        return redirect()->back()->with('success', 'Checkout details refreshed');
    }

    /**
     * Admin: cancel a checkout session at Easypay (DELETE /checkout/{id}).
     */
    public function cancel(EasypayCheckoutSession $session)
    {
        if (empty($session->checkout_id)) {
            return redirect()->back()->with('error', 'Session has no checkout_id to cancel');
        }

        $res = \App\Services\EasypayService::cancelCheckout($session->checkout_id);

        if (data_get($res, 'ok')) {
            $session->status = 'cancelled';
            $session->in_error = false;
            $session->save();

            return redirect()->back()->with('success', 'Checkout cancelled at Easypay');
        }

        // Persist remote response/message for debugging
        $session->message = is_array(data_get($res, 'body')) ? json_encode(data_get($res, 'body')) : (string) data_get($res, 'body');
        $session->in_error = true;
        $session->error_code = data_get($res, 'status');
        $session->save();

        return redirect()->back()->with('error', 'Failed to cancel checkout (see response)');
    }
}

