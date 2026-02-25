<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-grey-dark">
            New Country
        </h2>
    </x-slot>

    <div class="py-6 max-w-xl mx-auto sm:px-6 lg:px-8">
        <form method="POST"
              action="{{ route('admin.countries.store') }}"
              class="bg-white shadow rounded p-6 space-y-4">
            @csrf

            @foreach ($locales as $localeCode => $localeName)
            <div>
                <label class="block text-sm font-medium">Name ({{ $localeName }}) *</label>
                <input type="text"
                       name="translations[{{ $localeCode }}]"
                       value="{{ old("translations.{$localeCode}") }}"
                       required
                       class="w-full border rounded px-3 py-2 @error("translations.{$localeCode}") border-status-error @enderror">
                @error("translations.{$localeCode}")
                    <p class="text-status-error text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            @endforeach

            <div>
                <label class="block text-sm font-medium">ISO 3166 Alpha-2 *</label>
                <input type="text"
                       name="iso_alpha2"
                       value="{{ old('iso_alpha2') }}"
                       maxlength="2"
                       required
                       class="w-full border rounded px-3 py-2 @error('iso_alpha2') border-status-error @enderror">
                @error('iso_alpha2')
                    <p class="text-status-error text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium">Country Code *</label>
                <input type="text"
                       name="country_code"
                       value="{{ old('country_code') }}"
                       placeholder="+351"
                       required
                       class="w-full border rounded px-3 py-2 @error('country_code') border-status-error @enderror">
                @error('country_code')
                    <p class="text-status-error text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <label class="flex items-center gap-2">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', true))>
                Active
            </label>

            <div class="flex justify-between">
                <a href="{{ route('admin.countries.index') }}"
                   class="bg-grey-medium hover:bg-grey-dark text-light px-4 py-2 rounded">
                    Cancel
                </a>
                <button type="submit"
                        class="bg-accent-primary hover:bg-accent-primary/90 text-light px-4 py-2 rounded">
                    Save
                </button>
            </div>
        </form>
    </div>
</x-app-layout>

