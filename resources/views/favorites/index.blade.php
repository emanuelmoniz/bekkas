<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-grey-dark">
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
                                <a href="{{ route('store.show', $product) }}">
                                    <img src="{{ asset('storage/' . optional($product->primaryPhoto)->path) }}"
                                         class="h-40 w-full object-cover rounded mb-3">
                                    <div class="font-semibold">
                                        {{ optional($product->translation())->name }}
                                    </div>
                                    <div class="text-sm text-grey-dark">
                                        €{{ number_format($product->is_promo ? ($product->promo_price ?? $product->price) : $product->price, 2) }}
                                    </div>
                                    @if(isset($deliveryDates[$product->id]) && $deliveryDates[$product->id])
                                        <div class="text-xs text-grey-medium mt-1">
                                            {{ t('store.expected_delivery') ?: 'Expected delivery' }}: {{ $deliveryDates[$product->id] }}
                                        </div>
                                    @endif
                                </a>
                                
                                <form method="POST" action="{{ route('favorites.remove', $product) }}" class="mt-3">
                                    @csrf
                                    <button type="submit" class="w-full bg-status-error/10 hover:bg-status-error/20 text-status-error px-8 py-3 rounded-full uppercase text-sm">
                                        {{ t('favorites.remove') ?: 'Remove from Favorites' }}
                                    </button>
                                </form>
                            </div>
                        @endforeach
                    </div>

                    {{ $products->links() }}
                @else
                    <div class="bg-white p-6 rounded shadow text-center text-grey-dark">
                        {{ t('favorites.no_results') ?: 'No favorites match your filter criteria.' }}
                    </div>
                @endif
            @else
                <div class="bg-white p-6 rounded shadow text-center text-grey-dark">
                    {{ t('favorites.empty') ?: 'You have no favorite products yet.' }}
                    
                    @if(config('app.store_enabled'))
                        <div class="mt-4">
                            <a href="{{ route('store.index') }}"
                               class="inline-block bg-primary hover:bg-primary/90 text-white px-8 py-3 rounded-full uppercase text-center font-medium">
                                {{ t('favorites.browse_products') ?: 'Browse Products' }}
                            </a>
                        </div>
                    @endif
                </div>
            @endif

        </div>
    </div>
</x-app-layout>
