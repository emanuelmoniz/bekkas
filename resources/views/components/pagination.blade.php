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
    $elements = $elements ?? ($paginator ? $paginator->elements() : []);
    $linkMobile = 'text-sm text-accent-primary hover:text-accent-primary/90 no-underline';
    $disabledMobile = 'text-sm text-grey-medium no-underline';
    $linkDesktop = 'text-sm text-accent-primary hover:text-accent-primary/90 no-underline';
    $disabledDesktop = 'text-sm text-grey-medium no-underline';
    $pageLink = 'text-sm text-accent-primary hover:text-accent-primary/90 no-underline';
    $pageCurrent = 'text-sm text-grey-medium no-underline';
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
    <div class="flex gap-2 justify-between sm:hidden">

        @if($isAlpine)

            <a
                href="#"
                @click.prevent="prevPage(); window.scrollTo({top:0, behavior:'smooth'})"
                :class="'flex items-start ' + (currentPage === 1 ? '{{ $disabledMobile }} pointer-events-none' : '{{ $linkMobile }}')"
                :aria-label="(window.__paginationStrings || {}).previous || 'Previous'"
            >
                <svg class="w-4 h-4 inline-block me-2" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M12.707 15.707a1 1 0 01-1.414 0L6.586 11l4.707-4.707a1 1 0 011.414 1.414L9.414 11l3.293 3.293a1 1 0 010 1.414z" clip-rule="evenodd"/></svg>
                <span x-text="(window.__paginationStrings || {}).previous || 'Previous'"></span>
            </a>

            <a
                href="#"
                @click.prevent="nextPage(); window.scrollTo({top:0, behavior:'smooth'})"
                :class="'flex items-center ' + (currentPage === totalPages ? '{{ $disabledMobile }} pointer-events-none' : '{{ $linkMobile }}')"
                :aria-label="(window.__paginationStrings || {}).next || 'Next'"
            >
                <span x-text="(window.__paginationStrings || {}).next || 'Next'"></span>
                <svg class="w-4 h-4 inline-block ms-2" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M7.293 4.293a1 1 0 011.414 0L13.414 9l-4.707 4.707a1 1 0 01-1.414-1.414L10.586 9 7.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
            </a>

        @else

            @if ($paginator->onFirstPage())
                <span class="flex items-start {{ $disabledMobile }}" aria-disabled="true" aria-label="{{ t('pagination.previous') }}">
                    <svg class="w-4 h-4 inline-block me-2" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M12.707 15.707a1 1 0 01-1.414 0L6.586 11l4.707-4.707a1 1 0 011.414 1.414L9.414 11l3.293 3.293a1 1 0 010 1.414z" clip-rule="evenodd"/></svg>
                    {{ t('pagination.previous') }}
                </span>
            @else
                <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="flex items-start {{ $linkMobile }}" aria-label="{{ t('pagination.previous') }}">
                    <svg class="w-4 h-4 inline-block me-2" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M12.707 15.707a1 1 0 01-1.414 0L6.586 11l4.707-4.707a1 1 0 011.414 1.414L9.414 11l3.293 3.293a1 1 0 010 1.414z" clip-rule="evenodd"/></svg>
                    {{ t('pagination.previous') }}
                </a>
            @endif

            @if ($paginator->hasMorePages())
                <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="flex items-center {{ $linkMobile }}" aria-label="{{ t('pagination.next') }}">
                    {{ t('pagination.next') }}
                    <svg class="w-4 h-4 inline-block ms-2" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M7.293 4.293a1 1 0 011.414 0L13.414 9l-4.707 4.707a1 1 0 01-1.414-1.414L10.586 9 7.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
                </a>
            @else
                <span class="flex items-center {{ $disabledMobile }}" aria-disabled="true" aria-label="{{ t('pagination.next') }}">
                    {{ t('pagination.next') }}
                    <svg class="w-4 h-4 inline-block ms-2" fill="currentColor" viewBox="0 0 20 20" aria-hidden="true"><path fill-rule="evenodd" d="M7.293 4.293a1 1 0 011.414 0L13.414 9l-4.707 4.707a1 1 0 01-1.414-1.414L10.586 9 7.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/></svg>
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
            <span class="flex space-x-4">

                {{-- Previous page --}}
                @if($isAlpine)

                    <a
                        href="#"
                        @click.prevent="prevPage(); window.scrollTo({top:0, behavior:'smooth'})"
                        :class="currentPage === 1 ? '{{ $disabledDesktop }} pointer-events-none' : '{{ $linkDesktop }}'"
                        :aria-label="(window.__paginationStrings || {}).previous || 'Previous'"
                    >
                        <svg class="w-5 h-5 inline-block" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                    </a>

                @else

                    @if ($paginator->onFirstPage())
                        <span class="{{ $disabledDesktop }}" aria-disabled="true" aria-label="{{ t('pagination.previous') }}">
                            <svg class="w-5 h-5 inline-block" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                        </span>
                    @else
                        <a href="{{ $paginator->previousPageUrl() }}" rel="prev" class="{{ $linkDesktop }}" aria-label="{{ t('pagination.previous') }}">
                            <svg class="w-5 h-5 inline-block" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M12.707 5.293a1 1 0 010 1.414L9.414 10l3.293 3.293a1 1 0 01-1.414 1.414l-4-4a1 1 0 010-1.414l4-4a1 1 0 011.414 0z" clip-rule="evenodd" /></svg>
                        </a>
                    @endif

                @endif

                {{-- Page numbers / ellipsis --}}
                @if($isAlpine)
                    <template x-for="(item, i) in pageNumbers" :key="i">
                        <span class="flex items-end">
                            <span
                                x-show="item === '...'"
                                class="{{ $pageCurrent }}"
                                aria-hidden="true"
                            >…</span>
                            <a
                                x-show="item !== '...'"
                                href="#"
                                @click.prevent="goToPage(item); window.scrollTo({top:0, behavior:'smooth'})"
                                :class="item === currentPage ? '{{ $pageCurrent }} pointer-events-none' : '{{ $pageLink }}'"
                                :aria-current="item === currentPage ? 'page' : null"
                                x-text="item"
                            ></a>
                        </span>
                    </template>
                @else
                    <span class="flex items-end space-x-4"> 
                        @foreach ($elements as $element)
                                @if (is_string($element))
                                    <span class="{{ $pageCurrent }}" aria-disabled="true">{{ $element }}</span>
                                @endif
                                @if (is_array($element))
                                    @foreach ($element as $page => $url)
                                        @if ($page == $paginator->currentPage())
                                            <span class="{{ $pageCurrent }}" aria-current="page">{{ $page }}</span>
                                        @else
                                            <a href="{{ $url }}" class="{{ $pageLink }}" aria-label="{{ t('pagination.goto_page', ['page' => $page]) }}">
                                                {{ $page }}
                                            </a>
                                        @endif
                                    @endforeach
                                @endif
                        @endforeach
                    </span>
                @endif

                {{-- Next page --}}
                @if($isAlpine)

                    <a
                        href="#"
                        @click.prevent="nextPage(); window.scrollTo({top:0, behavior:'smooth'})"
                        :class="currentPage === totalPages ? '{{ $disabledDesktop }} pointer-events-none' : '{{ $linkDesktop }}'"
                        :aria-label="(window.__paginationStrings || {}).next || 'Next'"
                    >
                        <svg class="w-5 h-5 inline-block" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg>
                    </a>

                @else

                    @if ($paginator->hasMorePages())
                        <a href="{{ $paginator->nextPageUrl() }}" rel="next" class="{{ $linkDesktop }}" aria-label="{{ t('pagination.next') }}">
                            <svg class="w-5 h-5 inline-block" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg>
                        </a>
                    @else
                        <span class="{{ $disabledDesktop }}" aria-disabled="true" aria-label="{{ t('pagination.next') }}">
                            <svg class="w-5 h-5 inline-block" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg>
                    </span>
                    @endif

                @endif

            </span>
        </div>

    </div>

</nav>
@endif
