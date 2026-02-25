<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-grey-dark">New Translation Key</h2>
    </x-slot>

    <div class="py-6 max-w-3xl mx-auto sm:px-6 lg:px-8">
        <form method="POST"
              action="{{ route('admin.static-translations.store') }}"
              class="bg-white p-6 rounded shadow space-y-5">
            @csrf

            @if ($errors->any())
                <div class="p-3 bg-red-50 text-red-700 rounded text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            {{-- Key --}}
            <div>
                <label class="block font-semibold mb-1 text-sm" for="key">Key</label>
                <input id="key"
                       name="key"
                       value="{{ old('key') }}"
                       placeholder="e.g. nav.shop or checkout.pay.button"
                       class="w-full border rounded px-3 py-2 font-mono text-sm @error('key') border-red-400 @enderror">
                @error('key')
                    <p class="text-xs text-red-600 mt-1">{{ $message }}</p>
                @enderror
            </div>

            {{-- Context --}}
            <div>
                <label class="block font-semibold mb-1 text-sm" for="context">Context</label>
                <input id="context"
                       name="context"
                       value="{{ old('context') }}"
                       placeholder="e.g. nav, checkout, footer…"
                       class="w-full border rounded px-3 py-2 text-sm">
                <p class="text-xs text-grey-dark mt-1">Optional — groups keys for the audit command.</p>
            </div>

            <hr>

            {{-- Per-locale values --}}
            @foreach ($locales as $locale => $label)
                <div>
                    <label class="block font-semibold mb-1 text-sm" for="value_{{ $locale }}">
                        {{ $label }}
                        <span class="font-normal text-grey-dark">({{ $locale }})</span>
                    </label>
                    <textarea id="value_{{ $locale }}"
                              name="values[{{ $locale }}]"
                              rows="3"
                              class="w-full border rounded px-3 py-2 text-sm"
                              placeholder="Leave empty to skip this locale">{{ old('values.'.$locale) }}</textarea>
                </div>
            @endforeach

            <div class="flex justify-between items-center pt-2">
                <a href="{{ route('admin.static-translations.index') }}"
                   class="px-4 py-2 border rounded text-sm">← Back</a>
                <button class="px-6 py-2 bg-accent-primary hover:bg-accent-primary/90 text-light rounded text-sm font-medium">
                    Create
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
