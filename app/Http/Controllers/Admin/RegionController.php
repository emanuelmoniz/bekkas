<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Region;
use App\Models\RegionTranslation;
use App\Models\ShippingTier;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RegionController extends Controller
{
    public function index(Request $request)
    {
        $query = Region::with(['country', 'translations']);

        // Filter by name (search across translations)
        if ($request->filled('name')) {
            $query->whereHas('translations', function ($q) use ($request) {
                $q->where('name', 'like', '%'.$request->name.'%');
            });
        }

        // Filter by country
        if ($request->filled('country_id')) {
            $query->where('country_id', $request->country_id);
        }

        // Filter by postal code (matches if search is between from and to)
        if ($request->filled('postal_code')) {
            $postalCode = $request->postal_code;
            $query->where(function ($q) use ($postalCode) {
                $q->where('postal_code_from', '<=', $postalCode)
                    ->where('postal_code_to', '>=', $postalCode);
            });
        }

        // Filter by is_active
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $regions = $query->orderBy('id')->paginate(15)->withQueryString();
        $countries = Country::with('translations')->where('is_active', true)->orderByTranslatedName()->get();
        $locales = config('app.locales');

        return view('admin.regions.index', compact('regions', 'countries', 'locales'));
    }

    public function create()
    {
        $countries = Country::with('translations')->where('is_active', true)->orderByTranslatedName()->get();
        $locales = config('app.locales');

        return view('admin.regions.create', compact('countries', 'locales'));
    }

    public function store(Request $request)
    {
        $locales = array_keys(config('app.locales'));

        $rules = [
            'country_id'       => 'required|exists:countries,id',
            'postal_code_from' => 'required|string|max:20',
            'postal_code_to'   => 'required|string|max:20',
        ];

        foreach ($locales as $locale) {
            $rules["translations.{$locale}"] = 'required|string|max:255';
        }

        $request->validate($rules);

        DB::transaction(function () use ($request, $locales) {
            $region = Region::create([
                'country_id'       => $request->country_id,
                'postal_code_from' => $request->postal_code_from,
                'postal_code_to'   => $request->postal_code_to,
                'is_active'        => $request->boolean('is_active', true),
            ]);

            foreach ($locales as $locale) {
                RegionTranslation::create([
                    'region_id' => $region->id,
                    'locale'    => $locale,
                    'name'      => $request->input("translations.{$locale}"),
                ]);
            }
        });

        return redirect()->route('admin.regions.index');
    }

    public function show(Region $region)
    {
        $region->load(['country', 'translations']);
        $locales = config('app.locales');

        return view('admin.regions.show', compact('region', 'locales'));
    }

    public function edit(Region $region)
    {
        $region->load('translations');
        $countries = Country::with('translations')->where('is_active', true)->orderByTranslatedName()->get();
        $locales = config('app.locales');

        // Only tiers already assigned to this region can be set as default
        $shippingTiers = ShippingTier::with('translations')
            ->whereHas('regions', function ($q) use ($region) {
                $q->where('regions.id', $region->id);
            })->get();

        // Get current default shipping tier from the pivot
        $defaultShippingTierId = DB::table('region_shipping_tier')
            ->where('region_id', $region->id)
            ->where('is_default', true)
            ->value('shipping_tier_id');

        return view('admin.regions.edit', compact('region', 'countries', 'locales', 'shippingTiers', 'defaultShippingTierId'));
    }

    public function update(Request $request, Region $region)
    {
        $locales = array_keys(config('app.locales'));

        $rules = [
            'country_id'       => 'required|exists:countries,id',
            'postal_code_from' => 'required|string|max:20',
            'postal_code_to'   => 'required|string|max:20',
            'default_shipping_tier_id' => ['nullable', function ($attribute, $value, $fail) use ($region) {
                if ($value) {
                    $exists = DB::table('region_shipping_tier')
                        ->where('region_id', $region->id)
                        ->where('shipping_tier_id', $value)
                        ->exists();
                    if (! $exists) {
                        $fail('The selected default shipping tier is not assigned to this region.');
                    }
                }
            }],
        ];

        foreach ($locales as $locale) {
            $rules["translations.{$locale}"] = 'required|string|max:255';
        }

        $request->validate($rules);

        DB::transaction(function () use ($request, $region, $locales) {
            $region->update([
                'country_id'       => $request->country_id,
                'postal_code_from' => $request->postal_code_from,
                'postal_code_to'   => $request->postal_code_to,
                'is_active'        => $request->boolean('is_active'),
            ]);

            foreach ($locales as $locale) {
                RegionTranslation::updateOrCreate(
                    ['region_id' => $region->id, 'locale' => $locale],
                    ['name' => $request->input("translations.{$locale}")]
                );
            }

            // Clear the current default for this region
            DB::table('region_shipping_tier')
                ->where('region_id', $region->id)
                ->update(['is_default' => false]);

            // Set the new default on the pivot row (must already be assigned to this region)
            if ($request->filled('default_shipping_tier_id')) {
                DB::table('region_shipping_tier')
                    ->where('region_id', $region->id)
                    ->where('shipping_tier_id', $request->default_shipping_tier_id)
                    ->update(['is_default' => true]);
            }
        });

        return redirect()->route('admin.regions.index')
            ->with('success', 'Region updated successfully.');
    }

    public function destroy(Region $region)
    {
        $region->delete();

        return redirect()->route('admin.regions.index');
    }
}
