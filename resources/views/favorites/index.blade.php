<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            {{ t('favorites.title') ?: 'My Favorites' }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            @if($hasFavorites)
                {{-- FILTERS --}}
                <x-product-filters :categories="$categories" :materials="$materials" reset-route="favorites.index" />

                @if($products->total() > 0)
                    {{-- PRODUCTS --}}
                    <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-4 gap-6">
                        @foreach ($products as $product)
                            <div class="bg-white rounded shadow p-4 hover:shadow-lg transition relative isolate">
                                <a href="{{ route('products.show', $product) }}">
                                    <img src="{{ asset('storage/' . optional($product->primaryPhoto)->path) }}"
                                         class="h-40 w-full object-cover rounded mb-3">
                                    <div class="font-semibold">
                                        {{ optional($product->translation())->name }}
                                    </div>
                                    <div class="text-sm text-gray-600">
                                        €{{ number_format($product->is_promo ? ($product->promo_price ?? $product->price) : $product->price, 2) }}
                                    </div>
                                    @if(isset($deliveryDates[$product->id]) && $deliveryDates[$product->id])
                                        <div class="text-xs text-gray-500 mt-1">
                                            {{ t('products.expected_delivery') ?: 'Expected delivery' }}: {{ $deliveryDates[$product->id] }}
                                        </div>
                                    @endif
                                </a>
                                
                                <form method="POST" action="{{ route('favorites.remove', $product) }}" class="mt-3">
                                    @csrf
                                    <button type="submit" class="w-full bg-red-100 hover:bg-red-200 text-red-700 px-4 py-2 rounded text-sm">
                                        {{ t('favorites.remove') ?: 'Remove from Favorites' }}
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>

                    {{ $products->links() }}
                @else
                    <div class="bg-white p-6 rounded shadow text-center text-gray-600">
                        {{ t('favorites.no_results') ?: 'No favorites match your filter criteria.' }}
                    </div>
                @endif
            @else
                <div class="bg-white p-6 rounded shadow text-center text-gray-600">
                    {{ t('favorites.empty') ?: 'You have no favorite products yet.' }}
                    
                    <div class="mt-4">
                        <a href="{{ route('products.index') }}"
                           class="inline-block bg-indigo-600 hover:bg-indigo-700 text-white px-6 py-3 rounded text-center font-medium">
                            {{ t('favorites.browse_products') ?: 'Browse Products' }}
                        </a>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
