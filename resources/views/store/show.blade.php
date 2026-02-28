<x-app-layout>

    @php
        $optionTypesData = $product->optionTypes
            ->where('is_active', true)
            ->map(function ($type) {
                return [
                    'id'         => $type->id,
                    'name'       => optional($type->translation())->name ?? '',
                    'have_stock' => (bool) $type->have_stock,
                    'have_price' => (bool) $type->have_price,
                    'options'    => $type->options
                        ->where('is_active', true)
                        ->map(fn ($opt) => [
                            'id'          => $opt->id,
                            'name'        => optional($opt->translation())->name ?? '',
                            'stock'       => (int) $opt->stock,
                            'price'       => $opt->price !== null ? (float) $opt->price : null,
                            'promo_price' => $opt->promo_price !== null ? (float) $opt->promo_price : null,
                        ])
                        ->values()
                        ->toArray(),
                ];
            })
            ->values()
            ->toArray();
    @endphp

    <script>
        // Alpine component for the product page: handles favorites, options and cart additions
        function productPage(addUrl, optionTypes, basePrice, basePromoPrice, isPromo) {
            return {
                // option types data (array of {id, name, have_stock, have_price, options[]})
                optionTypes: optionTypes,
                // expose isPromo so Alpine template expressions (x-show, x-text) can reference it
                isPromo: isPromo,
                // selected options: { [typeId]: optionId }
                selectedOptions: {},

                // original (non-discounted) price — for strikethrough display when is_promo
                get originalPrice() {
                    if (!isPromo) return null;
                    for (const type of this.optionTypes) {
                        if (type.have_price) {
                            const optId = this.selectedOptions[type.id];
                            if (!optId) {
                                // No option selected: show lowest base price for strikethrough
                                const prices = type.options
                                    .map(o => o.price)
                                    .filter(p => p !== null);
                                return prices.length ? Math.min(...prices) : null;
                            }
                            const opt = type.options.find(o => o.id === optId);
                            if (opt && opt.price !== null) return opt.price;
                        }
                    }
                    return basePrice;
                },

                // resolved price based on selected options
                get resolvedPrice() {
                    for (const type of this.optionTypes) {
                        if (type.have_price) {
                            const optId = this.selectedOptions[type.id];
                            if (!optId) {
                                // No option selected yet: show lowest available price
                                const prices = type.options
                                    .map(o => isPromo && o.promo_price !== null ? o.promo_price : (o.price !== null ? o.price : null))
                                    .filter(p => p !== null);
                                return prices.length ? Math.min(...prices) : null;
                            }
                            const opt = type.options.find(o => o.id === optId);
                            if (opt) {
                                if (isPromo && opt.promo_price !== null) return opt.promo_price;
                                if (opt.price !== null) return opt.price;
                            }
                        }
                    }
                    // No have_price type: use product-level price
                    if (isPromo && basePromoPrice !== null) return basePromoPrice;
                    return basePrice;
                },

                // resolved stock (null means use product-level stock)
                get resolvedStock() {
                    for (const type of this.optionTypes) {
                        if (type.have_stock) {
                            const optId = this.selectedOptions[type.id];
                            if (!optId) return null;
                            const opt = type.options.find(o => o.id === optId);
                            return opt ? opt.stock : null;
                        }
                    }
                    return null; // use product-level
                },

                // effective max for the quantity input
                get effectiveMaxStock() {
                    const s = this.resolvedStock;
                    return s !== null ? s : {{ $product->stock }};
                },

                hasStockControlType() {
                    return this.optionTypes.some(t => t.have_stock);
                },

                // cart
                quantity: 1,
                adding: false,
                async addToCart(event) {
                    event.preventDefault();
                    if (this.adding) return;
                    this.adding = true;
                    try {
                        // Strip blank/zero selections so the server sees only real option ids
                        const filteredOptions = {};
                        for (const [typeId, optionId] of Object.entries(this.selectedOptions)) {
                            if (optionId) filteredOptions[typeId] = optionId;
                        }
                        const body = {
                            quantity: this.quantity,
                            options: filteredOptions,
                        };
                        const response = await fetch(addUrl, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json'
                            },
                            body: JSON.stringify(body)
                        });

                        if (response.status === 404) {
                            throw new Error('Product unavailable');
                        }

                        const contentType = response.headers.get('content-type') || '';
                        if (!contentType.includes('application/json')) {
                            throw new Error('{{ t("store.add_to_cart_failed") ?: 'Unable to add to cart' }}');
                        }

                        const data = await response.json();

                        if (!data.success) {
                            throw new Error(data.message || '{{ t("store.add_to_cart_failed") ?: 'Unable to add to cart' }}');
                        }

                        if (window.Alpine && window.Alpine.store && window.Alpine.store('flash')) {
                            window.Alpine.store('flash').showMessage('{{ t("store.added_to_cart") ?: 'Added to cart' }}');
                        }
                        if (window.Alpine && window.Alpine.store && window.Alpine.store('cart')) {
                            window.Alpine.store('cart').count = data.cartCount;
                        }
                    } catch (error) {
                        console.error('Error adding to cart:', error);
                        if (window.Alpine && window.Alpine.store && window.Alpine.store('flash')) {
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

    <div class="py-4">

        {{-- BACK LINK --}}
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 pb-4 flex">
            <a href="{{ $backUrl }}" class="text-sm text-accent-primary hover:underline flex items-center gap-1">
                <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7" />
                </svg>
                {{ t('store.back_to_store') ?: 'Back to store' }}
            </a>
        </div>

        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 grid grid-cols-1 lg:grid-cols-2 gap-6 animate-sequence">

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
            <div class="bg-white p-6 rounded shadow space-y-4 anim-item" x-data="productPage(
                '{{ route('cart.add', $product) }}',
                {{ json_encode($optionTypesData) }},
                {{ (float) $product->price }},
                {{ $product->promo_price !== null ? (float) $product->promo_price : 'null' }},
                {{ $product->is_promo ? 'true' : 'false' }}
            )">

                {{-- NAME (moved from header) --}}
                <h2 class="font-semibold text-xl text-grey-dark">
                    {{ optional($product->translation())->name }}
                </h2>

                {{-- PRICE & FAVORITE --}}
                <div class="flex items-center justify-between">
                    <div class="text-xl font-semibold flex items-baseline">
                        {{-- Reactive price: uses resolvedPrice when options are selected, falls back to PHP render --}}
                        <span x-text="resolvedPrice !== null ? '€' + resolvedPrice.toFixed(2) : '{{ number_format($product->is_promo ? ($product->promo_price ?? $product->price) : $product->price, 2) }}'">
                            €{{ number_format($product->is_promo ? ($product->promo_price ?? $product->price) : $product->price, 2) }}
                        </span>
                        <span x-show="isPromo && originalPrice !== null && originalPrice !== resolvedPrice"
                              x-text="'€' + (originalPrice !== null ? originalPrice.toFixed(2) : '')"
                              class="text-sm text-grey-medium line-through ml-2"
                              @if(!($product->is_promo)) style="display:none" @endif>
                            @if($product->is_promo && $product->price)
                                €{{ number_format($product->price, 2) }}
                            @endif
                        </span>
                    </div>
                    <x-favorite-button
                        :product-id="$product->id"
                        :is-favorite="$isFavorite ?? false"
                        class="hover:bg-grey-light"
                        icon-size="lg"
                    />
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
                {{-- If an option type controls stock, show reactive option-level stock; otherwise product-level --}}
                <template x-if="!hasStockControlType()">
                    @if ($product->stock > 0)
                        <div class="text-sm">
                            {{ t('store.stock') ?: 'Stock' }}:
                            <span class="text-accent-secondary font-medium">{{ t('store.available') ?: 'Available' }}</span>
                        </div>
                    @elseif ($product->is_backorder)
                        <div class="rounded border border-grey-light border-l-4 bg-accent-secondary/10 p-3">
                            <div class="text-sm font-medium text-accent-secondary mb-1">
                                {{ t('store.backorder_title') ?: 'Made to order' }}
                            </div>
                            <div class="text-sm text-accent-secondary">
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
                </template>

                {{-- Option-level stock indicator (only shown when a stock-controlling type is present AND option selected) --}}
                <template x-if="hasStockControlType() && resolvedStock !== null">
                    <div class="text-sm">
                        {{ t('store.stock') ?: 'Stock' }}:
                        <span x-show="resolvedStock > 0 || {{ $product->is_backorder ? 'true' : 'false' }}"
                              class="text-accent-primary font-medium">{{ t('store.available') ?: 'Available' }}</span>
                        <span x-show="resolvedStock <= 0 && !{{ $product->is_backorder ? 'true' : 'false' }}"
                              class="text-primary font-medium">{{ t('store.out_of_stock') ?: 'Out of stock' }}</span>
                    </div>
                </template>

                {{-- Option type selectors --}}
                @if ($product->optionTypes->where('is_active', true)->isNotEmpty())
                    <template x-for="type in optionTypes" :key="type.id">
                        <div>
                            <label class="block font-medium text-sm text-grey-dark mb-1" x-text="type.name"></label>
                            <select :name="'options[' + type.id + ']'"
                                    x-model.number="selectedOptions[type.id]"
                                    class="w-full border rounded px-3 py-2 text-sm">
                                <option value="">— {{ t('store.select_option') ?: 'Select an option' }} —</option>
                                <template x-for="opt in type.options" :key="opt.id">
                                    <option :value="opt.id"
                                            :disabled="type.have_stock && opt.stock <= 0 && !{{ $product->is_backorder ? 'true' : 'false' }}"
                                            x-text="opt.name + (type.have_price && opt.price !== null ? ' — €' + (isPromo && opt.promo_price !== null ? opt.promo_price : opt.price).toFixed(2) : '') + (type.have_stock && opt.stock <= 0 && !{{ $product->is_backorder ? 'true' : 'false' }} ? ' ({{ t('store.out_of_stock') ?: 'Out of stock' }})' : '')">
                                    </option>
                                </template>
                            </select>
                        </div>
                    </template>
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
                            :max="{{ $product->is_backorder ? 'undefined' : 'effectiveMaxStock' }}"
                            class="w-20 border rounded px-2 py-1">
                        <button :disabled="adding"
                                class="bg-primary text-white px-8 py-3 rounded-full uppercase"
                                x-text="adding ? '{{ t('store.adding') ?: 'Adding...' }}' : '{{ t('store.add_to_cart') ?: 'Add to cart' }}'">
                        </button>
                    </form>
                @endif

            </div>
        </div>
    </div>

    {{-- RELATED PRODUCTS --}}
    <div class="animate-sequence bg-secondary">
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
