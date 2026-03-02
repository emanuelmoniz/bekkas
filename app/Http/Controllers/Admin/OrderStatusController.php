<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Locale;
use App\Models\OrderStatus;
use Illuminate\Http\Request;

class OrderStatusController extends Controller
{
    public function index(Request $request)
    {
        $query = OrderStatus::with('translations')->orderBy('sort_order');

        if ($request->filled('name')) {
            $name = $request->name;
            $query->whereHas('translations', function ($q) use ($name) {
                $q->where('name', 'like', '%'.$name.'%');
            });
        }

        if ($request->filled('code')) {
            $query->where('code', 'like', '%'.$request->code.'%');
        }

        $statuses = $query->get();

        return view('admin.order-statuses.index', compact('statuses'));
    }

    public function create()
    {
        $locales = Locale::activeList();
        $defaultLocale = Locale::defaultLocale()?->code ?? 'en-UK';

        return view('admin.order-statuses.create', compact('locales', 'defaultLocale'));
    }

    public function store(Request $request)
    {
        $defaultLocale = Locale::defaultLocale()?->code ?? 'en-UK';

        $request->validate([
            'code' => 'required|string|max:255|unique:order_statuses,code',
            'sort_order' => 'required|integer',
            'translations' => 'required|array',
            'translations.*.locale' => 'required|string',
            'translations.*.name' => 'nullable|string|max:255',
        ]);

        $defaultEntry = collect($request->translations)->firstWhere('locale', $defaultLocale);
        if (empty($defaultEntry['name'])) {
            return back()->withErrors(['translations' => "Name for the default locale ({$defaultLocale}) is required."])->withInput();
        }

        $status = OrderStatus::create([
            'code' => $request->code,
            'sort_order' => $request->sort_order,
        ]);

        foreach ($request->translations as $translation) {
            if (! empty($translation['name'])) {
                $status->translations()->create([
                    'locale' => $translation['locale'],
                    'name' => $translation['name'],
                ]);
            }
        }

        return redirect()->route('admin.order-statuses.index')->with('success', 'Order status created successfully!');
    }

    public function edit(OrderStatus $orderStatus)
    {
        $orderStatus->load('translations');
        $locales = Locale::activeList();
        $defaultLocale = Locale::defaultLocale()?->code ?? 'en-UK';

        return view('admin.order-statuses.edit', compact('orderStatus', 'locales', 'defaultLocale'));
    }

    public function update(Request $request, OrderStatus $orderStatus)
    {
        $defaultLocale = Locale::defaultLocale()?->code ?? 'en-UK';

        $request->validate([
            'code' => 'required|string|max:255|unique:order_statuses,code,'.$orderStatus->id,
            'sort_order' => 'required|integer',
            'translations' => 'required|array',
            'translations.*.locale' => 'required|string',
            'translations.*.name' => 'nullable|string|max:255',
        ]);

        $defaultEntry = collect($request->translations)->firstWhere('locale', $defaultLocale);
        if (empty($defaultEntry['name'])) {
            return back()->withErrors(['translations' => "Name for the default locale ({$defaultLocale}) is required."])->withInput();
        }

        $orderStatus->update([
            'code' => $request->code,
            'sort_order' => $request->sort_order,
        ]);

        foreach ($request->translations as $translation) {
            if (! empty($translation['name'])) {
                $orderStatus->translations()->updateOrCreate(
                    ['locale' => $translation['locale']],
                    ['name' => $translation['name']]
                );
            }
        }

        return redirect()->route('admin.order-statuses.index')->with('success', 'Order status updated successfully!');
    }

    public function destroy(OrderStatus $orderStatus)
    {
        $orderStatus->delete();

        return redirect()->route('admin.order-statuses.index')->with('success', 'Order status deleted successfully!');
    }
}
