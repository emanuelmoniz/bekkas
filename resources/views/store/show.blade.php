<x-app-layout>
    
    <script>
        // Alpine component for the product page: handles favorites and cart additions
        // `addUrl` parameter allows the server to generate the correct path (prefix, locale, etc.)
        function productPage(productId, initialFavorite, addUrl) {
            return {
                // favorite toggle behaviour moved from previous helper
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
                },

                // cart behaviour
                quantity: 1,
                adding: false,
                async addToCart(event) {
                    event.preventDefault();
                    if (this.adding) return;
                    this.adding = true;
                    try {
                        const response = await fetch(addUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify({ quantity: this.quantity })
                        });

                        // if the server returned a 404 we convert to a friendly message
                        if (response.status === 404) {
                            throw new Error('Product unavailable');
                        }

                        if (! response.ok) throw new Error('Network response was not ok');
                        const data = await response.json();

                        // flash message
                        if (window.Alpine && window.Alpine.store && window.Alpine.store('flash')) {
                            window.Alpine.store('flash').showMessage('{{ t("store.added_to_cart") ?: 'Added to cart' }}');
                        }

                        // update cart count store if available
                        if (window.Alpine && window.Alpine.store && window.Alpine.store('cart')) {
                            window.Alpine.store('cart').count = data.cartCount;
                        }
                    } catch (error) {
                        console.error('Error adding to cart:', error);
                        if (window.Alpine && window.Alpine.store && window.Alpine.store('flash')) {
                            // display the error message if we have it, otherwise a generic failure
                            const msg = error.message || '{{ t("store.add_to_cart_failed") ?: 'Unable to add to cart' }}';
                            window.Alpine.store('flash').showMessage(msg, 'error');
                        }
                    } finally {
                        this.adding = false;
                    }
                }
            };
        }
    </script>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-2 gap-6 animate-sequence">

            {{-- GALLERY --}}
            @php
                $galleryImages = $product->photos
                    ->sortByDesc('is_primary')
                    ->map(fn ($photo) => [
                        'url'      => asset('storage/' . $photo->path),
                        'original' => $photo->original_path
                            ? asset('storage/' . $photo->original_path)
                            : null,
                    ])
                    ->values()
                    ->toArray();
            @endphp

            <div class="anim-item">
                <x-image-gallery :images="$galleryImages"/>
            </div>

            {{-- DETAILS --}}
            <div class="bg-light p-6 rounded shadow space-y-4 anim-item" x-data="productPage({{ $product->id }}, {{ json_encode($isFavorite ?? false) }}, '{{ route('cart.add', $product) }}')">

                {{-- BACK LINK --}}
                <div>
                    <a href="{{ $backUrl }}" class="text-sm text-accent-primary hover:underline flex items-center gap-1">
                        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                        </svg>
                        {{ t('store.back_to_store') ?: 'Back to store' }}
                    </a>
                </div>

                {{-- NAME (moved from header) --}}
                <h2 class="font-semibold text-xl text-grey-dark">
                    {{ optional($product->translation())->name }}
                </h2>

                {{-- PRICE & FAVORITE --}}
                <div class="flex items-center justify-between">
                    <div class="text-xl font-semibold flex items-baseline">
                        €{{ number_format($product->is_promo ? ($product->promo_price ?? $product->price) : $product->price, 2) }}
                        @if ($product->is_promo && $product->promo_price)
                            <span class="text-sm text-grey-medium line-through ml-2">
                                €{{ number_format($product->price, 2) }}
                            </span>
                        @endif
                    </div>
                    <button @click="toggle" class="p-2 hover:bg-grey-light rounded-full transition">
                        <svg xmlns="http://www.w3.org/2000/svg" :fill="isFavorite ? 'currentColor' : 'none'" viewBox="0 0 24 24" stroke="currentColor" class="w-6 h-6" :class="isFavorite ? 'text-status-error' : 'text-grey-medium'">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
                        </svg>
                    </button>
                </div>

                {{-- DESCRIPTION --}}
                @if (optional($product->translation())->description)
                    <h4 class="font-medium text-sm text-grey-dark mb-1">
                        {{ t('store.description') ?: 'Description' }}:
                    </h4>
                    <div class="text-grey-dark">
                        {!! nl2br(e(optional($product->translation())->description)) !!}
                    </div>
                @endif

                {{-- TECHNICAL INFO --}}
                @if (optional($product->translation())->technical_info)
                    <h4 class="font-medium text-sm text-grey-dark mb-1">
                        {{ t('store.technical_info') ?: 'Technical info' }}:
                    </h4>
                    <div class="text-grey-dark">
                        {!! nl2br(e(optional($product->translation())->technical_info)) !!}
                    </div>
                @endif

                {{-- CATEGORIES --}}
                @if ($product->categories->isNotEmpty())
                    <div>
                        <h4 class="font-medium text-sm text-grey-dark mb-1">
                            {{ t('store.categories') ?: 'Categories' }}
                        </h4>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($product->categories as $category)
                                <span class="bg-grey-light text-grey-dark text-xs px-2 py-1 rounded">
                                    {{ optional($category->translation())->name }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- MATERIALS --}}
                @if ($product->materials->isNotEmpty())
                    <div>
                        <h4 class="font-medium text-sm text-grey-dark mb-1">
                            {{ t('store.materials') ?: 'Materials' }}
                        </h4>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($product->materials as $material)
                                <span class="bg-grey-light text-grey-dark text-xs px-2 py-1 rounded">
                                    {{ optional($material->translation())->name }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- WEIGHT --}}
                <div class="text-sm text-grey-dark">
                    {{ t('store.weight') ?: 'Weight' }}: {{ (int) $product->weight }} g
                </div>

                {{-- DIMENSIONS --}}
                @if($product->width || $product->length || $product->height)
                    <div class="text-sm text-grey-dark">
                        {{ t('store.dimensions') ?: 'Dimensions' }}: 
                        {{ isset($product->width) ? (int) $product->width : '-' }} × {{ isset($product->length) ? (int) $product->length : '-' }} × {{ isset($product->height) ? (int) $product->height : '-' }} mm
                    </div>
                @endif

                {{-- EXPECTED DELIVERY --}}
                @if(isset($deliveryDate) && $deliveryDate)
                    <div class="rounded border border-grey-light border-l-4 bg-accent-primary/10 p-3">
                        <div class="text-sm font-medium text-accent-primary">
                            {{ t('store.expected_delivery') ?: 'Expected delivery' }}
                        </div>
                        <div class="text-lg font-semibold text-accent-primary">
                            {{ $deliveryDate }}
                        </div>
                        <div class="text-xs text-accent-primary mt-1">
                            {{ t('store.delivery_working_days') ?: 'Calculated in working days (Mon-Fri)' }}
                        </div>
                    </div>
                @endif

                {{-- STOCK / BACKORDER --}}
                @if ($product->stock > 0)
                    <div class="text-sm">
                        {{ t('store.stock') ?: 'Stock' }}:
                        <span class="text-accent-primary font-medium">{{ t('store.available') ?: 'Available' }}</span>
                    </div>
                @elseif ($product->is_backorder)
                    <div class="rounded border border-grey-light border-l-4 bg-accent-secondary/10 p-3">
                        <div class="text-sm font-medium text-accent-primary mb-1">
                            {{ t('store.backorder_title') ?: 'Made to order' }}
                        </div>
                        <div class="text-sm text-accent-primary">
                            {{ t('store.backorder_message') ?: 'This item does not have stock, but can be printed per request. The production time is' }}
                            <span class="font-medium">{{ $product->production_time }} {{ t('store.working_days') ?: 'working days' }}</span>.
                            {{ t('store.backorder_delivery_note') ?: 'The shown delivery date estimation already includes this production time.' }}
                        </div>
                    </div>
                @else
                    <div class="text-sm">
                        {{ t('store.stock') ?: 'Stock' }}:
                        <span class="text-primary font-medium">{{ t('store.out_of_stock') ?: 'Out of stock' }}</span>
                    </div>
                @endif

                @if ($product->stock > 0 || $product->is_backorder)
                    <form method="POST"
                        action="{{ route('cart.add', $product) }}"
                        @submit.prevent="addToCart"
                        class="pt-4 flex gap-2">
                        @csrf
                        <input type="number"
                            name="quantity"
                            x-model.number="quantity"
                            value="1"
                            min="1"
                            @if(!$product->is_backorder) max="{{ $product->stock }}" @endif
                            class="w-20 border rounded px-2 py-1">
                        <button :disabled="adding"
                                class="bg-accent-primary text-light px-4 py-2 rounded"
                                x-text="adding ? '{{ t('store.adding') ?: 'Adding...' }}' : '{{ t('store.add_to_cart') ?: 'Add to cart' }}'">
                        </button>
                    </form>
                @endif

            </div>
        </div>
    </div>

    {{-- RELATED PRODUCTS --}}
    <div class="animate-sequence">
        <x-related-products 
            class="anim-item"
            :categories="$product->categories"
            :exclude-id="$product->id"
            :title="t('store.related_products') ?: 'Related Products'"
            order="random"
            :max="8"
        />
    </div>
</x-app-layout>
