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
                                    Includes €{{ number_format($item['line_tax'], 2) }} tax
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

                    <div class="text-sm text-gray-500 flex justify-between">
                        <span>{{ t('cart.summary.product_tax') ?: 'Product tax' }}</span>
                        <span>€{{ number_format($productsTax, 2) }}</span>
                    </div>

                    <div class="flex justify-between">
                        <span>{{ t('cart.summary.shipping') ?: 'Shipping' }}</span>
                        <span>€{{ number_format($shipping['gross'], 2) }}</span>
                    </div>

                    <div class="text-sm text-gray-500 flex justify-between">
                        <span>{{ t('cart.summary.shipping_tax') ?: 'Shipping tax' }}</span>
                        <span>€{{ number_format($shipping['tax'], 2) }}</span>
                    </div>

                    <div class="border-t pt-2 flex justify-between font-semibold">
                        <span>{{ t('cart.summary.total') ?: 'Total' }}</span>
                        <span>€{{ number_format($totalGross, 2) }}</span>
                    </div>

                    <div class="text-sm text-gray-600 flex justify-between">
                        <span>{{ t('cart.summary.total_tax') ?: 'Total tax' }}</span>
                        <span>€{{ number_format($totalTax, 2) }}</span>
                    </div>

                    <div class="pt-4">
                        <a href="{{ route('checkout.index') }}"
                           class="w-full bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded text-center font-medium block">
                            {{ t('cart.checkout') ?: 'Proceed to Checkout' }}
                        </a>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
