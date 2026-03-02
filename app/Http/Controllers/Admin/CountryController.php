<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\CountryTranslation;
use App\Models\Locale;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CountryController extends Controller
{
    public function index(Request $request)
    {
        $query = Country::with('translations');

        if ($request->filled('name')) {
            $name = $request->name;
            $query->whereHas('translations', function ($q) use ($name) {
                $q->where('name', 'like', '%'.$name.'%');
            });
        }

        if ($request->filled('iso_alpha_2')) {
            $query->where('iso_alpha2', 'like', '%'.$request->iso_alpha_2.'%');
        }

        if ($request->filled('active')) {
            $query->where('is_active', $request->active);
        }

        $countries = $query->orderByTranslatedName()->get();

        return view('admin.countries.index', compact('countries'));
    }

    public function create()
    {
        // Show inputs for every active locale; values will be empty for new locales.
        $locales = Locale::activeList();

        return view('admin.countries.create', compact('locales'));
    }

    public function store(Request $request)
    {
        $locales = Locale::activeList();

        $rules = [
            'iso_alpha2' => 'required|string|size:2|unique:countries,iso_alpha2',
            'country_code' => 'required|string|max:10',
        ];

        foreach (array_keys($locales) as $locale) {
            // Nullable — only save if the user actually filled the field.
            $rules["translations.{$locale}"] = 'nullable|string|max:255';
        }

        $request->validate($rules);

        DB::transaction(function () use ($request, $locales) {
            $country = Country::create([
                'iso_alpha2' => strtoupper($request->iso_alpha2),
                'country_code' => $request->country_code,
                'is_active' => $request->boolean('is_active'),
            ]);

            foreach (array_keys($locales) as $locale) {
                $name = $request->input("translations.{$locale}");
                if (filled($name)) {
                    CountryTranslation::create([
                        'country_id' => $country->id,
                        'locale' => $locale,
                        'name' => $name,
                    ]);
                }
            }
        });

        return redirect()->route('admin.countries.index');
    }

    public function show(Country $country)
    {
        $country->load('translations');

        return view('admin.countries.show', compact('country'));
    }

    public function edit(Country $country)
    {
        $country->load('translations');
        // Show inputs for every active locale; pre-fill only what exists in DB.
        $locales = Locale::activeList();

        return view('admin.countries.edit', compact('country', 'locales'));
    }

    public function update(Request $request, Country $country)
    {
        $locales = Locale::activeList();

        $rules = [
            'iso_alpha2' => 'required|string|size:2|unique:countries,iso_alpha2,'.$country->id,
            'country_code' => 'required|string|max:10',
        ];

        foreach (array_keys($locales) as $locale) {
            // Nullable — only save if the user actually filled the field.
            $rules["translations.{$locale}"] = 'nullable|string|max:255';
        }

        $request->validate($rules);

        DB::transaction(function () use ($request, $country, $locales) {
            $country->update([
                'iso_alpha2' => strtoupper($request->iso_alpha2),
                'country_code' => $request->country_code,
                'is_active' => $request->boolean('is_active'),
            ]);

            foreach (array_keys($locales) as $locale) {
                $name = $request->input("translations.{$locale}");
                if (filled($name)) {
                    CountryTranslation::updateOrCreate(
                        ['country_id' => $country->id, 'locale' => $locale],
                        ['name' => $name]
                    );
                }
                // If field was cleared, leave the existing DB row untouched.
            }
        });

        return redirect()->route('admin.countries.index');
    }

    public function destroy(Country $country)
    {
        $country->delete();

        return redirect()->route('admin.countries.index');
    }
}
