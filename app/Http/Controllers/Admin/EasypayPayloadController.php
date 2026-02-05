<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EasypayPayload;
use App\Models\Order;
use App\Services\EasypayService;
use Illuminate\Http\Request;

class EasypayPayloadController extends Controller
{
    /**
     * Display a listing of Easypay payloads (admin).
     */
    public function index(Request $request)
    {
        $query = EasypayPayload::with(['order.user']);

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

        if ($request->filled('from_payload_date')) {
            $query->whereDate('created_at', '>=', $request->from_payload_date);
        }

        if ($request->filled('to_payload_date')) {
            $query->whereDate('created_at', '<=', $request->to_payload_date);
        }

        $payloads = $query->latest('created_at')->get();

        return view('admin.orders.payloads.index', compact('payloads'));
    }

    /**
     * Create a payload for an order (admin). Idempotent via EasypayService::createOrGetPayload.
     */
    public function store(Order $order)
    {
        // createOrGetPayload is idempotent and returns the existing payload if present
        $payload = EasypayService::createOrGetPayload($order);

        return redirect()->route('admin.orders.payloads.show', $payload)->with('success', 'Payload created');
    }

    /**
     * Display a single Easypay payload (admin).
     */
    public function show(EasypayPayload $payload)
    {
        $payload->load('order.user');
        return view('admin.orders.payloads.show', ['payload' => $payload]);
    }

    /**
     * Delete a payload (admin).
     */
    public function destroy(EasypayPayload $payload)
    {
        $order = $payload->order;

        // perform delete — sessions have payload_id nullable and are nullOnDelete by migration
        $payload->delete();

        return redirect()->route('admin.orders.payloads.index')->with('success', 'Payload deleted');
    }

    /**
     * Recreate payload from the authoritative order snapshot.
     * Deletes existing payload and creates a fresh one using EasypayService::createOrGetPayload
     */
    public function recreate(EasypayPayload $payload)
    {
        $order = $payload->order;
        if (! $order) {
            return redirect()->back()->with('error', 'Payload has no associated order');
        }

        \DB::transaction(function () use ($order) {
            // remove any existing payload(s) for the order
            \App\Models\EasypayPayload::where('order_id', $order->id)->delete();
            // create a fresh payload based on current order data
            \App\Services\EasypayService::createOrGetPayload($order);
        });

        // load the newly created payload to redirect to its show page
        $new = $order->fresh()->easypayPayload;

        if ($new) {
            return redirect()->route('admin.orders.payloads.show', $new)->with('success', 'Payload recreated');
        }

        return redirect()->route('admin.orders.payloads.index')->with('error', 'Failed to recreate payload');
    }
} 
