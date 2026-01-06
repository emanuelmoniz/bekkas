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
                  x-data="{ addressMode: '{{ $addresses->isEmpty() ? 'new' : 'existing' }}' }">
                @csrf

                <h3 class="font-semibold">{{ t('checkout.shipping_address') ?: 'Shipping Address' }}</h3>

                {{-- EXISTING ADDRESSES --}}
                @if ($addresses->isNotEmpty())
                    @foreach ($addresses as $address)
                        <label class="block border p-3 rounded cursor-pointer">
                            <input type="radio"
                                   name="address_id"
                                   value="{{ $address->id }}"
                                   x-model="addressMode"
                                   x-bind:value="'existing'"
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
                               name="address_mode"
                               value="new"
                               x-model="addressMode">
                        <span class="ml-2 font-medium">
                            {{ t('checkout.new_address') ?: 'New address' }}
                        </span>
                    </label>
                @endif

                {{-- NEW ADDRESS FORM --}}
                <div x-show="addressMode === 'new'" x-cloak class="space-y-2 pt-4">
                    <h4 class="font-medium">{{ t('checkout.new_address_details') ?: 'New address details' }}</h4>

                    <input name="title" placeholder="{{ t('checkout.title') ?: 'Title' }}" class="border rounded px-3 py-2 w-full">
                    <input name="nif" placeholder="{{ t('checkout.nif') ?: 'NIF' }}" class="border rounded px-3 py-2 w-full">
                    <input name="address_line_1" placeholder="{{ t('checkout.address_line_1') ?: 'Address line 1' }}" class="border rounded px-3 py-2 w-full">
                    <input name="address_line_2" placeholder="{{ t('checkout.address_line_2') ?: 'Address line 2' }}" class="border rounded px-3 py-2 w-full">
                    <input name="postal_code" placeholder="{{ t('checkout.postal_code') ?: 'Postal code' }}" class="border rounded px-3 py-2 w-full">
                    <input name="city" placeholder="{{ t('checkout.city') ?: 'City' }}" class="border rounded px-3 py-2 w-full">
                    <input name="country" placeholder="{{ t('checkout.country') ?: 'Country' }}" class="border rounded px-3 py-2 w-full">
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
