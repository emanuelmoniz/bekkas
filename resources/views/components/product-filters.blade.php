@props([
    'categories',
    'materials',
    'categoryCounts' => [],
    'materialCounts' => [],
    'priceFloor' => 0,
    'priceCeiling' => 0,
    'resetRoute' => 'store.index',
])

{{--
    Mobile: collapsible panel (Alpine.js toggle).
    Desktop: always-visible sidebar.
--}}
<div x-data="{ open: false }">

    {{-- Mobile toggle button --}}
    <button type="button"
            @click="open = !open"
            class="md:hidden w-full flex items-center justify-between bg-white border border-grey-light rounded px-4 py-3 mb-2 font-semibold">
        <span>{{ t('store.filter.filters') ?: 'Filters' }}</span>
        <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
        </svg>
    </button>

    {{-- Filter panel: hidden on mobile when closed, always visible on md+ --}}
    <div x-show="open" x-cloak class="md:hidden">
        @include('components.partials.product-filters-body', ['resetRoute' => $resetRoute, 'categories' => $categories, 'materials' => $materials, 'categoryCounts' => $categoryCounts, 'materialCounts' => $materialCounts, 'priceFloor' => $priceFloor, 'priceCeiling' => $priceCeiling])
    </div>

    <div class="hidden md:block">
        @include('components.partials.product-filters-body', ['resetRoute' => $resetRoute, 'categories' => $categories, 'materials' => $materials, 'categoryCounts' => $categoryCounts, 'materialCounts' => $materialCounts, 'priceFloor' => $priceFloor, 'priceCeiling' => $priceCeiling])
    </div>

</div>
