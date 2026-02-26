@props(['images' => []])

@php
/**
 * Agnostic Image Gallery component.
 *
 * Usage:
 *   <x-image-gallery :images="$images" />
 *
 * Each element in $images should be an array (or object) with:
 *   url      – thumbnail / display URL  (required)
 *   original – full-resolution URL       (optional; falls back to `url` in lightbox)
 *
 * Features:
 *   - Main "selected" photo (square) with prev/next arrows when >1 image
 *   - Thumbnail strip (max 4 visible) with navigation buttons when >4 images
 *   - Click main photo → full-screen lightbox with original resolution image
 *   - Lightbox: zoom via mouse wheel / zoom buttons, pan via drag / touch
 *   - Pinch-to-zoom on mobile
 *   - Keyboard: Escape closes, ← → navigates
 */

$imageData = collect($images)
    ->map(fn ($img) => [
        'url'      => is_array($img) ? ($img['url']      ?? '') : ($img->url      ?? ''),
        'original' => is_array($img) ? ($img['original'] ?? null) : ($img->original ?? null),
    ])
    ->values()
    ->toArray();
@endphp

<div
    x-data="imageGallery({{ json_encode($imageData) }})"
    x-init="init()"
    @keydown.escape.window="lightboxOpen && closeLightbox()"
    @keydown.arrow-left.window="lightboxOpen && prevImage()"
    @keydown.arrow-right.window="lightboxOpen && nextImage()"
    {{ $attributes }}
