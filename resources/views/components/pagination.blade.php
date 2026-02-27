{{--
    Unified pagination component.

    Usage – server-side (Laravel paginator):
        <x-pagination :paginator="$products" />

    Usage – Alpine.js client-side (portfolio with live filtering):
        <x-pagination :alpine="true" />
        The parent x-data scope must expose:
            showPagination, currentPage, totalPages, showingText,
            pageNumbers, prevPage(), nextPage(), goToPage()
        and window.__paginationStrings = { previous, next, navigation, showing }
--}}
@props([
    'paginator' => null,
    'elements'  => null,   // pre-computed by Laravel; passed from tailwind.blade.php
    'alpine'    => false,
])

@php
    $isAlpine = (bool) $alpine;
    // When called from tailwind.blade.php $elements is passed explicitly.
    // Fallback: compute from paginator so the component is self-contained.
    $elements = $elements ?? ($paginator ? $paginator->elements() : []);
@endphp

{{-- Alpine mode: always render, x-show handles visibility.
     Server mode: only render when there are multiple pages. --}}
@if($isAlpine || ($paginator && $paginator->hasPages()))
<nav
    @if($isAlpine)
        x-show="showPagination"
        x-cloak
        :aria-label="(window.__paginationStrings || {}).navigation || 'Pagination Navigation'"
    @else
        aria-label="{{ t('pagination.navigation') }}"
    @endif
    role="navigation"
    class="mt-6"
