<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-grey-dark">Edit Translation Key</h2>
    </x-slot>

    <div class="py-6 max-w-4xl mx-auto sm:px-6 lg:px-8">
        <form method="POST"
              action="{{ route('admin.static-translations.update', $encodedKey) }}"
              class="bg-white p-6 rounded shadow space-y-5">
            @csrf
            @method('PUT')

            {{-- Key (read-only) --}}
            <div>
                <x-input-label>Key</x-input-label>
                <input disabled
                       class="mt-1 block w-full border-grey-medium bg-grey-light text-grey-dark font-mono rounded-md shadow-sm text-sm"
                       value="{{ $key }}">
            </div>

            {{-- Context --}}
            <div>
                <x-input-label for="context">Context</x-input-label>
                <x-text-input id="context" name="context" type="text" class="mt-1 block w-full" :value="old('context', $context)" placeholder="e.g. nav, checkout, footer…" />
                <p class="text-xs text-grey-dark mt-1">Used to group keys in the audit command. Leave blank if unsure.</p>
            </div>

            <hr>

            {{-- Per-locale values --}}
            @foreach ($locales as $locale => $label)
                <div>
                    <x-input-label for="value_{{ $locale }}">
                        {{ $label }} <span class="font-normal text-grey-dark">({{ $locale }})</span>
                        @if (! $rows->has($locale))
                            <span class="ml-1 text-xs text-status-error font-normal">missing</span>
                        @endif
                    </x-input-label>
                    <textarea id="value_{{ $locale }}"
                              name="values[{{ $locale }}]"
                              rows="3"
                              class="mt-1 block w-full border-grey-medium focus:border-accent-primary focus:ring-accent-primary rounded-md shadow-sm text-sm"
                              placeholder="Leave empty to remove this locale row">{{ old('values.'.$locale, $rows[$locale]->value ?? '') }}</textarea>
                </div>
            @endforeach

            <div class="flex justify-between items-center pt-2">
                <a href="{{ route('admin.static-translations.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-white border border-grey-medium rounded-md font-semibold text-xs text-grey-dark uppercase tracking-widest shadow-sm hover:bg-grey-light transition ease-in-out duration-150">
                    ← Back
                </a>
                <x-primary-button>Save</x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>
