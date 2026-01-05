<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            Cart
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if (empty($items))
                <div class="bg-white p-6 rounded shadow text-center text-gray-600">
                    Your cart is empty.
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
                                        Update
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('cart.remove', $item['product']) }}">
                                    @csrf
                                    <button class="text-red-600 text-sm underline">
                                        Remove
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>

                <div class="bg-white p-6 rounded shadow space-y-2">
                    <div class="flex justify-between">
                        <span>Products</span>
                        <span>€{{ number_format($productsGross, 2) }}</span>
                    </div>

                    <div class="text-sm text-gray-500 flex justify-between">
                        <span>Product tax</span>
                        <span>€{{ number_format($productsTax, 2) }}</span>
                    </div>

                    <div class="flex justify-between">
                        <span>Shipping</span>
                        <span>€{{ number_format($shipping['gross'], 2) }}</span>
                    </div>

                    <div class="text-sm text-gray-500 flex justify-between">
                        <span>Shipping tax</span>
                        <span>€{{ number_format($shipping['tax'], 2) }}</span>
                    </div>

                    <div class="border-t pt-2 flex justify-between font-semibold">
                        <span>Total</span>
                        <span>€{{ number_format($totalGross, 2) }}</span>
                    </div>

                    <div class="text-sm text-gray-600 flex justify-between">
                        <span>Total tax</span>
                        <span>€{{ number_format($totalTax, 2) }}</span>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
