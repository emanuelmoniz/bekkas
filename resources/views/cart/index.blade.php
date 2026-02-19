<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            {{ t('page.cart.title') ?: 'Cart' }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (empty($items))
                <div class="bg-white p-6 rounded shadow text-center text-gray-600">
                    {{ t('cart.empty') ?: 'Your cart is empty.' }}
                    
                    @if(config('app.store_enabled'))
                        <div class="mt-4">
                            <a href="{{ route('store.index') }}"
                               class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded text-center font-medium">
                                {{ t('cart.start_shopping') ?: 'Start Shopping' }}
                            </a>
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
                                <div class="text-sm text-gray-600">
                                    €{{ number_format($item['unit_gross'], 2) }} × {{ $item['quantity'] }}
                                </div>
                                <div class="text-xs text-gray-500">
                                    @if(config('app.tax_enabled', env('APP_TAX_ENABLED', true)))
                                        Includes €{{ number_format($item['line_tax'], 2) }} tax
                                    @else
                                        {{ t('tax.included_in_price') ?: 'All taxes are included in the price' }}
                                    @endif
                                </div>
                            </div>

                            <div class="flex items-center gap-2">
                                <form method="POST" action="{{ route('cart.update', $item['product']) }}">
                                    @csrf
                                    <input type="number"
                                           name="quantity"
                                           value="{{ $item['quantity'] }}"
                                           min="1"
                                           class="w-16 border rounded px-2 py-1">
                                    <button class="text-sm underline ml-2">
                                        {{ t('cart.update') ?: 'Update' }}
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('cart.remove', $item['product']) }}">
                                    @csrf
                                    <button class="text-red-600 text-sm underline">
                                        {{ t('cart.remove') ?: 'Remove' }}
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="bg-white p-6 rounded shadow space-y-2">
                    <div class="flex justify-between">
                        <span>{{ t('cart.summary.products') ?: 'Products' }}</span>
                        <span>€{{ number_format($productsGross, 2) }}</span>
                    </div>

                    @if(config('app.tax_enabled', env('APP_TAX_ENABLED', true)))
                        <div class="text-sm text-gray-500 flex justify-between">
                            <span>{{ t('cart.summary.product_tax') ?: 'Product tax' }}</span>
                            <span>€{{ number_format($productsTax, 2) }}</span>
                        </div>
                    @else
                        <div class="text-sm text-gray-500">
                            {{ t('tax.included_in_price') ?: 'All taxes are included in the price' }}
                        </div>
                    @endif

                    <div class="border-t pt-2 flex justify-between font-semibold">
                        <span>{{ t('cart.summary.subtotal') ?: 'Subtotal' }}</span>
                        <span>€{{ number_format($productsGross, 2) }}</span>
                    </div>

                    <div class="bg-blue-50 border border-blue-200 rounded p-3 text-sm text-blue-800">
                        {{ t('cart.shipping_at_checkout') ?: 'The shipping cost will be calculated at checkout.' }}
                    </div>

                    <div class="pt-4">
                        <a href="{{ route('checkout.index') }}"
                           class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded text-center font-medium block">
                            {{ t('cart.checkout') ?: 'Proceed to Checkout' }}
                        </a>
                    </div>

                    @if(config('app.store_enabled'))
                        <div class="pt-2">
                            <a href="{{ route('store.index') }}"
                               class="w-full bg-gray-100 hover:bg-gray-200 text-gray-700 px-6 py-3 rounded text-center font-medium block">
                                {{ t('cart.continue_shopping') ?: 'Continue Shopping' }}
                            </a>
                        </div>
                    @endif
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
