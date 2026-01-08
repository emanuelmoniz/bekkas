<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Region;
use App\Models\Country;
use Illuminate\Http\Request;

class RegionController extends Controller
{
    public function index(Request $request)
    {
        $query = Region::with('country');

        // Filter by name
        if ($request->filled('name')) {
            $query->where('name', 'like', '%' . $request->name . '%');
        }

        // Filter by country
        if ($request->filled('country_id')) {
            $query->where('country_id', $request->country_id);
        }

        // Filter by postal code (matches if search is between from and to)
        if ($request->filled('postal_code')) {
            $postalCode = $request->postal_code;
            $query->where(function($q) use ($postalCode) {
                $q->where('postal_code_from', '<=', $postalCode)
                  ->where('postal_code_to', '>=', $postalCode);
            });
        }

        // Filter by is_active
        if ($request->filled('is_active')) {
            $query->where('is_active', $request->is_active);
        }

        $regions = $query->orderBy('name')->paginate(15)->withQueryString();
        $countries = Country::where('is_active', true)->orderBy('name_en')->get();

        return view('admin.regions.index', compact('regions', 'countries'));
    }

    public function create()
    {
        $countries = Country::where('is_active', true)->orderBy('name_en')->get();
        return view('admin.regions.create', compact('countries'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'country_id' => 'required|exists:countries,id',
            'name' => 'required|string|max:255',
            'postal_code_from' => 'required|string|max:20',
            'postal_code_to' => 'required|string|max:20',
        ]);

        Region::create([
            'country_id' => $request->country_id,
            'name' => $request->name,
            'postal_code_from' => $request->postal_code_from,
            'postal_code_to' => $request->postal_code_to,
            'is_active' => $request->boolean('is_active', true),
        ]);

        return redirect()->route('admin.regions.index');
    }

    public function show(Region $region)
    {
        $region->load('country');
        return view('admin.regions.show', compact('region'));
    }

    public function edit(Region $region)
    {
        $countries = Country::where('is_active', true)->orderBy('name_en')->get();
        return view('admin.regions.edit', compact('region', 'countries'));
    }

    public function update(Request $request, Region $region)
    {
        $request->validate([
            'country_id' => 'required|exists:countries,id',
            'name' => 'required|string|max:255',
            'postal_code_from' => 'required|string|max:20',
            'postal_code_to' => 'required|string|max:20',
        ]);

        $region->update([
            'country_id' => $request->country_id,
            'name' => $request->name,
            'postal_code_from' => $request->postal_code_from,
            'postal_code_to' => $request->postal_code_to,
            'is_active' => $request->boolean('is_active'),
        ]);

        return redirect()->route('admin.regions.index');
    }

    public function destroy(Region $region)
    {
        $region->delete();
        return redirect()->route('admin.regions.index');
    }
}
