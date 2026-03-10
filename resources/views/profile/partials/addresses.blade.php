<section>
    <header class="mb-4">
        <h2 class="text-lg font-medium text-dark">{{ t('profile.addresses') ?: 'Addresses' }}</h2>
    </header>

    @php
        $addressFormContext = session('address_form_context');
        $addressEditId = (int) session('address_edit_id');
    @endphp

    <x-validation-errors-alert :fields="['title','nif','phone','address_line_1','address_line_2','postal_code','city','country_id']" />

    <div class="space-y-4 mb-6">
        @php $addressCount = $addresses->count(); @endphp

        @forelse ($addresses as $address)
            <details id="address-{{ $address->id }}" class="border rounded" @if($address->is_default || ($addressFormContext === 'update' && $addressEditId === (int) $address->id)) open @endif>
                <summary class="px-4 py-3 cursor-pointer font-medium text-dark">
                    {{ $address->title ?: (t('address_form.new_address') ?: 'New address') }}
                </summary>

                <div class="p-4 border-t space-y-3">
                    {{-- UPDATE FORM --}}
                    <form method="POST" action="{{ route('addresses.update', $address) }}" class="space-y-2" novalidate>
                        @csrf
                        @method('PATCH')

                        {{-- address fields --}}
                        @include('partials.address-form-fields', [
                            'address' => $address,
                            // update form fields are always enabled
                            'disabledExpr' => null,
                            'inputClasses' => 'border rounded px-3 py-2 w-full',
                            'useOldInput' => $addressFormContext === 'update' && $addressEditId === (int) $address->id,
                            'requiredFields' => ['title','address_line_1','postal_code','city','country_id'],
                        ])

                        <label class="flex items-center gap-2">
                            <input type="checkbox"
                                   name="is_default"
                                   value="1"
                                   @checked($address->is_default)
                                   @disabled($address->is_default)>
                            {{ t('profile.default_address') ?: 'Default address' }}
                        </label>

                        <x-primary-cta>{{ t('profile.save') ?: 'Save' }}</x-primary-cta>
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
            </details>
        @empty
            <p class="text-sm text-grey-dark">{{ t('profile.no_addresses') ?: 'No addresses yet.' }}</p>
        @endforelse
    </div>

    {{-- ADD NEW ADDRESS --}}
    <details id="new-address-form" class="border rounded" @if($addressFormContext === 'store' && (old('title') || old('address_line_1') || old('postal_code') || old('city') || old('country_id'))) open @endif>
        <summary class="px-4 py-3 cursor-pointer font-medium text-dark">
            {{ t('address_form.new_address') ?: 'New address' }}
        </summary>

        <form method="POST" action="{{ route('addresses.store') }}" class="p-4 border-t space-y-2" novalidate>
            @csrf

            {{-- address fields for new entry --}}
            @include('partials.address-form-fields', [
                'address' => null,
                'inputClasses' => 'border rounded px-3 py-2 w-full',
                'useOldInput' => $addressFormContext === 'store',
                'requiredFields' => ['title','address_line_1','postal_code','city','country_id'],
            ])

            <label class="flex items-center gap-2 mt-2">
                <input type="checkbox" name="is_default" value="1">
                {{ t('profile.default_address') ?: 'Default address' }}
            </label>

            <x-primary-cta>{{ t('profile.add_address') ?: 'Add Address' }}</x-primary-cta>
        </form>
    </details>
</section>
