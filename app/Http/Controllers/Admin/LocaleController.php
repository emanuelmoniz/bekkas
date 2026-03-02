<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Country;
use App\Models\Locale;
use Illuminate\Http\Request;

class LocaleController extends Controller
{
    public function index(Request $request)
    {
        $query = Locale::with('country')->orderBy('code');

        if ($request->filled('code')) {
            $query->where('code', 'like', '%'.$request->code.'%');
        }

        if ($request->filled('name')) {
            $query->where('name', 'like', '%'.$request->name.'%');
        }

        if ($request->filled('active')) {
            $query->where('is_active', $request->active);
        }

        $locales = $query->get();

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
            'code' => 'required|string|max:10|unique:locales,code',
            'name' => 'required|string|max:255',
            'flag_emoji' => 'nullable|string|max:10',
            'country_id' => 'nullable|exists:countries,id',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active');
        $data['is_default'] = $request->boolean('is_default');

        if ($data['is_default']) {
            Locale::where('is_default', true)->update(['is_default' => false]);
        }

        Locale::create($data);

        return redirect()->route('admin.locales.index')
            ->with('success', 'Locale created.');
    }

    public function show(Locale $locale)
    {
        $locale->load('country');

        return view('admin.locales.show', compact('locale'));
    }

    public function edit(Locale $locale)
    {
        $countries = Country::with('translations')->where('is_active', true)->orderByTranslatedName()->get();

        return view('admin.locales.edit', compact('locale', 'countries'));
    }

    public function update(Request $request, Locale $locale)
    {
        $data = $request->validate([
            'code' => 'required|string|max:10|unique:locales,code,'.$locale->code.',code',
            'name' => 'required|string|max:255',
            'flag_emoji' => 'nullable|string|max:10',
            'country_id' => 'nullable|exists:countries,id',
            'is_active' => 'boolean',
            'is_default' => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active');
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
