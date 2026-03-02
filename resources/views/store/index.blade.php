<x-app-layout>

    <div class="py-6">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

            <div class="grid grid-cols-1 lg:grid-cols-[260px_1fr] gap-6 items-start">

                {{-- FILTERS SIDEBAR --}}
                <aside>
                    <x-product-filters
                        :categories="$categories"
                        :materials="$materials"
                        :category-counts="$categoryCounts"
                        :material-counts="$materialCounts"
                        :price-floor="$priceFloor"
                        :price-ceiling="$priceCeiling"
                        reset-route="store.index"
                    />
                </aside>

                {{-- PRODUCTS --}}
                <div>
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-6 animate-sequence">
                        @forelse ($products as $product)
                            <x-product-card
                                :product="$product"
                                :is-favorite="in_array($product->id, $favoriteIds ?? [])"
                                :delivery-date="$deliveryDates[$product->id] ?? null"
                                :animated="true"
                            />
                        @empty
                            <p class="text-grey-dark col-span-full text-center">
                                {{ t('store.no_products') ?: 'No products found.' }}
                            </p>
                        @endforelse
                    </div>

                    <div class="mt-6">
                        {{ $products->links() }}
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