>

    {{-- ── EMPTY STATE ─────────────────────────────────────────────────── --}}
    @if (empty($imageData))
        <div class="aspect-square w-full bg-grey-light flex items-center justify-center rounded">
            <span class="text-grey-medium text-sm">{{ t('store.no_photos') ?: 'No photos available' }}</span>
        </div>
    @else

    {{-- ── SELECTED PHOTO ──────────────────────────────────────────────── --}}
    <div class="relative aspect-square w-full overflow-hidden rounded bg-grey-light group">

        {{-- Main image --}}
        <img
            :src="current.url"
            :alt="'Image ' + (selectedIndex + 1)"
            class="w-full h-full object-cover cursor-zoom-in select-none"
            draggable="false"
            @click="openLightbox()"
        />

        {{-- Gradient overlays so buttons are always legible --}}
        <div class="pointer-events-none absolute inset-y-0 left-0 w-12 bg-gradient-to-r from-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
        <div class="pointer-events-none absolute inset-y-0 right-0 w-12 bg-gradient-to-l from-black/20 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>

        {{-- Prev button --}}
        <template x-if="images.length > 1">
            <button
                @click.stop="prevImage()"
                :disabled="!canPrev"
                class="absolute left-2 top-1/2 -translate-y-1/2 w-8 h-8 flex items-center justify-center rounded-full bg-white/80 hover:bg-white text-grey-dark shadow transition opacity-0 group-hover:opacity-100 disabled:group-hover:opacity-30 disabled:cursor-not-allowed"
                aria-label="{{ t('gallery.prev_image') ?: 'Previous image' }}"
            >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/>
                </svg>
            </button>
        </template>

        {{-- Next button --}}
        <template x-if="images.length > 1">
            <button
                @click.stop="nextImage()"
                :disabled="!canNext"
                class="absolute right-2 top-1/2 -translate-y-1/2 w-8 h-8 flex items-center justify-center rounded-full bg-white/80 hover:bg-white text-grey-dark shadow transition opacity-0 group-hover:opacity-100 disabled:group-hover:opacity-30 disabled:cursor-not-allowed"
                aria-label="{{ t('gallery.next_image') ?: 'Next image' }}"
            >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
                </svg>
            </button>
        </template>

        {{-- Zoom hint icon (bottom-right) --}}
        <div class="pointer-events-none absolute bottom-2 right-2 opacity-0 group-hover:opacity-70 transition-opacity">
            <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-5 h-5 text-white drop-shadow">
                <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803M10.5 7.5v3m0 0v3m0-3h3m-3 0H7.5"/>
            </svg>
        </div>

    </div>

    {{-- ── THUMBNAILS ───────────────────────────────────────────────────── --}}
    <template x-if="images.length > 1">
        <div class="relative mt-2 group">

            {{-- 4-up thumbnail grid --}}
            <div class="grid grid-cols-4 gap-1.5">
                <template x-for="(img, i) in visibleThumbs" :key="thumbOffset + i">
                    <button
                        @click="selectImage(thumbOffset + i)"
                        class="aspect-square overflow-hidden rounded transition focus:outline-none focus:ring-2 focus:ring-primary"
                        :class="selectedIndex === thumbOffset + i
                            ? 'ring-2 ring-accent-primary opacity-100'
                            : 'opacity-60 hover:opacity-100'"
                        :aria-label="'{{ t('gallery.view_image') ?: 'View image' }} ' + (thumbOffset + i + 1)"
                        :aria-pressed="selectedIndex === thumbOffset + i"
                    >
                        <img
                            :src="img.url"
                            :alt="'Thumbnail ' + (thumbOffset + i + 1)"
                            class="w-full h-full object-cover select-none"
                            draggable="false"
                        />
                    </button>
                </template>
            </div>

            {{-- Thumb Prev overlapping --}}
            <button
                @click="thumbPrev()"
                :disabled="!canThumbPrev"
                class="absolute left-2 top-1/2 -translate-y-1/2 w-8 h-8 flex items-center justify-center rounded-full bg-white/80 hover:bg-white text-grey-dark shadow transition opacity-0 group-hover:opacity-100 disabled:group-hover:opacity-30 disabled:cursor-not-allowed z-10"
                aria-label="{{ t('gallery.prev_thumbnails') ?: 'Previous thumbnails' }}"
            >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/>
                </svg>
            </button>

            {{-- Thumb Next overlapping --}}
            <button
                @click="thumbNext()"
                :disabled="!canThumbNext"
                class="absolute right-2 top-1/2 -translate-y-1/2 w-8 h-8 flex items-center justify-center rounded-full bg-white/80 hover:bg-white text-grey-dark shadow transition opacity-0 group-hover:opacity-100 disabled:group-hover:opacity-30 disabled:cursor-not-allowed z-10"
                aria-label="{{ t('gallery.next_thumbnails') ?: 'Next thumbnails' }}"
            >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-4 h-4">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
                </svg>
            </button>

        </div>
    </template>

    {{-- ── LIGHTBOX ─────────────────────────────────────────────────────── --}}
    {{-- x-teleport moves the overlay to <body> so CSS transforms on parent --}}
    {{-- grid columns / animated wrappers don't clip or reposition position:fixed --}}
    <template x-teleport="body">
    <div
        x-show="lightboxOpen"
        x-transition:enter="transition ease-out duration-200"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="transition ease-in duration-150"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        @click.self="closeLightbox()"
        class="fixed inset-0 z-50 flex items-center justify-center bg-black/90"
        role="dialog"
        aria-modal="true"
        aria-label="{{ t('gallery.lightbox') ?: 'Image viewer' }}"
        style="display:none;"
    >

        {{-- Top-right controls --}}
        <div class="absolute top-3 right-3 flex items-center gap-1.5 z-20">

            {{-- Zoom in --}}
            <button
                @click.stop="zoomIn()"
                class="w-9 h-9 flex items-center justify-center rounded-full bg-white/10 hover:bg-white/25 text-white transition"
                aria-label="{{ t('gallery.zoom_in') ?: 'Zoom in' }}"
            >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803M10.5 7.5v3m0 0v3m0-3h3m-3 0H7.5"/>
                </svg>
            </button>

            {{-- Zoom out --}}
            <button
                @click.stop="zoomOut()"
                class="w-9 h-9 flex items-center justify-center rounded-full bg-white/10 hover:bg-white/25 text-white transition"
                aria-label="{{ t('gallery.zoom_out') ?: 'Zoom out' }}"
            >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M21 21l-5.197-5.197m0 0A7.5 7.5 0 105.196 15.803M10.5 10.5h3m-3 0H7.5"/>
                </svg>
            </button>

            {{-- Reset zoom --}}
            <button
                @click.stop="zoomReset()"
                class="w-9 h-9 flex items-center justify-center rounded-full bg-white/10 hover:bg-white/25 text-white transition"
                aria-label="{{ t('gallery.zoom_reset') ?: 'Reset zoom' }}"
            >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 9V4.5M9 9H4.5M9 9L3.75 3.75M9 15v4.5M9 15H4.5M9 15l-5.25 5.25M15 9h4.5M15 9V4.5M15 9l5.25-5.25M15 15h4.5M15 15v4.5m0-4.5l5.25 5.25"/>
                </svg>
            </button>

            {{-- Close --}}
            <button
                @click.stop="closeLightbox()"
                class="w-9 h-9 flex items-center justify-center rounded-full bg-white/10 hover:bg-white/25 text-white transition"
                aria-label="{{ t('gallery.close') ?: 'Close' }}"
            >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/>
                </svg>
            </button>

        </div>

        {{-- Prev in lightbox --}}
        <template x-if="images.length > 1">
            <button
                @click.stop="prevImage()"
                :disabled="!canPrev"
                class="absolute left-3 top-1/2 -translate-y-1/2 z-20 w-10 h-10 flex items-center justify-center rounded-full bg-white/10 hover:bg-white/25 text-white transition disabled:opacity-20 disabled:cursor-not-allowed"
                aria-label="{{ t('gallery.prev_image') ?: 'Previous image' }}"
            >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M15.75 19.5L8.25 12l7.5-7.5"/>
                </svg>
            </button>
        </template>

        {{-- Next in lightbox --}}
        <template x-if="images.length > 1">
            <button
                @click.stop="nextImage()"
                :disabled="!canNext"
                class="absolute right-3 top-1/2 -translate-y-1/2 z-20 w-10 h-10 flex items-center justify-center rounded-full bg-white/10 hover:bg-white/25 text-white transition disabled:opacity-20 disabled:cursor-not-allowed"
                aria-label="{{ t('gallery.next_image') ?: 'Next image' }}"
            >
                <svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="2.5" stroke="currentColor" class="w-5 h-5">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M8.25 4.5l7.5 7.5-7.5 7.5"/>
                </svg>
            </button>
        </template>

        {{-- Pan-zoom container: fills the overlay but yields to the edge buttons --}}
        <div
            class="absolute inset-0 flex items-center justify-center overflow-hidden"
            data-gallery-lightbox-container
            x-ref="lightboxContainer"
            style="z-index: 10;"
        >
            <img
                :src="currentOriginal"
                :alt="'Image ' + (selectedIndex + 1)"
                class="max-w-full max-h-full select-none"
                style="touch-action: none; transform-origin: center center;"
                draggable="false"
                data-gallery-lightbox-img
                x-ref="lightboxImg"
            />
        </div>

        {{-- Counter (bottom-center) --}}
        <div class="absolute bottom-4 left-1/2 -translate-x-1/2 z-20 text-white/60 text-sm tabular-nums pointer-events-none">
            <span x-text="selectedIndex + 1"></span>&thinsp;/&thinsp;<span x-text="images.length"></span>
        </div>

    </div>
    </template>{{-- /x-teleport --}}

    @endif {{-- /empty --}}
</div>
