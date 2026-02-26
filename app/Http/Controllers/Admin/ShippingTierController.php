<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Locale;
use App\Models\Region;
use App\Models\ShippingTier;
use App\Models\ShippingTierTranslation;
use App\Models\Tax;
use Illuminate\Http\Request;

class ShippingTierController extends Controller
{
    public function index(Request $request)
    {
        $query = ShippingTier::with(['tax', 'countries', 'regions', 'translations']);

        // Filter by name (search in translations)
        if ($request->filled('name')) {
            $name = $request->name;
            $query->whereHas('translations', function ($q) use ($name) {
                $q->where('name', 'like', '%'.$name.'%');
            });
        }

        // Filter by country
        if ($request->filled('country_id')) {
            $query->whereHas('countries', function ($q) use ($request) {
                $q->where('countries.id', $request->country_id);
            });
        }

        // Filter by postal code (finds tiers with regions containing this postal code)
        if ($request->filled('postal_code')) {
            $postalCode = $request->postal_code;
            $query->whereHas('regions', function ($q) use ($postalCode) {
                $q->where('postal_code_from', '<=', $postalCode)
                    ->where('postal_code_to', '>=', $postalCode);
            });
        }

        // Filter by region
        if ($request->filled('region_id')) {
            $query->whereHas('regions', function ($q) use ($request) {
                $q->where('regions.id', $request->region_id);
            });
        }

        $tiers = $query->orderBy('weight_from')->paginate(15)->withQueryString();
        $countries = Country::with('translations')->where('is_active', true)->orderByTranslatedName()->get();
        $regions = Region::with('translations')->orderBy('id')->get();

        return view('admin.shipping-tiers.index', compact('tiers', 'countries', 'regions'));
    }

    public function create()
    {
        $taxes = Tax::where('is_active', true)
            ->orderBy('percentage')
            ->get();

        $countries = Country::with('translations')->where('is_active', true)
            ->orderByTranslatedName()
            ->get();

        $locales = Locale::activeList();

        return view('admin.shipping-tiers.create', compact('taxes', 'countries', 'locales'));
    }

    public function store(Request $request)
    {
        $nameRules = [];
        foreach (Locale::activeCodes() as $locale) {
            $nameRules["name.{$locale}"] = 'required|string|max:255';
        }

        $request->validate(array_merge($nameRules, [
            'weight_from' => 'required|integer|min:0',
            'weight_to' => 'required|integer|min:1|gt:weight_from',
            'cost_gross' => 'required|numeric|min:0',
            'shipping_days' => 'required|integer|min:1',
            'tax_id' => 'required|exists:taxes,id',
            'countries' => 'required|array|min:1',
            'countries.*' => 'exists:countries,id',
            'regions' => 'required|array|min:1',
            'regions.*' => 'exists:regions,id',
        ]));

        $tier = ShippingTier::create([
            'weight_from' => $request->weight_from,
            'weight_to' => $request->weight_to,
            'cost_gross' => $request->cost_gross,
            'shipping_days' => $request->shipping_days,
            'tax_id' => $request->tax_id,
            'active' => $request->boolean('active', true),
        ]);

        foreach (Locale::activeCodes() as $locale) {
            ShippingTierTranslation::create([
                'shipping_tier_id' => $tier->id,
                'locale' => $locale,
                'name' => $request->name[$locale],
            ]);
        }

        $tier->countries()->sync($request->countries);
        $tier->regions()->sync($request->regions);

        return redirect()->route('admin.shipping-tiers.index');
    }

    public function show(ShippingTier $shippingTier)
    {
        $shippingTier->load(['translations', 'tax', 'countries.translations', 'regions']);

        return view('admin.shipping-tiers.show', ['tier' => $shippingTier]);
    }

    public function edit(ShippingTier $shippingTier)
    {
        $shippingTier->load(['countries', 'regions', 'translations']);

        $taxes = Tax::where('is_active', true)
            ->orderBy('percentage')
            ->get();

        $countries = Country::with('translations')->where('is_active', true)
            ->orderByTranslatedName()
            ->get();

        $locales = Locale::activeList();

        return view('admin.shipping-tiers.edit', compact(
            'shippingTier',
            'taxes',
            'countries',
            'locales'
        ));
    }

    public function update(Request $request, ShippingTier $shippingTier)
    {
        $nameRules = [];
        foreach (Locale::activeCodes() as $locale) {
            $nameRules["name.{$locale}"] = 'required|string|max:255';
        }

        $request->validate(array_merge($nameRules, [
            'weight_from' => 'required|integer|min:0',
            'weight_to' => 'required|integer|min:1|gt:weight_from',
            'cost_gross' => 'required|numeric|min:0',
            'shipping_days' => 'required|integer|min:1',
            'tax_id' => 'required|exists:taxes,id',
            'countries' => 'required|array|min:1',
            'countries.*' => 'exists:countries,id',
            'regions' => 'required|array|min:1',
            'regions.*' => 'exists:regions,id',
        ]));

        $shippingTier->update([
            'weight_from' => $request->weight_from,
            'weight_to' => $request->weight_to,
            'cost_gross' => $request->cost_gross,
            'shipping_days' => $request->shipping_days,
            'tax_id' => $request->tax_id,
            'active' => $request->boolean('active'),
        ]);

        foreach (Locale::activeCodes() as $locale) {
            ShippingTierTranslation::updateOrCreate(
                ['shipping_tier_id' => $shippingTier->id, 'locale' => $locale],
                ['name' => $request->name[$locale]]
            );
        }

        $shippingTier->countries()->sync($request->countries);
        $shippingTier->regions()->sync($request->regions);

        return redirect()->route('admin.shipping-tiers.index');
    }

    public function destroy(ShippingTier $shippingTier)
    {
        $shippingTier->delete();

        return redirect()->route('admin.shipping-tiers.index');
    }

    public function duplicate(ShippingTier $shippingTier)
    {
        $shippingTier->load('translations');

        $newTier = ShippingTier::create([
            'weight_from' => $shippingTier->weight_from,
            'weight_to' => $shippingTier->weight_to,
            'cost_gross' => $shippingTier->cost_gross,
            'shipping_days' => $shippingTier->shipping_days,
            'tax_id' => $shippingTier->tax_id,
            'active' => $shippingTier->active,
        ]);

        foreach ($shippingTier->translations as $translation) {
            ShippingTierTranslation::create([
                'shipping_tier_id' => $newTier->id,
                'locale' => $translation->locale,
                'name' => $translation->name.' (Copy)',
            ]);
        }

        // Copy countries and regions relationships
        $newTier->countries()->sync($shippingTier->countries->pluck('id'));
        $newTier->regions()->sync($shippingTier->regions->pluck('id'));

        return redirect()->route('admin.shipping-tiers.edit', $newTier);
    }

    // AJAX endpoint to get regions for selected countries
    public function getRegions(Request $request)
    {
        $request->validate([
            'country_ids' => 'required|array',
            'country_ids.*' => 'exists:countries,id',
        ]);

        $regions = Region::with('translations')
            ->whereIn('country_id', $request->country_ids)
            ->where('is_active', true)
            ->get()
            ->sortBy(fn ($r) => $r->name ?? '')
            ->values()
            ->map(fn ($r) => [
                'id'         => $r->id,
                'name'       => $r->name,
                'country_id' => $r->country_id,
            ]);

        return response()->json($regions);
    }
}
