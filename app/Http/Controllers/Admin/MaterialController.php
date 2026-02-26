<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Locale;
use App\Models\Material;
use App\Models\MaterialTranslation;
use Illuminate\Http\Request;

class MaterialController extends Controller
{
    public function index(Request $request)
    {
        $query = Material::with('translations');

        if ($request->filled('name')) {
            $name = $request->name;
            $query->whereHas('translations', function ($q) use ($name) {
                $q->where('name', 'like', '%'.$name.'%');
            });
        }

        $materials = $query->get();

        return view('admin.materials.index', compact('materials'));
    }

    public function create()
    {
        return view('admin.materials.create');
    }

    public function store(Request $request)
    {
        $defaultLocale = Locale::defaultLocale()?->code ?? 'en-UK';
        $nameRules = [];
        foreach (Locale::activeCodes() as $locale) {
            $nameRules["name.$locale"] = $locale === $defaultLocale ? 'required|string|max:255' : 'nullable|string|max:255';
        }
        $request->validate($nameRules);

        $material = Material::create();

        foreach (Locale::activeCodes() as $locale) {
            $value = $request->input("name.$locale");
            if (!empty($value)) {
                MaterialTranslation::create([
                    'material_id' => $material->id,
                    'locale' => $locale,
                    'name' => $value,
                ]);
            }
        }

        return redirect()->route('admin.materials.index');
    }

    public function show(Material $material)
    {
        $material->load('translations');

        return view('admin.materials.show', compact('material'));
    }

    public function edit(Material $material)
    {
        $material->load('translations');

        return view('admin.materials.edit', compact('material'));
    }

    public function update(Request $request, Material $material)
    {
        $defaultLocale = Locale::defaultLocale()?->code ?? 'en-UK';
        $nameRules = [];
        foreach (Locale::activeCodes() as $locale) {
            $nameRules["name.$locale"] = $locale === $defaultLocale ? 'required|string|max:255' : 'nullable|string|max:255';
        }
        $request->validate($nameRules);

        foreach (Locale::activeCodes() as $locale) {
            $value = $request->input("name.$locale");
            if (!empty($value)) {
                $material->translations()
                    ->updateOrCreate(
                        ['locale' => $locale],
                        ['name' => $value]
                    );
            }
        }

        return redirect()->route('admin.materials.index');
    }

    public function destroy(Material $material)
    {
        $material->delete();

        return redirect()->route('admin.materials.index');
    }
}
