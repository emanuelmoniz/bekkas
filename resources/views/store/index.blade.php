<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-grey-dark">
            {{ t('store.title') ?: 'Store' }}
        </h2>
    </x-slot>

    <script id="favorites-data" type="application/json">
        {!! json_encode($favoriteIds ?? []) !!}
    </script>

    <script>
        function favoritesData() {
            return {
                favorites: JSON.parse(document.getElementById('favorites-data').textContent),
                async toggleFavorite(productId) {
                    try {
                        const response = await fetch(`/favorites/toggle/${productId}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            }
                        });
                        const data = await response.json();
                        if (data.isFavorite) {
                            this.favorites.push(productId);
                        } else {
                            this.favorites = this.favorites.filter(id => id !== productId);
                        }
                        // Update Alpine store
                        if (window.Alpine && window.Alpine.store) {
                            window.Alpine.store('favorites').count = data.favoritesCount;
                        }
                    } catch (error) {
                        console.error('Error toggling favorite:', error);
                    }
                }
            }
        }
    </script>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- FILTERS --}}
            <x-product-filters :categories="$categories" :materials="$materials" reset-route="store.index" />

            {{-- PRODUCTS --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-4 gap-6 animate-sequence" x-data="favoritesData()">
                @forelse ($products as $product)
                    <div class="bg-light rounded shadow p-4 hover:shadow-lg transition relative overflow-hidden isolate anim-item" data-index="{{ $loop->index }}">
                        <button @click.prevent="toggleFavorite({{ $product->id }})" 
                                class="absolute top-2 right-2 p-2 bg-white rounded-full transition z-10">
                            <svg xmlns="http://www.w3.org/2000/svg" 
                                 :fill="favorites.includes({{ $product->id }}) ? 'currentColor' : 'none'" 
                                 viewBox="0 0 24 24" 
                                 stroke="currentColor" 
                                 class="w-5 h-5" 
                                 :class="favorites.includes({{ $product->id }}) ? 'text-status-error' : 'text-grey-medium'">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                            </svg>
                        </button>
                        <a href="{{ route('store.show', $product) }}" class="block">
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
                    </div>
                @empty
                    <p class="text-grey-dark col-span-full text-center">
                        {{ t('store.no_products') ?: 'No products found.' }}
                    </p>
                @endforelse
            </div>

            {{ $products->links() }}
        </div>
    </div>
</x-app-layout>
