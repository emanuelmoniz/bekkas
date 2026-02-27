<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>{{ config('app.name', 'BEKKAS') }} - {{ t('nav.portfolio') ?: 'Portfolio' }}</title>

        <x-favorites-init />

        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif

        @php
            $pgPrevious   = t('pagination.previous')   ?: 'Previous';
            $pgNext       = t('pagination.next')        ?: 'Next';
            $pgNavigation = t('pagination.navigation')  ?: 'Pagination Navigation';
            $pgShowing    = t('pagination.showing', ['first' => ':first', 'last' => ':last', 'total' => ':total'])
                            ?: 'Showing :first to :last of :total results';
        @endphp
        <script>
            window.__portfolioProjects = @json(
                $projects->map(fn ($p) => [
                    'year'        => $p->production_date?->year ?? 0,
                    'materialIds' => $p->materials->pluck('id')->values()->all(),
                ])
            );
            window.__paginationStrings = {
                previous:   @json($pgPrevious),
                next:       @json($pgNext),
                navigation: @json($pgNavigation),
                showing:    @json($pgShowing),
            };
        </script>
    </head>
    <body class="bg-white text-dark">
        @include('layouts.navigation')

        {{-- ──────────────────────────────────────────────────────────────────
             Main Alpine component wraps everything so filter state is shared.
        ────────────────────────────────────────────────────────────────────── --}}
        <div
            x-data="{
                selectedYears: [],
                selectedMaterials: [],
                projects: window.__portfolioProjects || [],
                currentPage: 1,
                perPage: 16,
                toggleYear(year) {
                    const idx = this.selectedYears.indexOf(year);
                    if (idx >= 0) {
                        this.selectedYears.splice(idx, 1);
                    } else {
                        this.selectedYears.push(year);
                    }
                    this.currentPage = 1;
                },
                toggleMaterial(id) {
                    const idx = this.selectedMaterials.indexOf(id);
                    if (idx >= 0) {
                        this.selectedMaterials.splice(idx, 1);
                    } else {
                        this.selectedMaterials.push(id);
                    }
                    this.currentPage = 1;
                },
                _passesFilter(year, materialIds) {
                    const yearOk = this.selectedYears.length === 0    || this.selectedYears.includes(year);
                    const matOk  = this.selectedMaterials.length === 0 || materialIds.some(m => this.selectedMaterials.includes(m));
                    return yearOk && matOk;
                },
                get filteredIndices() {
                    return this.projects.reduce((acc, p, i) => {
                        if (this._passesFilter(p.year, p.materialIds)) acc.push(i);
                        return acc;
                    }, []);
                },
                get pagedIndices() {
                    const start = (this.currentPage - 1) * this.perPage;
                    return this.filteredIndices.slice(start, start + this.perPage);
                },
                isVisible(index) {
                    return this.pagedIndices.includes(index);
                },
                get filteredTotal() {
                    return this.filteredIndices.length;
                },
                get firstItem() {
                    if (this.filteredTotal === 0) return 0;
                    return (this.currentPage - 1) * this.perPage + 1;
                },
                get lastItem() {
                    return Math.min(this.currentPage * this.perPage, this.filteredTotal);
                },
                get showingText() {
                    const s = (window.__paginationStrings || {}).showing || 'Showing :first to :last of :total results';
                    return s
                        .replace(':first', this.firstItem)
                        .replace(':last',  this.lastItem)
                        .replace(':total', this.filteredTotal);
                },
                get hasVisibleProjects() {
                    return this.filteredTotal > 0;
                },
                get totalPages() {
                    return Math.max(1, Math.ceil(this.filteredTotal / this.perPage));
                },
                get showPagination() {
                    return this.totalPages > 1;
                },
                prevPage() {
                    if (this.currentPage > 1) this.currentPage--;
                },
                nextPage() {
                    if (this.currentPage < this.totalPages) this.currentPage++;
                },
                goToPage(n) {
                    this.currentPage = n;
                },
                get pageNumbers() {
                    const total = this.totalPages;
                    if (total <= 7) return Array.from({length: total}, (_, i) => i + 1);
                    const curr  = this.currentPage;
                    const left  = Math.max(2, curr - 1);
                    const right = Math.min(total - 1, curr + 1);
                    const pages = [1];
                    if (left > 2) pages.push('...');
                    for (let i = left; i <= right; i++) pages.push(i);
                    if (right < total - 1) pages.push('...');
                    pages.push(total);
                    return pages;
                }
            }"
        >

        {{-- ── HERO / TITLE SECTION ────────────────────────────────────────── --}}
        <section class="bg-secondary py-12 md:py-16">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h1 class="uppercase text-4xl md:text-5xl font-bold text-dark mb-3">
                    {{ t('portfolio.title') ?: 'Portfolio' }}
                </h1>
                <p class="text-grey-dark text-lg max-w-2xl mx-auto">
                    {{ t('portfolio.subtitle') ?: 'A showcase of our 3D printing projects.' }}
                </p>
            </div>
        </section>

        {{-- ── FILTER SECTION ─────────────────────────────────────────────── --}}
        <section class="bg-light border-t border-grey-medium/20 pt-8">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                @if($years->isNotEmpty() || $materials->isNotEmpty())
                <div class="flex flex-col gap-4">

                    {{-- Year filters --}}
                    @if($years->isNotEmpty())
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="text-xs font-semibold uppercase tracking-widest text-grey-dark mr-1 shrink-0">
                            {{ t('portfolio.filter.year') ?: 'Year' }}
                        </span>
                        @foreach($years as $year)
                        <button
                            type="button"
                            @click="toggleYear({{ $year }})"
                            :class="selectedYears.includes({{ $year }})
                                ? 'bg-primary text-white border-primary'
                                : 'bg-white text-grey-dark border-grey-medium hover:border-primary hover:text-primary'"
                            class="px-4 py-1.5 rounded-full border text-sm font-medium transition-colors duration-150 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary"
                        >
                            {{ $year }}
                        </button>
                        @endforeach
                    </div>
                    @endif

                    {{-- Material filters --}}
                    @if($materials->isNotEmpty())
                    <div class="flex flex-wrap items-center gap-2">
                        <span class="text-xs font-semibold uppercase tracking-widest text-grey-dark mr-1 shrink-0">
                            {{ t('portfolio.filter.material') ?: 'Material' }}
                        </span>
                        @foreach($materials as $material)
                        @php $matName = $material->translation()?->name ?? $material->id; @endphp
                        <button
                            type="button"
                            @click="toggleMaterial({{ $material->id }})"
                            :class="selectedMaterials.includes({{ $material->id }})
                                ? 'bg-primary text-white border-primary'
                                : 'bg-white text-grey-dark border-grey-medium hover:border-primary hover:text-primary'"
                            class="px-4 py-1.5 rounded-full border text-sm font-medium transition-colors duration-150 focus:outline-none focus-visible:ring-2 focus-visible:ring-primary"
                        >
                            {{ $matName }}
                        </button>
                        @endforeach
                    </div>
                    @endif

                </div>
                @endif

            </div>
        </section>

        {{-- ── PROJECTS GRID ───────────────────────────────────────────────── --}}
        <section class="bg-light pt-6 md:pt-8 pb-10 md:pb-14">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

                @if($projects->isEmpty())
                    <p class="text-center text-grey-dark py-20">
                        {{ t('portfolio.no_projects') ?: 'No projects yet. Check back soon!' }}
                    </p>
                @else
                    <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 md:gap-6">
                        @foreach($projects as $project)
                        <div
                            data-portfolio-card
                            x-show="isVisible({{ $loop->index }})"
                            x-transition:leave="transition ease-in duration-150"
                            x-transition:leave-start="opacity-100"
                            x-transition:leave-end="opacity-0"
                            x-transition:enter="transition ease-out duration-200"
                            x-transition:enter-start="opacity-0"
                            x-transition:enter-end="opacity-100"
                        >
                            <x-project-card :project="$project" :animated="false" />
                        </div>
                        @endforeach
                    </div>

                    {{-- Empty-state message when filters leave nothing visible --}}
                    <p
                        x-show="!hasVisibleProjects && (selectedYears.length > 0 || selectedMaterials.length > 0)"
                        x-cloak
                        class="text-center text-grey-dark py-12 col-span-full"
                    >{{ t('portfolio.no_results') ?: 'No projects match the selected filters.' }}</p>

                    {{-- ── PAGINATION (matches store tailwind pagination style) ── --}}
                    <nav
                        x-show="showPagination"
                        x-cloak
                        role="navigation"
                        :aria-label="(window.__paginationStrings || {}).navigation || 'Pagination Navigation'"
                        class="mt-6"
                    >
                        {{-- Mobile: simple prev / next --}}
                        <div class="flex gap-2 items-center justify-between sm:hidden">
                            <button
                                type="button"
                                @click="prevPage()"
                                :disabled="currentPage === 1"
                                :class="currentPage === 1
                                    ? 'text-gray-600 border-gray-300 cursor-not-allowed'
                                    : 'text-gray-800 border-gray-300 hover:text-gray-700 hover:bg-gray-100'"
                                class="inline-flex items-center px-4 py-2 text-sm font-medium border leading-5 rounded-md transition ease-in-out duration-150"
                                x-text="(window.__paginationStrings || {}).previous || 'Previous'"
                            ></button>
                            <button
                                type="button"
                                @click="nextPage()"
                                :disabled="currentPage === totalPages"
                                :class="currentPage === totalPages
                                    ? 'text-gray-600 border-gray-300 cursor-not-allowed'
                                    : 'text-gray-800 border-gray-300 hover:text-gray-700 hover:bg-gray-100'"
                                class="inline-flex items-center px-4 py-2 text-sm font-medium border leading-5 rounded-md transition ease-in-out duration-150"
                                x-text="(window.__paginationStrings || {}).next || 'Next'"
                            ></button>
                        </div>

                        {{-- Desktop: showing text + page buttons --}}
                        <div class="hidden sm:flex-1 sm:flex sm:gap-2 sm:items-center sm:justify-between">
                            <div>
                                <p class="text-sm text-gray-700 leading-5" x-text="showingText"></p>
                            </div>
                            <div>
                                <span class="inline-flex rtl:flex-row-reverse shadow-sm rounded-md">

                                    {{-- Previous --}}
                                    <button
                                        type="button"
                                        @click="prevPage()"
                                        :disabled="currentPage === 1"
                                        :class="currentPage === 1
                                            ? 'text-gray-500 cursor-not-allowed'
                                            : 'text-gray-500 hover:text-gray-400 active:bg-gray-100 active:text-gray-500'"
                                        class="inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 border border-gray-300 rounded-l-md leading-5 transition ease-in-out duration-150"
                                        :aria-label="(window.__paginationStrings || {}).previous || 'Previous'"
                                    >
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                                    </button>

                                    {{-- Page numbers / ellipsis --}}
                                    <template x-for="(item, i) in pageNumbers" :key="i">
                                        <span style="display:contents">
                                            <span
                                                x-show="item === '...'"
                                                class="inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-700 border border-gray-300 cursor-default leading-5"
                                                aria-hidden="true"
                                            >…</span>
                                            <button
                                                x-show="item !== '...'"
                                                type="button"
                                                @click="goToPage(item)"
                                                :class="item === currentPage
                                                    ? 'text-gray-700 bg-gray-200 cursor-default'
                                                    : 'text-gray-700 hover:text-gray-700 hover:bg-gray-100 active:bg-gray-100'"
                                                class="inline-flex items-center px-4 py-2 -ml-px text-sm font-medium border border-gray-300 leading-5 transition ease-in-out duration-150"
                                                :aria-current="item === currentPage ? 'page' : null"
                                                x-text="item"
                                            ></button>
                                        </span>
                                    </template>

                                    {{-- Next --}}
                                    <button
                                        type="button"
                                        @click="nextPage()"
                                        :disabled="currentPage === totalPages"
                                        :class="currentPage === totalPages
                                            ? 'text-gray-500 cursor-not-allowed'
                                            : 'text-gray-500 hover:text-gray-400 active:bg-gray-100 active:text-gray-500'"
                                        class="inline-flex items-center px-2 py-2 -ml-px text-sm font-medium text-gray-500 border border-gray-300 rounded-r-md leading-5 transition ease-in-out duration-150"
                                        :aria-label="(window.__paginationStrings || {}).next || 'Next'"
                                    >
                                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg>
                                    </button>

                                </span>
                            </div>
                        </div>
                    </nav>
                @endif

            </div>
        </section>

        {{-- ── CTA / NAVIGATION SECTION ──────────────────────────────────── --}}
        <section class="py-16 md:py-24 bg-primary animate-sequence">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center text-white anim-item" data-index="0">
                <h2 class="text-4xl font-bold mb-6">
                    {{ t('portfolio.cta.title') ?: 'Have a Project in Mind?' }}
                </h2>
                <p class="text-xl mb-8 max-w-2xl mx-auto">
                    {{ t('portfolio.cta.description') ?: 'We bring your ideas to life with precision 3D printing. Tell us about your project.' }}
                </p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center animate-sequence">
                    @if(config('app.store_enabled'))
                        <a href="{{ route('store.index') }}"
                           class="inline-block bg-white text-primary hover:bg-grey-light px-8 py-3 rounded-full uppercase font-semibold transition-colors anim-item"
                           data-index="0">
                            {{ t('portfolio.cta.shop') ?: 'Browse Products' }}
                        </a>
                    @endif
                    <a href="{{ route('custom.index') }}"
                       class="inline-block bg-white/10 hover:bg-white/20 text-white border border-white/40 px-8 py-3 rounded-full uppercase font-semibold transition-colors anim-item"
                       data-index="1">
                        {{ t('portfolio.cta.custom') ?: 'Start a Custom Project' }}
                    </a>
                </div>
            </div>
        </section>

        </div>{{-- end x-data --}}

        @include('layouts.footer')
    </body>
</html>
