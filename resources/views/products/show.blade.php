<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            {{ optional($product->translation())->name }}
        </h2>
    </x-slot>

    <script>
        function favoriteToggle(productId, initialFavorite) {
            return {
                isFavorite: initialFavorite,
                async toggle() {
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
                        this.isFavorite = data.isFavorite;
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
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- GALLERY --}}
            <div class="space-y-4">
                @forelse ($product->photos as $photo)
                    <img src="{{ asset('storage/' . $photo->path) }}"
                         class="w-full rounded shadow">
                @empty
                    <div class="bg-gray-200 h-64 flex items-center justify-center rounded">
                        <span class="text-gray-500">{{ t('products.no_photos') ?: 'No photos available' }}</span>
                    </div>
                @endforelse
            </div>

            {{-- DETAILS --}}
            <div class="bg-white p-6 rounded shadow space-y-4" x-data="favoriteToggle({{ $product->id }}, {{ json_encode($isFavorite ?? false) }})">

                {{-- PRICE & FAVORITE --}}
                <div class="flex items-center justify-between">
                    <div class="text-xl font-semibold">
                        €{{ number_format($product->is_promo ? ($product->promo_price ?? $product->price) : $product->price, 2) }}
                    </div>
                    <button @click="toggle" class="p-2 hover:bg-gray-100 rounded-full transition">
                        <svg xmlns="http://www.w3.org/2000/svg" :fill="isFavorite ? 'currentColor' : 'none'" viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6" :class="isFavorite ? 'text-red-500' : 'text-gray-400'">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                    </button>
                </div>

                {{-- DESCRIPTION --}}
                @if (optional($product->translation())->description)
                    <div class="text-gray-700">
                        {!! nl2br(e(optional($product->translation())->description)) !!}
                    </div>
                @endif

                {{-- WEIGHT --}}
                <div class="text-sm text-gray-600">
                    {{ t('products.weight') ?: 'Weight' }}: {{ $product->weight }} g
                </div>

                {{-- EXPECTED DELIVERY --}}
                @if(isset($deliveryDate) && $deliveryDate)
                    <div class="bg-blue-50 border border-blue-200 rounded p-3">
                        <div class="text-sm font-medium text-blue-900">
                            {{ t('products.expected_delivery') ?: 'Expected delivery' }}
                        </div>
                        <div class="text-lg font-semibold text-blue-700">
                            {{ $deliveryDate }}
                        </div>
                        <div class="text-xs text-blue-600 mt-1">
                            {{ t('products.delivery_working_days') ?: 'Calculated in working days (Mon-Fri)' }}
                        </div>
                    </div>
                @endif

                {{-- STOCK / BACKORDER --}}
                @if ($product->stock > 0)
                    <div class="text-sm">
                        {{ t('products.stock') ?: 'Stock' }}:
                        <span class="text-green-600 font-medium">{{ t('products.available') ?: 'Available' }}</span>
                    </div>
                @elseif ($product->is_backorder)
                    <div class="bg-amber-50 border border-amber-200 rounded p-3">
                        <div class="text-sm font-medium text-amber-900 mb-1">
                            {{ t('products.backorder_title') ?: 'Made to order' }}
                        </div>
                        <div class="text-sm text-amber-800">
                            {{ t('products.backorder_message') ?: 'This item does not have stock, but can be printed per request. The production time is' }}
                            <span class="font-medium">{{ $product->production_time }} {{ t('products.working_days') ?: 'working days' }}</span>.
                            {{ t('products.backorder_delivery_note') ?: 'The shown delivery date estimation already includes this production time.' }}
                        </div>
                    </div>
                @else
                    <div class="text-sm">
                        {{ t('products.stock') ?: 'Stock' }}:
                        <span class="text-red-600 font-medium">{{ t('products.out_of_stock') ?: 'Out of stock' }}</span>
                    </div>
                @endif

                {{-- CATEGORIES --}}
                @if ($product->categories->isNotEmpty())
                    <div>
                        <h4 class="font-medium text-sm text-gray-700 mb-1">
                            {{ t('products.categories') ?: 'Categories' }}
                        </h4>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($product->categories as $category)
                                <span class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded">
                                    {{ optional($category->translation())->name }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- MATERIALS --}}
                @if ($product->materials->isNotEmpty())
                    <div>
                        <h4 class="font-medium text-sm text-gray-700 mb-1">
                            {{ t('products.materials') ?: 'Materials' }}
                        </h4>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($product->materials as $material)
                                <span class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded">
                                    {{ optional($material->translation())->name }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif

@if ($product->stock > 0 || $product->is_backorder)
    <form method="POST"
          action="{{ route('cart.add', $product) }}"
          class="pt-4 flex gap-2">
        @csrf
        <input type="number"
               name="quantity"
               value="1"
               min="1"
               @if(!$product->is_backorder) max="{{ $product->stock }}" @endif
               class="w-20 border rounded px-2 py-1">
        <button class="bg-indigo-600 text-white px-4 py-2 rounded">
            {{ t('products.add_to_cart') ?: 'Add to cart' }}
        </button>
    </form>
@endif

            </div>
        </div>
    </div>
</x-app-layout>
