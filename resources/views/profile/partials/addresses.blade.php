<section>
    <header class="mb-4">
        <h2 class="text-lg font-medium text-dark">{{ t('profile.addresses') ?: 'Addresses' }}</h2>
    </header>

    <div class="space-y-4 mb-6">
        @php $addressCount = $addresses->count(); @endphp

        @forelse ($addresses as $address)
            <div class="border p-4 rounded space-y-3">
                {{-- UPDATE FORM --}}
                <form method="POST" action="{{ route('addresses.update', $address) }}" class="space-y-2">
                    @csrf
                    @method('PATCH')

                    <div class="grid grid-cols-2 gap-2">
                        <input name="title" value="{{ $address->title }}" placeholder="{{ t('profile.address_title') ?: 'Address name' }}" class="border rounded px-2 py-1" required>
                        <input name="nif" value="{{ $address->nif }}" placeholder="{{ t('profile.address_nif_optional') ?: 'NIF (optional)' }}" class="border rounded px-2 py-1">
                        <input name="phone" value="{{ $address->phone }}" placeholder="{{ t('profile.address_phone_optional') ?: 'Phone (optional)' }}" class="border rounded px-2 py-1">
                        <input name="address_line_1" value="{{ $address->address_line_1 }}" placeholder="{{ t('profile.address_line_1') ?: 'Address line 1' }}" class="border rounded px-2 py-1" required>
                        <input name="address_line_2" value="{{ $address->address_line_2 }}" placeholder="{{ t('profile.address_line_2_optional') ?: 'Address line 2 (optional)' }}" class="border rounded px-2 py-1">
                        <input name="postal_code" value="{{ $address->postal_code }}" placeholder="{{ t('profile.address_postal_code') ?: 'Postal code' }}" class="border rounded px-2 py-1" required>
                        <input name="city" value="{{ $address->city }}" class="border rounded px-2 py-1" required>
                        <select name="country_id" class="border rounded px-2 py-1" required>
                            @foreach(\App\Models\Country::with('translations')->where('is_active', true)->orderByTranslatedName()->get() as $country)
                                <option value="{{ $country->id }}" @selected($address->country_id == $country->id)>
                                    {{ $country->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <label class="flex items-center gap-2">
                        <input type="checkbox"
                               name="is_default"
                               value="1"
                               @checked($address->is_default)
                               @disabled($address->is_default)>
                        {{ t('profile.default_address') ?: 'Default address' }}
                    </label>

                    <button class="bg-accent-primary text-light px-3 py-1 rounded">
                        {{ t('profile.save') ?: 'Save' }}
                    </button>
                </form>

                {{-- DELETE FORM (SEPARATE) --}}
                <form method="POST"
                      action="{{ route('addresses.destroy', $address) }}"
                      onsubmit="return confirm('Delete this address?')">
                    @csrf
                    @method('DELETE')

                    <button class="text-grey-dark text-sm">
                        {{ t('profile.delete') ?: 'Delete' }}
                    </button>
                </form>
            </div>
        @empty
            <p class="text-sm text-grey-dark">{{ t('profile.no_addresses') ?: 'No addresses yet.' }}</p>
        @endforelse
    </div>

    {{-- ADD NEW ADDRESS --}}
    <form method="POST" action="{{ route('addresses.store') }}" class="border p-4 rounded space-y-2">
        @csrf
        <h3 class="font-medium">{{ t('profile.add_new_address') ?: 'Add new address' }}</h3>

        <div class="grid grid-cols-2 gap-2">
            <input name="title" placeholder="{{ t('profile.address_title') ?: 'Address name' }}" class="border rounded px-2 py-1" required>
            <input name="nif" placeholder="{{ t('profile.address_nif_optional') ?: 'NIF (optional)' }}" class="border rounded px-2 py-1">
            <input name="phone" placeholder="{{ t('profile.address_phone_optional') ?: 'Phone (optional)' }}" class="border rounded px-2 py-1">
            <input name="address_line_1" placeholder="{{ t('profile.address_line_1') ?: 'Address line 1' }}" class="border rounded px-2 py-1" required>
            <input name="address_line_2" placeholder="{{ t('profile.address_line_2_optional') ?: 'Address line 2 (optional)' }}" class="border rounded px-2 py-1">
            <input name="postal_code" placeholder="{{ t('profile.address_postal_code') ?: 'Postal code' }}" class="border rounded px-2 py-1" required>
            <input name="city" placeholder="{{ t('profile.address_city') ?: 'City' }}" class="border rounded px-2 py-1" required>
            <select name="country_id" class="border rounded px-2 py-1" required>
                <option value="">{{ t('profile.address_country') ?: 'Country' }}</option>
                @foreach(\App\Models\Country::with('translations')->where('is_active', true)->orderByTranslatedName()->get() as $country)
                    <option value="{{ $country->id }}" @selected(old('country_id') == $country->id)>
                        {{ $country->name }}
                    </option>
                @endforeach
            </select>
        </div>

        <label class="flex items-center gap-2 mt-2">
            <input type="checkbox" name="is_default" value="1">
            {{ t('profile.default_address') ?: 'Default address' }}
        </label>

        <button class="bg-accent-primary text-light px-4 py-2 rounded mt-2">
            {{ t('profile.add_address') ?: 'Add Address' }}
        </button>
    </form>
</section>
