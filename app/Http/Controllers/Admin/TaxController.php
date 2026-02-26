<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Locale;
use App\Models\Tax;
use App\Models\TaxTranslation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class TaxController extends Controller
{
    public function index(Request $request)
    {
        $query = Tax::with('translations')->orderBy('percentage');

        if ($request->filled('name')) {
            $name = $request->name;
            $query->whereHas('translations', function ($q) use ($name) {
                $q->where('name', 'like', '%'.$name.'%');
            });
        }

        if ($request->filled('active')) {
            $query->where('is_active', $request->active);
        }

        $taxes = $query->get();

        return view('admin.taxes.index', compact('taxes'));
    }

    public function create()
    {
        $locales = Locale::activeList();

        return view('admin.taxes.create', compact('locales'));
    }

    public function store(Request $request)
    {
        $locales = Locale::activeList();

        $rules = [
            'percentage' => 'required|numeric|min:0',
        ];

        foreach (array_keys($locales) as $locale) {
            // Nullable — only save if the user actually filled the field.
            $rules["translations.{$locale}"] = 'nullable|string|max:255';
        }

        $request->validate($rules);

        DB::transaction(function () use ($request, $locales) {
            $tax = Tax::create([
                'percentage' => $request->percentage,
                'is_active'  => $request->boolean('is_active', true),
            ]);

            foreach (array_keys($locales) as $locale) {
                $name = $request->input("translations.{$locale}");
                if (filled($name)) {
                    TaxTranslation::create([
                        'tax_id' => $tax->id,
                        'locale' => $locale,
                        'name'   => $name,
                    ]);
                }
            }
        });

        return redirect()->route('admin.taxes.index');
    }

    public function edit(Tax $tax)
    {
        $tax->load('translations');
        $locales = Locale::activeList();

        return view('admin.taxes.edit', compact('tax', 'locales'));
    }

    public function update(Request $request, Tax $tax)
    {
        $locales = Locale::activeList();

        $rules = [
            'percentage' => 'required|numeric|min:0',
        ];

        foreach (array_keys($locales) as $locale) {
            // Nullable — only save if the user actually filled the field.
            $rules["translations.{$locale}"] = 'nullable|string|max:255';
        }

        $request->validate($rules);

        DB::transaction(function () use ($request, $tax, $locales) {
            $tax->update([
                'percentage' => $request->percentage,
                'is_active'  => $request->boolean('is_active'),
            ]);

            foreach (array_keys($locales) as $locale) {
                $name = $request->input("translations.{$locale}");
                if (filled($name)) {
                    TaxTranslation::updateOrCreate(
                        ['tax_id' => $tax->id, 'locale' => $locale],
                        ['name' => $name]
                    );
                }
                // If field was left empty, leave the existing DB row untouched.
            }
        });

        return redirect()->route('admin.taxes.index');
    }
}
