<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\CountryTranslation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class CountryController extends Controller
{
    public function index()
    {
        $countries = Country::with('translations')->orderByTranslatedName()->get();
        $locales   = config('app.locales');

        return view('admin.countries.index', compact('countries', 'locales'));
    }

    public function create()
    {
        $locales = config('app.locales');

        return view('admin.countries.create', compact('locales'));
    }

    public function store(Request $request)
    {
        $locales = array_keys(config('app.locales'));

        $rules = [
            'iso_alpha2'   => 'required|string|size:2|unique:countries,iso_alpha2',
            'country_code' => 'required|string|max:10',
        ];

        foreach ($locales as $locale) {
            $rules["translations.{$locale}"] = 'required|string|max:255';
        }

        $request->validate($rules);

        DB::transaction(function () use ($request, $locales) {
            $country = Country::create([
                'iso_alpha2'   => strtoupper($request->iso_alpha2),
                'country_code' => $request->country_code,
                'is_active'    => $request->boolean('is_active'),
            ]);

            foreach ($locales as $locale) {
                CountryTranslation::create([
                    'country_id' => $country->id,
                    'locale'     => $locale,
                    'name'       => $request->input("translations.{$locale}"),
                ]);
            }
        });

        return redirect()->route('admin.countries.index');
    }

    public function edit(Country $country)
    {
        $country->load('translations');
        $locales = config('app.locales');

        return view('admin.countries.edit', compact('country', 'locales'));
    }

    public function update(Request $request, Country $country)
    {
        $locales = array_keys(config('app.locales'));

        $rules = [
            'iso_alpha2'   => 'required|string|size:2|unique:countries,iso_alpha2,'.$country->id,
            'country_code' => 'required|string|max:10',
        ];

        foreach ($locales as $locale) {
            $rules["translations.{$locale}"] = 'required|string|max:255';
        }

        $request->validate($rules);

        DB::transaction(function () use ($request, $country, $locales) {
            $country->update([
                'iso_alpha2'   => strtoupper($request->iso_alpha2),
                'country_code' => $request->country_code,
                'is_active'    => $request->boolean('is_active'),
            ]);

            foreach ($locales as $locale) {
                CountryTranslation::updateOrCreate(
                    ['country_id' => $country->id, 'locale' => $locale],
                    ['name' => $request->input("translations.{$locale}")]
                );
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
