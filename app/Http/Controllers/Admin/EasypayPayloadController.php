<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\EasypayPayload;
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
     * Display a single Easypay payload (admin).
     */
    public function show(EasypayPayload $payload)
    {
        $payload->load('order.user');
        return view('admin.orders.payloads.show', ['payload' => $payload]);
    }
} 