>

    {{-- ── MOBILE: prev / next only ─────────────────────────────────────── --}}
    <div class="flex gap-2 items-center justify-between sm:hidden">

        @if($isAlpine)

            <button
                type="button"
                @click="prevPage(); window.scrollTo({top:0, behavior:'smooth'})"
                :disabled="currentPage === 1"
                :class="currentPage === 1
                    ? 'text-gray-600 border-gray-300 cursor-not-allowed'
                    : 'text-gray-800 border-gray-300 hover:text-gray-700 hover:bg-gray-100'"
                class="inline-flex items-center px-4 py-2 text-sm font-medium border leading-5 rounded-md transition ease-in-out duration-150"
                x-text="(window.__paginationStrings || {}).previous || 'Previous'"
            ></button>

            <button
                type="button"
                @click="nextPage(); window.scrollTo({top:0, behavior:'smooth'})"
                :disabled="currentPage === totalPages"
                :class="currentPage === totalPages
                    ? 'text-gray-600 border-gray-300 cursor-not-allowed'
                    : 'text-gray-800 border-gray-300 hover:text-gray-700 hover:bg-gray-100'"
                class="inline-flex items-center px-4 py-2 text-sm font-medium border leading-5 rounded-md transition ease-in-out duration-150"
                x-text="(window.__paginationStrings || {}).next || 'Next'"
            ></button>

        @else

            @if ($paginator->onFirstPage())
                <span class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 border border-gray-300 cursor-not-allowed leading-5 rounded-md">
                    {{ t('pagination.previous') }}
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-800 border border-gray-300 leading-5 rounded-md hover:text-gray-700 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-800 transition ease-in-out duration-150 hover:bg-gray-100">
                    {{ t('pagination.previous') }}
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-800 border border-gray-300 leading-5 rounded-md hover:text-gray-700 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-800 transition ease-in-out duration-150 hover:bg-gray-100">
                    {{ t('pagination.next') }}
                </a>
            @else
                <span class="inline-flex items-center px-4 py-2 text-sm font-medium text-gray-600 border border-gray-300 cursor-not-allowed leading-5 rounded-md">
                    {{ t('pagination.next') }}
                </span>
            @endif

        @endif

    </div>

    {{-- ── DESKTOP: showing text + numbered buttons ─────────────────────── --}}
    <div class="hidden sm:flex-1 sm:flex sm:gap-2 sm:items-center sm:justify-between">

        <div>
            @if($isAlpine)
                <p class="text-sm text-gray-700 leading-5" x-text="showingText"></p>
            @else
                <p class="text-sm text-gray-700 leading-5">
                    {{ t('pagination.showing', [
                        'first' => $paginator->firstItem(),
                        'last'  => $paginator->lastItem(),
                        'total' => $paginator->total(),
                    ]) }}
                </p>
            @endif
        </div>

        <div>
            <span class="inline-flex rtl:flex-row-reverse shadow-sm rounded-md">

                {{-- Previous page --}}
                @if($isAlpine)

                    <button
                        type="button"
                        @click="prevPage(); window.scrollTo({top:0, behavior:'smooth'})"
                        :disabled="currentPage === 1"
                        :class="currentPage === 1
                            ? 'text-gray-500 cursor-not-allowed'
                            : 'text-gray-500 hover:text-gray-400 active:bg-gray-100 active:text-gray-500'"
                        class="inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 border border-gray-300 rounded-l-md leading-5 transition ease-in-out duration-150"
                        :aria-label="(window.__paginationStrings || {}).previous || 'Previous'"
                    >
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                    </button>

                @else

                    @if ($paginator->onFirstPage())
                        <span aria-disabled="true" aria-label="{{ t('pagination.previous') }}">
                            <span class="inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 border border-gray-300 cursor-not-allowed rounded-l-md leading-5" aria-hidden="true">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                            </span>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="inline-flex items-center px-2 py-2 text-sm font-medium text-gray-500 border border-gray-300 rounded-l-md leading-5 hover:text-gray-400 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-500 transition ease-in-out duration-150" aria-label="{{ t('pagination.previous') }}">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                        </a>
                    @endif

                @endif

                {{-- Page numbers / ellipsis --}}
                @if($isAlpine)

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
                                @click="goToPage(item); window.scrollTo({top:0, behavior:'smooth'})"
                                :class="item === currentPage
                                    ? 'text-gray-700 bg-gray-200 cursor-default'
                                    : 'text-gray-700 hover:text-gray-700 hover:bg-gray-100 active:bg-gray-100'"
                                class="inline-flex items-center px-4 py-2 -ml-px text-sm font-medium border border-gray-300 leading-5 transition ease-in-out duration-150"
                                :aria-current="item === currentPage ? 'page' : null"
                                x-text="item"
                            ></button>
                        </span>
                    </template>

                @else

                    @foreach ($elements as $element)
                        @if (is_string($element))
                            <span aria-disabled="true">
                                <span class="inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-700 border border-gray-300 cursor-default leading-5">{{ $element }}</span>
                            </span>
                        @endif
                        @if (is_array($element))
                            @foreach ($element as $page => $url)
                                @if ($page == $paginator->currentPage())
                                    <span aria-current="page">
                                        <span class="inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-700 bg-gray-200 border border-gray-300 cursor-default leading-5">{{ $page }}</span>
                                    </span>
                                @else
                                    <a href="{{ $url }}" class="inline-flex items-center px-4 py-2 -ml-px text-sm font-medium text-gray-700 border border-gray-300 leading-5 hover:text-gray-700 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-700 transition ease-in-out duration-150 hover:bg-gray-100" aria-label="{{ t('pagination.goto_page', ['page' => $page]) }}">
                                        {{ $page }}
                                    </a>
                                @endif
                            @endforeach
                        @endif
                    @endforeach

                @endif

                {{-- Next page --}}
                @if($isAlpine)

                    <button
                        type="button"
                        @click="nextPage(); window.scrollTo({top:0, behavior:'smooth'})"
                        :disabled="currentPage === totalPages"
                        :class="currentPage === totalPages
                            ? 'text-gray-500 cursor-not-allowed'
                            : 'text-gray-500 hover:text-gray-400 active:bg-gray-100 active:text-gray-500'"
                        class="inline-flex items-center px-2 py-2 -ml-px text-sm font-medium text-gray-500 border border-gray-300 rounded-r-md leading-5 transition ease-in-out duration-150"
                        :aria-label="(window.__paginationStrings || {}).next || 'Next'"
                    >
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg>
                    </button>

                @else

                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="inline-flex items-center px-2 py-2 -ml-px text-sm font-medium text-gray-500 border border-gray-300 rounded-r-md leading-5 hover:text-gray-400 focus:outline-none focus:ring ring-gray-300 focus:border-blue-300 active:bg-gray-100 active:text-gray-500 transition ease-in-out duration-150" aria-label="{{ t('pagination.next') }}">
                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg>
                        </a>
                    @else
                        <span aria-disabled="true" aria-label="{{ t('pagination.next') }}">
                            <span class="inline-flex items-center px-2 py-2 -ml-px text-sm font-medium text-gray-500 border border-gray-300 cursor-not-allowed rounded-r-md leading-5" aria-hidden="true">
                                <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg>
                            </span>
                        </span>
                    @endif

                @endif

            </span>
        </div>

    </div>

</nav>
@endif
