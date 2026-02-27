<x-app-layout>
    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            @if($hasFavorites)
                <div class="grid grid-cols-1 lg:grid-cols-[260px_1fr] gap-6 items-start">

                    {{-- FILTERS SIDEBAR --}}
                    <aside>
                        <x-product-filters
                            :categories="$categories"
                            :materials="$materials"
                            :category-counts="$categoryCounts"
                            :material-counts="$materialCounts"
                            reset-route="favorites.index"
                        />
                    </aside>

                    {{-- PRODUCTS --}}
                    <div>
                        @if($products->total() > 0)
                            <div class="grid grid-cols-2 lg:grid-cols-3 gap-6 animate-sequence">
                                @foreach ($products as $product)
                                    <x-product-card
                                        :product="$product"
                                        :is-favorite="in_array($product->id, $favoriteIds)"
                                        :delivery-date="$deliveryDates[$product->id] ?? null"
                                        :animated="true"
                                    />
                                @endforeach
                            </div>

                            <div class="mt-6">
                                {{ $products->links() }}
                            </div>
                        @else
                            <div class="bg-white p-6 rounded shadow text-center text-grey-dark">
                                {{ t('favorites.no_results') ?: 'No favorites match your filter criteria.' }}
                            </div>
                        @endif
                    </div>

                </div>
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

    @if($hasFavorites && $categories->isNotEmpty())
        {{-- RELATED PRODUCTS --}}
        <div class="animate-sequence bg-secondary">
            <x-related-products
                class="anim-item"
                :categories="$categories"
                :exclude-ids="$favoriteIds"
                :title="t('store.related_products') ?: 'Related Products'"
                order="random"
                :max="8"
            />
        </div>
    @endif
</x-app-layout>
