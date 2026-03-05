<x-app-layout>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-6">

            @if (empty($items))
                <div class="bg-white p-6 rounded shadow text-center text-grey-dark">
                    {{ t('cart.empty') ?: 'Your cart is empty.' }}
                    
                    @if(config('app.store_enabled'))
                        <div class="mt-4">
                            <x-primary-cta as="a" :href="route('store.index')">
                                {{ t('cart.start_shopping') ?: 'Start Shopping' }}
                            </x-primary-cta>
                        </div>
                    @endif
                </div>
            @else
                <div class="bg-white rounded shadow divide-y">
                    @foreach ($items as $item)
                        <div class="p-4 flex justify-between items-center">
                            <div>
                                <div class="font-medium">
                                    {{ optional($item['product']->translation())->name }}
                                </div>

                                {{-- Selected options --}}
                                @if (!empty($item['selected_option_labels']))
                                    <div class="text-xs text-grey-dark mt-0.5 space-x-2">
                                        @foreach ($item['selected_option_labels'] as $label)
                                            <span>{{ $label['type_name'] }}: <strong>{{ $label['option_name'] }}</strong></span>
                                        @endforeach
                                    </div>
                                @endif

                                <div class="text-sm text-grey-dark">
                                    €{{ number_format($item['unit_gross'], 2) }} × {{ $item['quantity'] }}
                                </div>
                                <div class="text-xs text-grey-medium">
                                    @if(config('app.tax_enabled', env('APP_TAX_ENABLED', true)))
                                        {{ t('cart.item_tax_included', ['amount' => '€'.number_format($item['line_tax'], 2)]) }}
                                    @else
                                        {{ t('tax.included_in_price') ?: 'All taxes are included in the price' }}
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                <form method="POST" action="{{ route('cart.update', $item['product']) }}">
                                    @csrf
                                    <input type="hidden" name="old_cart_key" value="{{ $item['cart_key'] }}">
                                    {{-- Preserve selected options through the update form --}}
                                    @foreach ($item['options'] as $typeId => $optionId)
                                        <input type="hidden" name="options[{{ $typeId }}]" value="{{ $optionId }}">
                                    @endforeach
                                    <input type="number"
                                           name="quantity"
                                           value="{{ $item['quantity'] }}"
                                           min="1"
                                           class="w-16 border rounded px-2 py-1">
                                    <button class="text-sm underline ml-2">
                                        {{ t('cart.update') ?: 'Update' }}
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('cart.remove') }}">
                                    @csrf
                                    <input type="hidden" name="cart_key" value="{{ $item['cart_key'] }}">
                                    <button class="text-grey-dark text-sm underline">
                                        {{ t('cart.remove') ?: 'Remove' }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="bg-white p-6 rounded shadow space-y-2">
                    @if(config('app.tax_enabled', env('APP_TAX_ENABLED', true)))
                        <div class="flex justify-between">
                            <span>{{ t('cart.summary.products') ?: 'Products' }}</span>
                            <span>€{{ number_format($productsGross - $productsTax, 2) }}</span>
                        </div>

                        <div class="border-b mb-2 text-sm text-grey-medium flex justify-between">
                            <span>{{ t('cart.summary.product_tax') ?: 'Product tax' }}</span>
                            <span>€{{ number_format($productsTax, 2) }}</span>
                        </div>
                    @endif

                    <div class="flex justify-between font-semibold">
                        <span>{{ t('cart.summary.subtotal') ?: 'Subtotal' }}</span>
                        <span>€{{ number_format($productsGross, 2) }}</span>
                    </div>

                    <div class="bg-status-info/10 border border-status-info rounded p-3 text-sm text-status-info">
                        {{ t('cart.shipping_at_checkout') ?: 'The shipping cost will be calculated at checkout.' }}
                    </div>

                    <x-primary-cta as="a" :href="route('checkout.index')" :full-width="true">
                        {{ t('cart.checkout') ?: 'Proceed to Checkout' }}
                    </x-primary-cta>

                    @if(config('app.store_enabled'))
                        <x-optional-cta as="a" :href="route('store.index')" :full-width="true" class="mt-2">
                            {{ t('cart.continue_shopping') ?: 'Continue Shopping' }}
                        </x-optional-cta>
                    @endif
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
