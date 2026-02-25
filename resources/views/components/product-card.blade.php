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

<div {{ $attributes->merge(['class' => 'bg-white rounded shadow hover:shadow-lg transition relative overflow-hidden isolate' . ($index !== null ? ' anim-item' : '')]) }}
     @if($index !== null) data-index="{{ $index }}" @endif>

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
    <x-favorite-button
        :product-id="$product->id"
        :is-favorite="$isFavorite"
        class="absolute top-2 right-2 bg-white z-10"
        icon-size="sm"
    />

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
            <h3>
                {{ optional($product->translation())->name }}
            </h3>

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
