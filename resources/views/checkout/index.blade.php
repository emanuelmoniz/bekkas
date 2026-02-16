<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            {{ t('checkout.title') ?: 'Checkout' }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 grid md:grid-cols-3 gap-6" x-data="checkoutData()">

            {{-- LEFT --}}
            <form method="POST"
                  action="{{ route('checkout.place') }}"
                  class="md:col-span-2 bg-white p-6 rounded shadow space-y-4">
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
                                   data-country-id="{{ $address->country_id }}"
                                   data-postal-code="{{ $address->postal_code }}"
                                   @click="addressMode = 'existing'; selectedAddressId = {{ $address->id }}; onAddressChange()"
                                   @checked($address->is_default || (!$addresses->where('is_default', true)->first() && $loop->first))>
                            <span class="ml-2">
                                {{ $address->title }} —
                                {{ $address->address_line_1 }},
                                {{ $address->city }}
                                ({{ $address->postal_code }})
                            </span>
                        </label>
                    @endforeach

                    {{-- NEW ADDRESS OPTION --}}
                    <label class="block border p-3 rounded cursor-pointer">
                        <input type="radio"
                               name="address_selection"
                               value="new"
                               @click="addressMode = 'new'; selectedAddressId = null; onAddressChange()">
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
                    <input name="postal_code" 
                           x-model="newPostalCode"
                           @input="updateShippingTiersForNewAddress()"
                           placeholder="{{ t('checkout.postal_code') ?: 'Postal code' }}" 
                           class="border rounded px-3 py-2 w-full" 
                           :disabled="addressMode !== 'new'">
                    <input name="city" placeholder="{{ t('checkout.city') ?: 'City' }}" class="border rounded px-3 py-2 w-full" :disabled="addressMode !== 'new'">
                    <select name="country_id" x-model="newCountryId" @change="updateShippingTiersForNewAddress()" class="border rounded px-3 py-2 w-full" :disabled="addressMode !== 'new'">
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

                {{-- SHIPPING TIER SELECTION --}}
                <div x-show="addressMode === 'new' && availableTiers.length === 0 && !qualifiesForFreeShipping" x-cloak class="bg-amber-50 border border-amber-200 rounded p-4 mt-6">
                    <p class="text-sm text-amber-800">
                        {{ t('checkout.address_required_for_shipping') ?: 'Please fill the address form so we can show the available shipping options.' }}
                    </p>
                </div>

                <div x-show="availableTiers.length > 0" x-cloak class="mt-6">
                    <h3 class="font-semibold mb-3">{{ t('checkout.select_shipping_method') ?: 'Select Shipping Method' }}</h3>
                    
                    <div x-show="qualifiesForFreeShipping" class="bg-green-50 border border-green-200 rounded p-3 mb-3">
                        <p class="text-sm text-green-800">
                            {{ t('checkout.free_shipping_message') ?: 'Your order total exceeds' }} €<span x-text="freeShippingOver.toFixed(2)"></span>. 
                            {{ t('checkout.free_shipping_qualified') ?: 'You qualify for free shipping! You can also choose a faster shipping method below (additional cost applies).' }}
                        </p>
                    </div>
                    
                    <template x-for="tier in availableTiers" :key="tier.id">
                        <label class="block border p-3 rounded cursor-pointer mb-2 hover:bg-gray-50" :class="{ 'border-green-500 bg-green-50': tier.is_free }">
                            <input type="radio"
                                   name="shipping_tier_id"
                                   :value="tier.id"
                                   @click="selectTier(tier)"
                                   :checked="selectedTierId === tier.id">
                            <span class="ml-2">
                                <span class="font-medium" x-text="tier.name"></span>
                                <span x-show="tier.is_free" class="text-green-600 font-semibold ml-1">({{ t('checkout.free') ?: 'FREE' }})</span>
                                <span x-show="!tier.is_free">
                                    —
                                    €<span x-text="Number(tier.cost_gross).toFixed(2)"></span>
                                </span>
                                (<span x-text="tier.shipping_days"></span> {{ t('products.working_days') ?: 'working days' }})
                            </span>
                        </label>
                    </template>
                </div>

                <div x-show="!qualifiesForFreeShipping && addressMode === 'existing' && availableTiers.length === 0" x-cloak class="bg-amber-50 border border-amber-200 rounded p-4 mt-6">
                    <p class="text-sm text-amber-800">
                        {{ t('checkout.no_shipping_available') ?: 'No shipping options available for your address. Please contact us.' }}
                    </p>
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

                @if(config('app.tax_enabled', env('APP_TAX_ENABLED', true)))
                    <div class="text-sm text-gray-500 flex justify-between">
                        <span>{{ t('checkout.products_tax') ?: 'Products tax' }}</span>
                        <span>€{{ number_format($productsTax, 2) }}</span>
                    </div>

                    <div class="flex justify-between">
                        <span>{{ t('checkout.shipping') ?: 'Shipping' }}</span>
                        <span x-text="'€' + Number(shipping?.gross ?? 0).toFixed(2)" x-cloak>€{{ number_format($shipping['gross'], 2) }}</span>
                    </div>

                    <div class="text-sm text-gray-500 flex justify-between">
                        <span>{{ t('checkout.shipping_tax') ?: 'Shipping tax' }}</span>
                        <span x-text="'€' + Number(shipping?.tax ?? 0).toFixed(2)" x-cloak>€{{ number_format($shipping['tax'], 2) }}</span>
                    </div>

                    <div class="text-sm text-gray-500 flex justify-between">
                        <span>{{ t('checkout.total_tax') ?: 'Total tax' }}</span>
                        <span x-text="'€' + totalTax" x-cloak>€{{ number_format($totalTax, 2) }}</span>
                    </div>
                @else
                    <div class="text-sm text-gray-500">
                        {{ t('tax.included_in_price') ?: 'All taxes are included in the price' }}
                    </div>

                    <div class="flex justify-between">
                        <span>{{ t('checkout.shipping') ?: 'Shipping' }}</span>
                        <span x-text="'€' + Number(shipping?.gross ?? 0).toFixed(2)" x-cloak>€{{ number_format($shipping['gross'], 2) }}</span>
                    </div>
                @endif

                <div class="font-semibold flex justify-between pt-2">
                    <span>{{ t('checkout.total') ?: 'Total' }}</span>
                    <span x-text="'€' + totalGross" x-cloak>€{{ number_format($totalGross, 2) }}</span>
                </div>
            </div>

        </div>
    </div>

    <script>
        function checkoutData() {
            return {
                addressMode: '{{ $addresses->isEmpty() ? "new" : "existing" }}',
                selectedAddressId: {{ $addresses->where('is_default', true)->first()->id ?? $addresses->first()->id ?? 'null' }},
                newPostalCode: '',
                newCountryId: '',
                qualifiesForFreeShipping: {{ $qualifiesForFreeShipping ? 'true' : 'false' }},
                freeShippingOver: {{ $freeShippingOver }},
                allTiers: @json($availableShippingTiersFormatted),
                addresses: @json($addresses->map(fn($a) => ['id' => $a->id, 'country_id' => $a->country_id, 'postal_code' => $a->postal_code])->toArray()),
                availableTiers: [],
                selectedTierId: {{ $selectedShippingTier ? $selectedShippingTier->id : 'null' }},
                shipping: @json($shipping),
                productsGross: {{ $productsGross }},
                productsTax: {{ $productsTax }},
                filterTiersByCountryAndPostal(tiers, countryId, postalCode) {
                    if (!countryId) return [];
                    // Filter tiers by regions that belong to the selected country
                    const tiersForCountry = tiers.filter(tier => {
                        if (!tier.regions || !Array.isArray(tier.regions)) return false;
                        return tier.regions.some(region => region.country_id == countryId);
                    });
                    if (!postalCode || postalCode.length < 4) return tiersForCountry;
                    // Normalize postal code to first 4 digits for comparison
                    const normalizedPostal = postalCode.length > 4 ? postalCode.substring(0, 4) : postalCode;
                    // Further filter by postal code
                    return tiersForCountry.filter(tier =>
                        tier.regions.some(region => region.country_id == countryId && normalizedPostal >= region.postal_code_from.substring(0, 4) && normalizedPostal <= region.postal_code_to.substring(0, 4))
                    );
                },
                filterTiers(tiers, qualifiesForFreeShipping) {
                    if (!qualifiesForFreeShipping) return tiers;
                    // Find the free tier (is_free: true)
                    const freeTier = tiers.find(t => t.is_free);
                    if (!freeTier) return tiers;
                    // Only show the free tier and any active tiers that are faster
                    return [freeTier, ...tiers.filter(t => !t.is_free && t.shipping_days < freeTier.shipping_days)];
                },
                init() {
                    // On page load, filter tiers for display
                    if (this.addressMode === 'existing' && this.selectedAddressId) {
                        const address = this.addresses.find(a => a.id == this.selectedAddressId);
                        if (address) {
                            this.fetchShippingTiers(address.postal_code, this.selectedAddressId, null);
                        }
                    } else if (this.addressMode === 'new') {
                        this.updateShippingTiersForNewAddress();
                    }
                },
                updateShippingTiersForNewAddress() {
                    this.newPostalCode = this.newPostalCode.trim();
                    if (!this.newCountryId || !this.newPostalCode || this.newPostalCode.length < 4) {
                        this.availableTiers = [];
                        this.selectedTierId = null;
                        this.shipping = { gross: 0, net: 0, tax: 0 };
                        return;
                    }
                    // Use AJAX to get tiers
                    this.fetchShippingTiers(this.newPostalCode, null, this.newCountryId);
                },
                async fetchShippingTiers(postalCode, addressId, countryId) {
                    try {
                        const response = await fetch('{{ route("checkout.shipping-tiers") }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': '{{ csrf_token() }}'
                            },
                            body: JSON.stringify({
                                postal_code: postalCode,
                                address_id: addressId,
                                country_id: countryId
                            })
                        });
                        const data = await response.json();
                        this.availableTiers = this.filterTiers(data.tiers, this.qualifiesForFreeShipping);
                        if (this.availableTiers.length > 0) {
                            this.selectTier(this.availableTiers[0]);
                        } else {
                            this.selectedTierId = null;
                            this.shipping = { gross: 0, net: 0, tax: 0 };
                        }
                    } catch (error) {
                        console.error('Error fetching shipping tiers:', error);
                    }
                },
                selectTier(tier) {
                    this.selectedTierId = tier.id;
                    this.shipping = tier.shipping;
                },
                async onAddressChange() {
                    if (this.addressMode === 'existing' && this.selectedAddressId) {
                        const address = this.addresses.find(a => a.id == this.selectedAddressId);
                        if (address) {
                            this.fetchShippingTiers(address.postal_code, this.selectedAddressId, null);
                        }
                    } else if (this.addressMode === 'new') {
                        this.updateShippingTiersForNewAddress();
                    }
                },
                get totalGross() {
                    return (Number(this.productsGross) + Number(this.shipping.gross)).toFixed(2);
                },
                get totalTax() {
                    return (Number(this.productsTax) + Number(this.shipping.tax)).toFixed(2);
                }
            }
        }
    </script>

</x-app-layout>
