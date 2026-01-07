<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\OrderStatus;
use Illuminate\Http\Request;

class OrderStatusController extends Controller
{
    public function index()
    {
        $statuses = OrderStatus::with('translations')->orderBy('sort_order')->get();
        return view('admin.order-statuses.index', compact('statuses'));
    }

    public function create()
    {
        $locales = config('app.locales');
        return view('admin.order-statuses.create', compact('locales'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'code' => 'required|string|max:255|unique:order_statuses,code',
            'sort_order' => 'required|integer',
            'translations' => 'required|array',
            'translations.*.locale' => 'required|string',
            'translations.*.name' => 'required|string|max:255',
        ]);

        $status = OrderStatus::create([
            'code' => $request->code,
            'sort_order' => $request->sort_order,
        ]);

        foreach ($request->translations as $translation) {
            $status->translations()->create([
                'locale' => $translation['locale'],
                'name' => $translation['name'],
            ]);
        }

        return redirect()->route('admin.order-statuses.index')->with('success', 'Order status created successfully!');
    }

    public function edit(OrderStatus $orderStatus)
    {
        $orderStatus->load('translations');
        $locales = config('app.locales');
        return view('admin.order-statuses.edit', compact('orderStatus', 'locales'));
    }

    public function update(Request $request, OrderStatus $orderStatus)
    {
        $request->validate([
            'code' => 'required|string|max:255|unique:order_statuses,code,' . $orderStatus->id,
            'sort_order' => 'required|integer',
            'translations' => 'required|array',
            'translations.*.locale' => 'required|string',
            'translations.*.name' => 'required|string|max:255',
        ]);

        $orderStatus->update([
            'code' => $request->code,
            'sort_order' => $request->sort_order,
        ]);

        foreach ($request->translations as $translation) {
            $orderStatus->translations()->updateOrCreate(
                ['locale' => $translation['locale']],
                ['name' => $translation['name']]
            );
        }

        return redirect()->route('admin.order-statuses.index')->with('success', 'Order status updated successfully!');
    }

    public function destroy(OrderStatus $orderStatus)
    {
        $orderStatus->delete();
        return redirect()->route('admin.order-statuses.index')->with('success', 'Order status deleted successfully!');
    }
}
