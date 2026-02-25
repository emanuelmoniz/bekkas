<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Locale;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    public function index()
    {
        $locales = Locale::with('country')->orderBy('code')->get();
        return view('admin.locales.index', compact('locales'));
    }

    public function create()
    {
        $countries = Country::with('translations')->where('is_active', true)->orderByTranslatedName()->get();
        return view('admin.locales.create', compact('countries'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'code'       => 'required|string|max:10|unique:locales,code',
            'name'       => 'required|string|max:255',
            'flag_emoji' => 'nullable|string|max:10',
            'country_id' => 'nullable|exists:countries,id',
            'is_active'  => 'boolean',
            'is_default' => 'boolean',
        ]);

        $data['is_active']  = $request->boolean('is_active');
        $data['is_default'] = $request->boolean('is_default');

        if ($data['is_default']) {
            Locale::where('is_default', true)->update(['is_default' => false]);
        }

        Locale::create($data);

        return redirect()->route('admin.locales.index')
                         ->with('success', 'Locale created.');
    }

    public function edit(Locale $locale)
    {
        $countries = Country::with('translations')->where('is_active', true)->orderByTranslatedName()->get();
        return view('admin.locales.edit', compact('locale', 'countries'));
    }

    public function update(Request $request, Locale $locale)
    {
        $data = $request->validate([
            'code'       => 'required|string|max:10|unique:locales,code,' . $locale->code . ',code',
            'name'       => 'required|string|max:255',
            'flag_emoji' => 'nullable|string|max:10',
            'country_id' => 'nullable|exists:countries,id',
            'is_active'  => 'boolean',
            'is_default' => 'boolean',
        ]);

        $data['is_active']  = $request->boolean('is_active');
        $data['is_default'] = $request->boolean('is_default');

        if ($data['is_default']) {
            Locale::where('is_default', true)
                  ->where('code', '!=', $locale->code)
                  ->update(['is_default' => false]);
        }

        $locale->update($data);

        return redirect()->route('admin.locales.index')
                         ->with('success', 'Locale updated.');
    }

    public function destroy(Locale $locale)
    {
        $locale->delete();

        return redirect()->route('admin.locales.index')
                         ->with('success', 'Locale deleted.');
    }
}
