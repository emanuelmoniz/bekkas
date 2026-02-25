<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-grey-dark">
            New Region
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <form method="POST"
              action="{{ route('admin.regions.store') }}"
              class="bg-white shadow rounded p-6 space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium">Country *</label>
                <select name="country_id" required class="w-full border rounded px-3 py-2">
                    <option value="">Select a country</option>
                    @foreach ($countries as $country)
                        <option value="{{ $country->id }}" @selected(old('country_id') == $country->id)>
                            {{ $country->name }}
                        </option>
                    @endforeach
                </select>
                @error('country_id')
                    <p class="text-status-error text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

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
                <label class="block text-sm font-medium">Postal Code From *</label>
                <input type="text"
                       name="postal_code_from"
                       value="{{ old('postal_code_from') }}"
                       placeholder="1000-001"
                       required
                       class="w-full border rounded px-3 py-2 @error('postal_code_from') border-status-error @enderror">
                @error('postal_code_from')
                    <p class="text-status-error text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium">Postal Code To *</label>
                <input type="text"
                       name="postal_code_to"
                       value="{{ old('postal_code_to') }}"
                       placeholder="1999-999"
                       required
                       class="w-full border rounded px-3 py-2 @error('postal_code_to') border-status-error @enderror">
                @error('postal_code_to')
                    <p class="text-status-error text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <label class="flex items-center gap-2">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', true))>
                Active
            </label>

            <div class="flex justify-between">
                <a href="{{ route('admin.regions.index') }}"
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
