@props([
    'categories' => null,       // Collection<Category> | array | null — filter by category; null = all products
    'max'        => null,       // int|null — maximum items to fetch; null = unlimited
    'order'      => 'newest',   // 'newest' | 'random'
    'isPromo'    => false,      // bool — when true filter to is_promo products only
    'isFeatured' => false,      // bool — when true filter to is_featured products only
    'title'      => null,       // string|null — optional section heading
    'excludeId'  => null,       // int|null — product ID to omit (e.g. the currently viewed product)
])

{{--
    Related Products horizontal scroll section.

    **Props:**
      - `categories`  – Collection or array of Category models whose IDs are used to filter
                        products.  Pass null (default) to show all active products.
      - `max`         – Maximum number of products to display.  null = unlimited.
      - `order`       – 'newest' (default, orders by created_at DESC) | 'random'.
      - `isPromo`     – When true, only is_promo products are shown.  When false (default),
                        no promo filter is applied.
      - `isFeatured`  – When true, only is_featured products are shown.  When false (default),
                        no featured filter is applied.
      - `title`       – Optional heading text rendered above the scroller.
      - `excludeId`   – Product integer ID to exclude from results (pass current $product->id
                        on the show page to avoid showing the same product in the list).

    **Layout:**
      - 4 cards per row on desktop  (sm:w-[calc(25%-12px)])
      - 2 cards per row on mobile   (w-[calc(50%-8px)])
      - Navigation prev/next arrows are rendered when items overflow:
          • count > 4 → arrows on desktop  (+ mobile)
          • count > 2 → arrows on mobile only
      - Container uses `animate-sequence`; each card contributes `anim-item` + `data-index`
        via the product-card component (index prop).
--}}

@php
    use App\Models\Product;

    $query = Product::with(['translations', 'photos'])
        ->where('active', true);

    // Exclude a specific product (e.g. the one already being viewed).
    if ($excludeId !== null) {
        $query->where('id', '!=', $excludeId);
    }

    // Category filter — if categories provided, restrict to products in those categories.
    $cats = collect($categories ?? []);
    if ($cats->isNotEmpty()) {
        $catIds = $cats->pluck('id');
        $query->whereHas('categories', fn ($q) => $q->whereIn('categories.id', $catIds));
    }

    // Optional promo / featured filters.
    if ($isPromo) {
        $query->where('is_promo', true);
    }
    if ($isFeatured) {
        $query->where('is_featured', true);
    }

    // Ordering.
    $order === 'random' ? $query->inRandomOrder() : $query->latest();

    // Limit.
    if ($max !== null) {
        $query->limit((int) $max);
    }

    $relatedProducts = $query->get();
    $count           = $relatedProducts->count();

    // Navigation arrows appear only when items overflow the visible viewport.
    // Desktop shows 4 cards, mobile shows 2.
    $showNavDesktop = $count > 4;
    $showNavMobile  = $count > 2;
@endphp

@if ($relatedProducts->isNotEmpty())
<section class="py-8 border-t border-grey-light">
    <div class="max-w-5xl mx-auto sm:px-6 lg:px-8">

        @if ($title)
            <h3 class="text-lg font-semibold text-grey-dark mb-4 px-4 sm:px-0">{{ $title }}</h3>
        @endif

        {{--
            Alpine component handles scroll-state tracking so we can conditionally
            show/hide the prev & next arrow buttons.
        --}}
        <div
            x-data="{
                canScrollLeft: false,
                canScrollRight: false,
                init() {
                    this.update();
                },
                update() {
                    const s = this.$refs.scroller;
                    this.canScrollLeft  = s.scrollLeft > 1;
                    this.canScrollRight = s.scrollLeft < s.scrollWidth - s.clientWidth - 1;
                },
                prev() {
                    this.$refs.scroller.scrollBy({
                        left: -(this.$refs.scroller.clientWidth / 2),
                        behavior: 'smooth',
                    });
                },
                next() {
                    this.$refs.scroller.scrollBy({
                        left: (this.$refs.scroller.clientWidth / 2),
                        behavior: 'smooth',
                    });
                },
            }"
            x-init="init(); $nextTick(() => update())"
            class="relative px-4 sm:px-0"
        >
            {{-- ◀ Prev arrow --}}
            @if ($showNavMobile)
                <button
                    x-show="canScrollLeft"
                    x-transition.opacity
                    @click="prev"
                    class="absolute top-1/2 -translate-y-1/2 z-10 flex items-center justify-center bg-light shadow-md rounded-full text-grey-dark hover:bg-grey-light transition -left-1 w-8 h-8 {{ $showNavDesktop ? 'sm:-left-5 sm:w-9 sm:h-9' : 'sm:hidden' }}"
                    aria-label="{{ t('store.scroll_prev') ?: 'Previous' }}"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 sm:w-5 sm:h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M15 19l-7-7 7-7"/>
                    </svg>
                </button>
            @endif

            {{-- ▶ Next arrow --}}
            @if ($showNavMobile)
                <button
                    x-show="canScrollRight"
                    x-transition.opacity
                    @click="next"
                    class="absolute top-1/2 -translate-y-1/2 z-10 flex items-center justify-center bg-light shadow-md rounded-full text-grey-dark hover:bg-grey-light transition -right-1 w-8 h-8 {{ $showNavDesktop ? 'sm:-right-5 sm:w-9 sm:h-9' : 'sm:hidden' }}"
                    aria-label="{{ t('store.scroll_next') ?: 'Next' }}"
                >
                    <svg xmlns="http://www.w3.org/2000/svg" class="w-4 h-4 sm:w-5 sm:h-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7"/>
                    </svg>
                </button>
            @endif

            {{--
                Horizontal scroll track.
                The outer div owns overflow-x:auto and the x-ref for Alpine scroll tracking.
                The inner div owns the flex layout and animate-sequence.
                Splitting them prevents the browser from silently setting overflow-y:auto on
                the same element (which clips the translate-y-8 of .anim-item cards).
            --}}
            <div
                x-ref="scroller"
                @scroll.debounce.50ms="update()"
                class="overflow-x-auto overflow-y-hidden scroll-smooth snap-x snap-mandatory no-scrollbar"
            >
                <div class="flex gap-4 animate-sequence pb-2">
                    @foreach ($relatedProducts as $relatedProduct)
                        <x-product-card
                            class="flex-none w-[calc(50%-8px)] sm:w-[calc(25%-12px)] snap-start"
                            :product="$relatedProduct"
                            :index="$loop->index"
                        />
                    @endforeach
                </div>
            </div>
        </div>

    </div>
</section>
@endif
