<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-grey-dark">Edit Translation Key</h2>
    </x-slot>

    <div class="py-6 max-w-3xl mx-auto sm:px-6 lg:px-8">
        <form method="POST"
              action="{{ route('admin.static-translations.update', $encodedKey) }}"
              class="bg-white p-6 rounded shadow space-y-5">
            @csrf
            @method('PUT')

            @if ($errors->any())
                <div class="p-3 bg-red-50 text-red-700 rounded text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Key (read-only) --}}
            <div>
                <label class="block font-semibold mb-1 text-sm">Key</label>
                <input disabled
                       class="w-full border rounded px-3 py-2 bg-grey-light text-grey-dark font-mono text-sm"
                       value="{{ $key }}">
            </div>

            {{-- Context --}}
            <div>
                <label class="block font-semibold mb-1 text-sm" for="context">Context</label>
                <input id="context"
                       name="context"
                       value="{{ old('context', $context) }}"
                       placeholder="e.g. nav, checkout, footer…"
                       class="w-full border rounded px-3 py-2 text-sm">
                <p class="text-xs text-grey-dark mt-1">Used to group keys in the audit command. Leave blank if unsure.</p>
            </div>

            <hr>

            {{-- Per-locale values --}}
            @foreach ($locales as $locale => $label)
                <div>
                    <label class="block font-semibold mb-1 text-sm" for="value_{{ $locale }}">
                        {{ $label }}
                        <span class="font-normal text-grey-dark">({{ $locale }})</span>
                        @if (! $rows->has($locale))
                            <span class="ml-1 text-xs text-red-500 font-normal">missing</span>
                        @endif
                    </label>
                    <textarea id="value_{{ $locale }}"
                              name="values[{{ $locale }}]"
                              rows="3"
                              class="w-full border rounded px-3 py-2 text-sm"
                              placeholder="Leave empty to remove this locale row">{{ old('values.'.$locale, $rows[$locale]->value ?? '') }}</textarea>
                </div>
            @endforeach

            <div class="flex justify-between items-center pt-2">
                <a href="{{ route('admin.static-translations.index') }}"
                   class="px-4 py-2 border rounded text-sm">← Back</a>
                <button class="px-6 py-2 bg-accent-primary hover:bg-accent-primary/90 text-light rounded text-sm font-medium">
                    Save
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
