<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-grey-dark">
            Edit Country
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <form method="POST"
              action="{{ route('admin.countries.update', $country) }}"
              class="bg-white shadow rounded p-6 space-y-4">
            @csrf
            @method('PATCH')

            @foreach ($locales as $localeCode => $localeName)
            <div>
                <label class="block text-sm text-grey-dark">Name ({{ $localeName }})</label>
                <input type="text"
                       name="translations[{{ $localeCode }}]"
                       value="{{ old("translations.{$localeCode}", $country->translations->firstWhere('locale', $localeCode)?->name) }}"
                       class="mt-1 block w-full border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">
                <x-input-error :messages="$errors->get('translations.'.$localeCode)" class="mt-2" />
            </div>
            @endforeach

            <div>
                <x-input-label for="iso_alpha2">ISO 3166 Alpha-2 <span class="text-status-error">*</span></x-input-label>
                <x-text-input id="iso_alpha2" name="iso_alpha2" type="text" class="mt-1 block w-full" :value="$country->iso_alpha2" maxlength="2" required />
                <x-input-error :messages="$errors->get('iso_alpha2')" class="mt-2" />
            </div>

            <div>
                <x-input-label for="country_code">Country Code <span class="text-status-error">*</span></x-input-label>
                <x-text-input id="country_code" name="country_code" type="text" class="mt-1 block w-full" :value="$country->country_code" required />
                <x-input-error :messages="$errors->get('country_code')" class="mt-2" />
            </div>

            <label class="flex items-center gap-2">
                <input type="checkbox"
                       name="is_active"
                       value="1"
                       @checked($country->is_active)>
                Active
            </label>

            <div class="flex justify-between">
                <button type="button"
                   onclick="window.location.href='{{ route('admin.countries.index') }}'"
                   class="inline-flex items-center px-2 py-2 bg-white border border-grey-medium rounded text-sm text-grey-dark uppercase shadow-sm hover:bg-grey-light">
                    Cancel
                </button>
                <x-primary-button>Update</x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>

