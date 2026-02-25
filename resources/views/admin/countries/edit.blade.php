<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-grey-dark">
            Edit Country
        </h2>
    </x-slot>

    <div class="py-6 max-w-xl mx-auto sm:px-6 lg:px-8">
        <form method="POST"
              action="{{ route('admin.countries.update', $country) }}"
              class="bg-white shadow rounded p-6 space-y-4">
            @csrf
            @method('PATCH')

            @foreach ($locales as $localeCode => $localeName)
            <div>
                <label class="block text-sm font-medium">Name ({{ $localeName }})</label>
                <input type="text"
                       name="translations[{{ $localeCode }}]"
                       value="{{ old("translations.{$localeCode}", $country->translations->firstWhere('locale', $localeCode)?->name) }}"
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
                       value="{{ $country->iso_alpha2 }}"
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
                       value="{{ $country->country_code }}"
                       required
                       class="w-full border rounded px-3 py-2 @error('country_code') border-status-error @enderror">
                @error('country_code')
                    <p class="text-status-error text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <label class="flex items-center gap-2">
                <input type="checkbox"
                       name="is_active"
                       value="1"
                       @checked($country->is_active)>
                Active
            </label>

            <div class="flex justify-between">
                <a href="{{ route('admin.countries.index') }}"
                   class="bg-grey-medium hover:bg-grey-dark text-light px-4 py-2 rounded">
                    Cancel
                </a>
                <button class="bg-accent-primary text-light px-4 py-2 rounded">
                    Update
                </button>
            </div>
        </form>
    </div>
</x-app-layout>

