/**
 * Alpine.js data factory for the favourite toggle button.
 *
 * Registered globally as `favoriteToggle` via Alpine.data() in app.js so that
 * it is available to any template without extra <script> blocks.
 *
 * Usage in Blade:
 *   <button x-data="favoriteToggle(productId, initialFavorite)" @click.prevent="toggleFavorite()" …>
 *
 * Or via the dedicated Blade component:
 *   <x-favorite-button :product-id="$product->id" :is-favorite="$isFavorite" />
 *
 * @param {number}  productId       – the product's numeric id
 * @param {boolean} initialFavorite – whether the product is already favourited
 */
export function favoriteToggle(productId, initialFavorite) {
    return {
        isFavorite: initialFavorite,
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
                if (window.Alpine?.store) {
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
