@props([
    'product',
    'isFavorite'     => false,
    'deliveryDate'   => null,
    'index'          => null,
    'scrollerConfig' => [],
])

{{--
    Reusable product card component.

    **Required props:**
      - `product`        – App\Models\Product instance (with `photos`
                           relation already loaded).

    **Optional props:**
      - `isFavorite`     – boolean; whether the current user has this product
                           favourited. Defaults to `false`.
      - `deliveryDate`   – formatted delivery date string (e.g. "Mar 2026").
                           When null/empty the delivery row is hidden.
      - `index`          – integer position in the parent grid (0-based).
                           When provided the `anim-item` class and
                           `data-index` attribute are added for staggered
                           entrance animations.
      - `scrollerConfig` – array of image-scroller config keys to merge on
                           top of the card defaults.  Accepts any key that
                           `<x-image-scroller>` understands:
                             `interval`, `autoplay`, `autoplay_desktop`,
                             `autoplay_mobile`.
                           Card defaults: interval=1500, autoplay_mobile=true,
                           autoplay_desktop=false.

    **Example:**
      ```blade
      <x-product-card
          :product="$product"
          :is-favorite="in_array($product->id, $favoriteIds ?? [])"
          :delivery-date="$deliveryDates[$product->id] ?? null"
          :index="$loop->index"
      />
      ```
--}}

@once
    <script>
        function productCard(productId, isInitialFavorite) {
            return {
                isFavorite: isInitialFavorite,
                toggling: false,
                async toggleFavorite() {
                    if (this.toggling) return;
                    this.toggling = true;
                    try {
                        const res = await fetch(`/favorites/toggle/${productId}`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                                'Accept': 'application/json',
                            },
                        });
                        const data = await res.json();
                        this.isFavorite = data.isFavorite;
                        if (window.Alpine && window.Alpine.store) {
                            window.Alpine.store('favorites').count = data.favoritesCount;
                        }
                    } catch (e) {
                        console.error('toggleFavorite error', e);
                    } finally {
                        this.toggling = false;
                    }
                },
            };
        }
    </script>
@endonce

@php
    // Merge caller-supplied config on top of the card defaults.
    $resolvedScrollerConfig = array_merge([
        'interval'         => 1500,
        'autoplay_mobile'  => true,
        'autoplay_desktop' => false,
    ], $scrollerConfig);

    $scrollerImages = $product->photos
        ->sortByDesc('is_primary')
        ->map(fn ($p) => asset('storage/' . $p->path));
@endphp

<div {{ $attributes->merge(['class' => 'bg-light rounded shadow hover:shadow-lg transition relative overflow-hidden isolate' . ($index !== null ? ' anim-item' : '')]) }}
     @if($index !== null) data-index="{{ $index }}" @endif
     x-data="productCard({{ $product->id }}, {{ $isFavorite ? 'true' : 'false' }})">

    {{-- Badges: featured / promo --}}
    <div class="absolute top-2 left-2 flex flex-col items-start gap-2 z-10">
        @if($product->is_featured)
            <span class="inline-block w-auto bg-accent-secondary text-white text-xs font-semibold uppercase px-2 py-1 rounded">
                {{ t('store.badge.featured') ?: 'FEATURED' }}
            </span>
        @endif
        @if($product->is_promo)
            <span class="inline-block w-auto bg-accent-primary text-white text-xs font-semibold uppercase px-2 py-1 rounded">
                {{ t('store.badge.promo') ?: 'PROMO' }}
            </span>
        @endif
    </div>

    {{-- Favourite toggle button --}}
    <button @click.prevent="toggleFavorite()"
            class="absolute top-2 right-2 p-2 bg-white rounded-full transition z-10"
            :disabled="toggling"
            :aria-label="isFavorite ? '{{ t('store.remove_from_favorites') ?: 'Remove from favourites' }}' : '{{ t('store.add_to_favorites') ?: 'Add to favourites' }}'">
        <svg xmlns="http://www.w3.org/2000/svg"
             :fill="isFavorite ? 'currentColor' : 'none'"
             viewBox="0 0 24 24"
             stroke="currentColor"
             class="w-5 h-5"
             :class="isFavorite ? 'text-status-error' : 'text-grey-medium'">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                  d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
        </svg>
    </button>

    {{-- Card body: image scroller + product info --}}
    <a href="{{ route('store.show', $product) }}" class="block">

        @if($scrollerImages->isNotEmpty())
            <x-image-scroller
                class="w-full aspect-square"
                :images="$scrollerImages"
                :config="$resolvedScrollerConfig"
            />
        @else
            <div class="w-full aspect-square bg-grey-light flex items-center justify-center">
                <span class="text-grey-medium text-sm">{{ t('store.no_photos') ?: 'No photo' }}</span>
            </div>
        @endif

        <div class="p-4 pt-3">
            <div class="font-semibold">
                {{ optional($product->translation())->name }}
            </div>

            <div class="text-sm text-grey-dark flex items-baseline">
                €{{ number_format($product->is_promo ? ($product->promo_price ?? $product->price) : $product->price, 2) }}
                @if($product->is_promo)
                    <span class="text-xs text-grey-medium line-through ml-2">
                        €{{ number_format($product->price, 2) }}
                    </span>
                @endif
            </div>

            @if($deliveryDate)
                <div class="text-xs text-grey-medium mt-1">
                    {{ t('store.expected_delivery') ?: 'Expected delivery' }}: {{ $deliveryDate }}
                </div>
            @endif
        </div>

    </a>
</div>
