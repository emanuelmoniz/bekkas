<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            {{ t('checkout.title') ?: 'Checkout' }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 grid md:grid-cols-3 gap-6">

            {{-- LEFT --}}
            <form method="POST"
                  action="{{ route('checkout.place') }}"
                  class="md:col-span-2 bg-white p-6 rounded shadow space-y-4"
                  x-data="{ addressMode: '{{ $addresses->isEmpty() ? 'new' : 'existing' }}', selectedAddressId: {{ $addresses->where('is_default', true)->first()->id ?? 'null' }} }">
                @csrf

                {{-- Validation Errors --}}
                @if ($errors->any())
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded relative" role="alert">
                        <strong class="font-bold">{{ t('validation.error_heading') ?: 'Please fix the following errors:' }}</strong>
                        <ul class="mt-2 list-disc list-inside text-sm">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <h3 class="font-semibold">{{ t('checkout.shipping_address') ?: 'Shipping Address' }}</h3>

                {{-- Hidden field for address_id --}}
                <input type="hidden" name="address_id" :value="addressMode === 'existing' ? selectedAddressId : ''">

                {{-- EXISTING ADDRESSES --}}
                @if ($addresses->isNotEmpty())
                    @foreach ($addresses as $address)
                        <label class="block border p-3 rounded cursor-pointer">
                            <input type="radio"
                                   name="address_selection"
                                   value="{{ $address->id }}"
                                   @click="addressMode = 'existing'; selectedAddressId = {{ $address->id }}"
                                   @checked($address->is_default)>
                            <span class="ml-2">
                                {{ $address->title }} —
                                {{ $address->address_line_1 }},
                                {{ $address->city }}
                            </span>
                        </label>
                    @endforeach

                    {{-- NEW ADDRESS OPTION --}}
                    <label class="block border p-3 rounded cursor-pointer">
                        <input type="radio"
                               name="address_selection"
                               value="new"
                               @click="addressMode = 'new'; selectedAddressId = null">
                        <span class="ml-2 font-medium">
                            {{ t('checkout.new_address') ?: 'New address' }}
                        </span>
                    </label>
                @endif

                {{-- NEW ADDRESS FORM --}}
                <div x-show="addressMode === 'new'" x-cloak class="space-y-2 pt-4">
                    <h4 class="font-medium">{{ t('checkout.new_address_details') ?: 'New address details' }}</h4>

                    <input name="title" placeholder="{{ t('checkout.address_title') ?: 'Address name' }}" class="border rounded px-3 py-2 w-full" :disabled="addressMode !== 'new'">
                    <input name="nif" placeholder="{{ t('checkout.nif_optional') ?: 'NIF (optional)' }}" class="border rounded px-3 py-2 w-full" :disabled="addressMode !== 'new'">
                    <input name="phone" placeholder="{{ t('checkout.phone_optional') ?: 'Phone (optional)' }}" class="border rounded px-3 py-2 w-full" :disabled="addressMode !== 'new'">
                    <input name="address_line_1" placeholder="{{ t('checkout.address_line_1') ?: 'Address line 1' }}" class="border rounded px-3 py-2 w-full" :disabled="addressMode !== 'new'">
                    <input name="address_line_2" placeholder="{{ t('checkout.validation.address_line_2_optional') ?: 'Address line 2 (optional)' }}" class="border rounded px-3 py-2 w-full" :disabled="addressMode !== 'new'">
                    <input name="postal_code" placeholder="{{ t('checkout.postal_code') ?: 'Postal code' }}" class="border rounded px-3 py-2 w-full" :disabled="addressMode !== 'new'">
                    <input name="city" placeholder="{{ t('checkout.city') ?: 'City' }}" class="border rounded px-3 py-2 w-full" :disabled="addressMode !== 'new'">
                    <select name="country_id" class="border rounded px-3 py-2 w-full" :disabled="addressMode !== 'new'">
                        <option value="">{{ t('checkout.country') ?: 'Country' }}</option>
                        @foreach(\App\Models\Country::where('is_active', true)->orderBy('name_pt')->get() as $country)
                            <option value="{{ $country->id }}">
                                {{ $country->name_pt }}
                            </option>
                        @endforeach
                    </select>
                    
                    <label class="flex items-center gap-2 pt-2">
                        <input type="checkbox" name="is_default" value="1" class="rounded" :disabled="addressMode !== 'new'">
                        <span class="text-sm">{{ t('checkout.set_as_default') ?: 'Set as default address' }}</span>
                    </label>
                </div>

                <button class="bg-indigo-600 text-white px-6 py-3 rounded mt-6">
                    {{ t('checkout.place_order') ?: 'Place Order' }}
                </button>
            </form>

            {{-- RIGHT --}}
            <div class="bg-white p-6 rounded shadow space-y-2">
                <h3 class="font-semibold">{{ t('checkout.summary') ?: 'Summary' }}</h3>

                @foreach ($items as $item)
                    <div class="text-sm flex justify-between">
                        <span>{{ optional($item['product']->translation())->name }} × {{ $item['quantity'] }}</span>
                        <span>€{{ number_format($item['gross'], 2) }}</span>
                    </div>
                @endforeach

                <hr>

                <div class="text-sm text-gray-500 flex justify-between">
                    <span>{{ t('checkout.products_tax') ?: 'Products tax' }}</span>
                    <span>€{{ number_format($productsTax, 2) }}</span>
                </div>

                <div class="flex justify-between">
                    <span>{{ t('checkout.shipping') ?: 'Shipping' }}</span>
                    <span>€{{ number_format($shipping['gross'], 2) }}</span>
                </div>

                <div class="text-sm text-gray-500 flex justify-between">
                    <span>{{ t('checkout.shipping_tax') ?: 'Shipping tax' }}</span>
                    <span>€{{ number_format($shipping['tax'], 2) }}</span>
                </div>

                <div class="text-sm text-gray-500 flex justify-between">
                    <span>{{ t('checkout.total_tax') ?: 'Total tax' }}</span>
                    <span>€{{ number_format($totalTax, 2) }}</span>
                </div>

                <div class="font-semibold flex justify-between pt-2">
                    <span>{{ t('checkout.total') ?: 'Total' }}</span>
                    <span>€{{ number_format($totalGross, 2) }}</span>
                </div>
            </div>

        </div>
    </div>
</x-app-layout>
