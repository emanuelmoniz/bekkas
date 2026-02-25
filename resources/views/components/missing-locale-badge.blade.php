{{--
    Missing-locale badge component.
    Shows nothing when all active locales are covered.
    Shows a red pill when one or more translations are absent.

    Usage: <x-missing-locale-badge :model="$product" />
    The model must have the `translations` relationship loaded.
--}}
@if ($hasMissing())
    <span
        class="inline-flex items-center gap-1 px-2 py-0.5 rounded-full
               bg-red-100 text-red-800 text-xs font-semibold leading-tight cursor-help"
        title="Missing translations: {{ $missingLocales->implode(', ') }}"
    >
        ⚠ {{ $missingLocales->count() }}
    </span>
@endif
