<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-grey-dark">
            Edit Region
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <form method="POST"
              action="{{ route('admin.regions.update', $region) }}"
              class="bg-white shadow rounded p-6 space-y-4">
            @csrf
            @method('PATCH')

            <div>
                <label class="block text-sm font-medium">Country *</label>
                <select name="country_id" required class="w-full border rounded px-3 py-2">
                    @foreach ($countries as $country)
                        <option value="{{ $country->id }}" @selected(old('country_id', $region->country_id) == $country->id)>
                            {{ $country->name }}
                        </option>
                    @endforeach
                </select>
                @error('country_id')
                    <p class="text-status-error text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            @foreach ($locales as $localeCode => $localeName)
            @php $existing = $region->translations->where('locale', $localeCode)->first(); @endphp
            <div>
                <label class="block text-sm font-medium">Name ({{ $localeName }}) *</label>
                <input type="text"
                       name="translations[{{ $localeCode }}]"
                       value="{{ old("translations.{$localeCode}", $existing?->name) }}"
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
                       value="{{ old('postal_code_from', $region->postal_code_from) }}"
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
                       value="{{ old('postal_code_to', $region->postal_code_to) }}"
                       required
                       class="w-full border rounded px-3 py-2 @error('postal_code_to') border-status-error @enderror">
                @error('postal_code_to')
                    <p class="text-status-error text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-2">Default Shipping Tier</label>
                <select name="default_shipping_tier_id" class="w-full border rounded px-3 py-2">
                    <option value="">— No Default (use global) —</option>
                    @foreach($shippingTiers as $tier)
                        <option value="{{ $tier->id }}" 
                                @selected(old('default_shipping_tier_id', $defaultShippingTierId) == $tier->id)>
                            {{ $tier->translation()?->name }} ({{ $tier->weight_from }}-{{ $tier->weight_to }}g, {{ $tier->shipping_days }} days)
                        </option>
                    @endforeach
                </select>
                @error('default_shipping_tier_id')
                    <p class="text-status-error text-sm mt-1">{{ $message }}</p>
                @enderror
                <p class="text-sm text-grey-dark mt-1">
                    Only tiers assigned to this region are shown. Assign tiers from the Shipping Tiers admin section.
                </p>
            </div>

            <label class="flex items-center gap-2">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $region->is_active))>
                Active
            </label>

            <div class="flex justify-between">
                <a href="{{ route('admin.regions.index') }}"
                   class="bg-grey-medium hover:bg-grey-dark text-light px-4 py-2 rounded">
                    Cancel
                </a>
                <button type="submit"
                        class="bg-accent-primary hover:bg-accent-primary/90 text-light px-4 py-2 rounded">
                    Update
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
