<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use Illuminate\Http\Request;

class CountryController extends Controller
{
    public function index()
    {
        $countries = Country::orderBy('name_pt')->get();
        return view('admin.countries.index', compact('countries'));
    }

    public function create()
    {
        return view('admin.countries.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name_pt' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'iso_alpha2' => 'required|string|size:2|unique:countries,iso_alpha2',
            'country_code' => 'required|string|max:10',
        ]);

        Country::create([
            'name_pt' => $request->name_pt,
            'name_en' => $request->name_en,
            'iso_alpha2' => strtoupper($request->iso_alpha2),
            'country_code' => $request->country_code,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.countries.index');
    }

    public function edit(Country $country)
    {
        return view('admin.countries.edit', compact('country'));
    }

    public function update(Request $request, Country $country)
    {
        $request->validate([
            'name_pt' => 'required|string|max:255',
            'name_en' => 'required|string|max:255',
            'iso_alpha2' => 'required|string|size:2|unique:countries,iso_alpha2,' . $country->id,
            'country_code' => 'required|string|max:10',
        ]);

        $country->update([
            'name_pt' => $request->name_pt,
            'name_en' => $request->name_en,
            'iso_alpha2' => strtoupper($request->iso_alpha2),
            'country_code' => $request->country_code,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.countries.index');
    }

    public function destroy(Country $country)
    {
        $country->delete();
        return redirect()->route('admin.countries.index');
    }
}
