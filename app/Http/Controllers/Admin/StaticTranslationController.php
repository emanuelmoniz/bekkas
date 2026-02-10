<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\StaticTranslation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

class StaticTranslationController extends Controller
{
    public function index(Request $request)
    {
        $query = StaticTranslation::query();

        if ($request->filled('key')) {
            $query->where('key', 'like', '%'.$request->key.'%');
        }

        if ($request->filled('locale')) {
            $query->where('locale', $request->locale);
        }

        $items = $query->orderBy('key')->paginate(50);

        return view('admin.static-translations.index', compact('items'));
    }

    public function create()
    {
        return view('admin.static-translations.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'key' => 'required|string|max:255',
            'locale' => 'required|string|max:5',
            'value' => 'required|string',
        ]);

        StaticTranslation::create($request->only(['key', 'locale', 'value']));

        // Invalidate cache
        Cache::forget('static_translations_all');

        return redirect()->route('admin.static-translations.index')->with('success', 'Translation added.');
    }

    public function edit(StaticTranslation $static_translation)
    {
        return view('admin.static-translations.edit', ['item' => $static_translation]);
    }

    public function update(Request $request, StaticTranslation $static_translation)
    {
        $request->validate([
            'value' => 'required|string',
        ]);

        $static_translation->update($request->only(['value']));

        Cache::forget('static_translations_all');

        return redirect()->route('admin.static-translations.index')->with('success', 'Translation updated.');
    }

    public function destroy(StaticTranslation $static_translation)
    {
        $static_translation->delete();
        Cache::forget('static_translations_all');

        return redirect()->route('admin.static-translations.index')->with('success', 'Translation removed.');
    }
}
