@props([
    /**
     * Numeric product ID – required.
     */
    'productId',

    /**
     * Whether the product is already favourited by the current user/session.
     * Defaults to false.
     */
    'isFavorite' => false,

    /**
     * Icon size variant: 'sm' (w-5 h-5) | 'md' (w-6 h-6, default) | 'lg' (w-7 h-7).
     */
    'iconSize' => 'md',
])

{{--
    Self-contained favourite toggle button.

    Uses `favoriteToggle()` (registered via Alpine.data in app.js) as its own
    Alpine scope so it can be dropped into any context – product card, product
    details page, etc. – without coupling to a parent component's data.

    The button's base classes are `p-2 rounded-full transition`; pass extra
    classes via the `class` attribute to position or style from the call site.

    **Examples:**
      Product card (absolute positioned top-right):
        <x-favorite-button
            :product-id="$product->id"
            :is-favorite="$isFavorite"
            class="absolute top-2 right-2 bg-white z-10"
        />

      Product details page:
        <x-favorite-button
            :product-id="$product->id"
            :is-favorite="$isFavorite ?? false"
            class="hover:bg-grey-light"
            icon-size="lg"
        />
--}}

@php
    $iconSizeClass = match ($iconSize) {
        'sm'    => 'w-5 h-5',
        'lg'    => 'w-7 h-7',
        default => 'w-6 h-6',
    };

    $addLabel    = t('store.add_to_favorites')    ?: 'Add to favourites';
    $removeLabel = t('store.remove_from_favorites') ?: 'Remove from favourites';
@endphp

<button {{ $attributes->merge(['class' => 'p-2 rounded-full transition']) }}
        x-data="favoriteToggle({{ $productId }}, {{ $isFavorite ? 'true' : 'false' }})"
        @click.prevent="toggleFavorite()"
        :disabled="toggling"
        :aria-label="isFavorite ? '{{ $removeLabel }}' : '{{ $addLabel }}'">
    <svg xmlns="http://www.w3.org/2000/svg"
         :fill="isFavorite ? 'currentColor' : 'none'"
         viewBox="0 0 24 24"
         stroke="currentColor"
         class="{{ $iconSizeClass }}"
         :class="isFavorite ? 'text-status-error' : 'text-grey-medium'">
        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
              d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z" />
    </svg>
</button>
