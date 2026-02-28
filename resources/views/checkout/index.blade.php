<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-grey-dark">
            {{ t('checkout.title') ?: 'Checkout' }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid lg:grid-cols-3 gap-6" x-data="checkoutData()">

            {{-- LEFT --}}
            <form method="POST"
                  action="{{ route('checkout.place') }}"
                  class="lg:col-span-2 bg-white p-6 rounded shadow space-y-4">
                @csrf

                {{-- Validation Errors --}}
                @if ($errors->any())
                    <div class="px-4 py-3 rounded relative border border-grey-light border-l-4 bg-primary/10 text-primary" role="alert">
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
                        @foreach(\App\Models\Country::with('translations')->where('is_active', true)->orderByTranslatedName()->get() as $country)
                            <option value="{{ $country->id }}">
                                {{ $country->name }}
                            </option>
                        @endforeach
                    </select>
                    
                    <label class="flex items-center gap-2 pt-2">
                        <input type="checkbox" name="is_default" value="1" class="rounded" :disabled="addressMode !== 'new'">
                        <span class="text-sm">{{ t('checkout.set_as_default') ?: 'Set as default address' }}</span>
                    </label>
                </div>

                {{-- SHIPPING TIER SELECTION --}}
                <div x-show="addressMode === 'new' && availableTiers.length === 0 && !qualifiesForFreeShipping" x-cloak class="px-4 py-3 rounded border border-grey-light border-l-4 bg-primary/10 text-accent-secondary mt-6">
                    <p class="text-sm text-accent-secondary">
                        {{ t('checkout.address_required_for_shipping') ?: 'Please fill the address form so we can show the available shipping options.' }}
                    </p>
                </div>

                <div x-show="availableTiers.length > 0" x-cloak class="mt-6">
                    <h3 class="font-semibold mb-3">{{ t('checkout.select_shipping_method') ?: 'Select Shipping Method' }}</h3>
                    
                    <div x-show="qualifiesForFreeShipping" class="px-3 py-2 rounded border border-grey-light border-l-4 bg-primary/10 text-accent-primary mb-3">
                        <p class="text-sm text-accent-primary">
                            {{ t('checkout.free_shipping_message') ?: 'Your order total exceeds' }} €<span x-text="freeShippingOver.toFixed(2)"></span>. 
                            {{ t('checkout.free_shipping_qualified') ?: 'You qualify for free shipping! You can also choose a faster shipping method below (additional cost applies).' }}
                        </p>
                    </div>
                    
                    <template x-for="tier in availableTiers" :key="tier.id">
                        <label class="block border p-3 rounded cursor-pointer mb-2 hover:bg-white" :class="{ 'border-accent-primary bg-primary/10 text-accent-primary': tier.is_free }">
                            <input type="radio"
                                   name="shipping_tier_id"
                                   :value="tier.id"
                                   @click="selectTier(tier)"
                                   :checked="selectedTierId === tier.id">
                            <span class="ml-2">
                                <span class="font-medium" x-text="tier.name"></span>
                                <span x-show="tier.is_free" class="text-accent-primary font-semibold ml-1">({{ t('checkout.free') ?: 'FREE' }})</span>
                                <span x-show="!tier.is_free">
                                    —
                                    €<span x-text="Number(tier.cost_gross).toFixed(2)"></span>
                                </span>
                                (<span x-text="tier.shipping_days"></span> {{ t('store.working_days') ?: 'working days' }})
                            </span>
                        </label>
                    </template>
                </div>

                <div x-show="!qualifiesForFreeShipping && addressMode === 'existing' && availableTiers.length === 0" x-cloak class="px-4 py-3 rounded border border-grey-light border-l-4 bg-primary/10 text-accent-secondary mt-6">
                    <p class="text-sm text-accent-secondary">
                        {{ t('checkout.no_shipping_available') ?: 'No shipping options available for your address. Please contact us.' }}
                    </p>
                </div>

                <button class="bg-primary text-white px-8 py-3 rounded-full uppercase mt-6">
                    {{ t('checkout.place_order') ?: 'Place Order' }}
                </button>
            </form>

            {{-- RIGHT --}}
            <div class="bg-white p-6 rounded shadow space-y-2">
                <h3 class="font-semibold">{{ t('checkout.summary') ?: 'Summary' }}</h3>

                @foreach ($items as $item)
                    <div class="text-sm flex justify-between">
                        <div>
                            <span>{{ optional($item['product']->translation())->name }} × {{ $item['quantity'] }}</span>
                            @if (!empty($item['selected_option_labels']))
                                <div class="text-xs text-grey-medium mt-0.5">
                                    @foreach ($item['selected_option_labels'] as $label)
                                        <span class="block">{{ $label['type_name'] }}: <strong>{{ $label['option_name'] }}</strong></span>
                                    @endforeach
                                </div>
                            @endif
                        </div>
                        <span>€{{ number_format($item['gross'], 2) }}</span>
                    </div>
                @endforeach

                <hr>

                @if(config('app.tax_enabled', env('APP_TAX_ENABLED', true)))
                    <div class="text-sm text-grey-medium flex justify-between">
                        <span>{{ t('checkout.products_tax') ?: 'Products tax' }}</span>
                        <span>€{{ number_format($productsTax, 2) }}</span>
                    </div>

                    <div class="flex justify-between">
                        <span>{{ t('checkout.shipping') ?: 'Shipping' }}</span>
                        <span x-text="'€' + Number(shipping?.gross ?? 0).toFixed(2)" x-cloak>€{{ number_format($shipping['gross'], 2) }}</span>
                    </div>

                    <div class="text-sm text-grey-medium flex justify-between">
                        <span>{{ t('checkout.shipping_tax') ?: 'Shipping tax' }}</span>
                        <span x-text="'€' + Number(shipping?.tax ?? 0).toFixed(2)" x-cloak>€{{ number_format($shipping['tax'], 2) }}</span>
                    </div>

                    <div class="text-sm text-grey-medium flex justify-between">
                        <span>{{ t('checkout.total_tax') ?: 'Total tax' }}</span>
                        <span x-text="'€' + totalTax" x-cloak>€{{ number_format($totalTax, 2) }}</span>
                    </div>
                @else
                    <div class="text-sm text-grey-medium">
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
