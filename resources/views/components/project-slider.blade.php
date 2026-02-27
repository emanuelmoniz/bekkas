@props([
    'materials'   => null,      // Collection<Material> | array | null — filter by material; null = all active projects
    'max'         => null,      // int|null — maximum items to fetch; null = unlimited
    'order'       => 'newest',  // 'newest' | 'random'
    'isFeatured'  => false,     // bool — when true filter to is_featured projects only
    'title'       => null,      // string|null — optional section heading
])

{{--
    Project Slider horizontal scroll section.

    **Props:**
      - `materials`   – Collection or array of Material models whose IDs are used to filter
                        projects.  Pass null (default) to show all active projects.
      - `max`         – Maximum number of projects to display.  null = unlimited.
      - `order`       – 'newest' (default, orders by production_date DESC then created_at DESC)
                        | 'random'.
      - `isFeatured`  – When true, only is_featured projects are shown.  When false (default),
                        no featured filter is applied.
      - `title`       – Optional heading text rendered above the scroller.

    **Layout:**
      - 4 cards per row on desktop  (sm:w-[calc(25%-12px)])
      - 2 cards per row on mobile   (w-[calc(50%-8px)])
      - Navigation prev/next arrows are rendered when items overflow:
          • count > 4 → arrows on desktop  (+ mobile)
          • count > 2 → arrows on mobile only
      - Container uses `animate-sequence`; each card contributes `anim-item`
        via the project-card component (animated prop).
--}}

@php
    use App\Models\Project;

    $query = Project::with(['translations', 'photos'])
        ->where('is_active', true);

    // Material (category) filter — if materials provided, restrict to projects that use them.
    $mats = collect($materials ?? []);
    if ($mats->isNotEmpty()) {
        $matIds = $mats->pluck('id');
        $query->whereHas('materials', fn ($q) => $q->whereIn('materials.id', $matIds));
    }

    // Optional featured filter.
    if ($isFeatured) {
        $query->where('is_featured', true);
    }

    // Ordering.
    if ($order === 'random') {
        $query->inRandomOrder();
    } else {
        $query->orderByRaw('COALESCE(production_date, created_at) DESC');
    }

    // Limit.
    if ($max !== null) {
        $query->limit((int) $max);
    }

    $projects = $query->get();
    $count    = $projects->count();

    // Navigation arrows appear only when items overflow the visible viewport.
    // Desktop shows 4 cards, mobile shows 2.
    $showNavDesktop = $count > 4;
    $showNavMobile  = $count > 2;
@endphp

@if ($projects->isNotEmpty())
<div>
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
            cardWidth() {
                const first = this.$refs.scroller.querySelector('.snap-start');
                return first ? first.offsetWidth + 16 : Math.round(this.$refs.scroller.clientWidth / 4);
            },
            prev() {
                this.$refs.scroller.scrollBy({
                    left: -this.cardWidth(),
                    behavior: 'smooth',
                });
            },
            next() {
                this.$refs.scroller.scrollBy({
                    left: this.cardWidth(),
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
                class="absolute top-1/2 -translate-y-1/2 z-10 flex items-center justify-center bg-white shadow-md rounded-full text-grey-dark hover:bg-grey-light transition -left-1 w-8 h-8 {{ $showNavDesktop ? 'sm:-left-5 sm:w-9 sm:h-9' : 'sm:hidden' }}"
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
                class="absolute top-1/2 -translate-y-1/2 z-10 flex items-center justify-center bg-white shadow-md rounded-full text-grey-dark hover:bg-grey-light transition -right-1 w-8 h-8 {{ $showNavDesktop ? 'sm:-right-5 sm:w-9 sm:h-9' : 'sm:hidden' }}"
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
            <div class="flex gap-4 animate-sequence pb-4">
                @foreach ($projects as $project)
                    <x-project-card
                        class="flex-none w-[calc(50%-8px)] sm:w-[calc(25%-12px)] snap-start"
                        :project="$project"
                        :animated="true"
                    />
                @endforeach
            </div>
        </div>
    </div>
</div>
@endif
