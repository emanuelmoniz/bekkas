<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Locale;
use App\Models\StaticTranslation;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class StaticTranslationController extends Controller
{
    // ── URL-safe base64 helpers ───────────────────────────────────────────────

    public static function encodeKey(string $key): string
    {
        return rtrim(strtr(base64_encode($key), '+/', '-_'), '=');
    }

    private static function decodeKey(string $encoded): string
    {
        return base64_decode(strtr($encoded, '-_', '+/'));
    }

    // ── Actions ───────────────────────────────────────────────────────────────

    public function index(Request $request)
    {
        $keyRows = StaticTranslation::select(DB::raw('`key`, MIN(`context`) as context'))
            ->when($request->filled('search'), fn ($q) => $q->where('key', 'like', '%'.$request->search.'%'))
            ->when($request->filled('ctx'),    fn ($q) => $q->where('context', 'like', '%'.$request->ctx.'%'))
            ->when($request->filled('text'),   fn ($q) => $q->whereExists(function ($sq) use ($request) {
                $sq->select(DB::raw(1))
                   ->from('static_translations as st2')
                   ->whereColumn('st2.key', 'static_translations.key')
                   ->where('st2.value', 'like', '%'.$request->text.'%');
            }))
            ->groupBy('key')
            ->orderBy('key')
            ->paginate(50)
            ->withQueryString();

        $allTranslations = StaticTranslation::whereIn('key', $keyRows->pluck('key'))
            ->get()
            ->groupBy('key')
            ->map(fn ($rows) => $rows->keyBy('locale'));

        $locales = Locale::activeList();

        return view('admin.static-translations.index', compact('keyRows', 'allTranslations', 'locales'));
    }

    public function create()
    {
        $locales = Locale::activeList();

        return view('admin.static-translations.create', compact('locales'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'key'      => [
                'required', 'string', 'max:255',
                function ($attribute, $value, $fail) {
                    if (StaticTranslation::where('key', $value)->exists()) {
                        $fail('This key already exists — use Edit to modify it.');
                    }
                },
            ],
            'context'  => ['nullable', 'string', 'max:255'],
            'values'   => ['required', 'array'],
            'values.*' => ['nullable', 'string'],
        ]);

        $key     = $request->input('key');
        $context = $request->input('context');
        $now     = now();
        $rows    = [];

        foreach ($request->input('values', []) as $locale => $value) {
            if (filled($value)) {
                $rows[] = [
                    'key'        => $key,
                    'locale'     => $locale,
                    'context'    => $context,
                    'value'      => $value,
                    'created_at' => $now,
                    'updated_at' => $now,
                ];
            }
        }

        if (! empty($rows)) {
            StaticTranslation::insert($rows);
        }

        Cache::forget('static_translations_all');

        return redirect()
            ->route('admin.static-translations.index')
            ->with('success', 'Translation key created.');
    }

    public function edit(string $encodedKey)
    {
        $key  = self::decodeKey($encodedKey);
        $rows = StaticTranslation::where('key', $key)->get()->keyBy('locale');

        if ($rows->isEmpty()) {
            abort(404);
        }

        $context = $rows->first()->context ?? '';
        $locales = Locale::activeList();

        return view('admin.static-translations.edit',
            compact('key', 'encodedKey', 'rows', 'context', 'locales'));
    }

    public function update(Request $request, string $encodedKey)
    {
        $key = self::decodeKey($encodedKey);

        $request->validate([
            'context'  => ['nullable', 'string', 'max:255'],
            'values'   => ['required', 'array'],
            'values.*' => ['nullable', 'string'],
        ]);

        $context = $request->input('context');

        foreach ($request->input('values', []) as $locale => $value) {
            if (filled($value)) {
                StaticTranslation::updateOrCreate(
                    ['key' => $key, 'locale' => $locale],
                    ['context' => $context, 'value' => $value]
                );
            } else {
                // Empty value → remove that locale row
                StaticTranslation::where('key', $key)->where('locale', $locale)->delete();
            }
        }

        Cache::forget('static_translations_all');

        return redirect()
            ->route('admin.static-translations.index')
            ->with('success', 'Translation updated.');
    }

    public function destroy(string $encodedKey)
    {
        $key = self::decodeKey($encodedKey);

        StaticTranslation::where('key', $key)->delete();

        Cache::forget('static_translations_all');

        return redirect()
            ->route('admin.static-translations.index')
            ->with('success', 'Translation key deleted.');
    }
}
