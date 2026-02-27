@props([
    'project',
    'animated'       => false,
    'scrollerConfig' => [],
])

{{--
    Reusable project card component.

    **Required props:**
      - `project`        – App\Models\Project instance (with `photos`
                           and `translations` relations already loaded).

    **Optional props:**
      - `animated`       – boolean; when true the `anim-item` class is added
                           so the card participates in the entrance animation.
      - `scrollerConfig` – array of image-scroller config overrides.
                           Accepted keys: `interval`, `autoplay`,
                           `autoplay_desktop`, `autoplay_mobile`.
                           Card defaults: interval=2500, autoplay_mobile=true,
                           autoplay_desktop=false.

    **Design:**
      - Square card (aspect-square).
      - Image slider fills the full card.
      - A persistent gradient + name bar always sits at the card bottom.
      - On hover, a details panel (client → time) expands below the name
        using a CSS grid-rows transition — no translate, no off-canvas elements.
      - The entire card links to the project show page.

    **Example:**
      ```blade
      <x-project-card
          :project="$project"
          :animated="true"
      />
      ```
--}}

@php
    $resolvedScrollerConfig = array_merge([
        'interval'         => 2500,
        'autoplay_mobile'  => true,
        'autoplay_desktop' => false,
    ], $scrollerConfig);

    $scrollerImages = $project->photos
        ->sortByDesc('is_primary')
        ->map(fn ($p) => asset('storage/' . $p->path));

    $name = optional($project->translation())->name ?? $project->client ?? '—';

    // TODO: replace '#' with route('portfolio.show', $project) once the
    // public project show page is implemented.
    $showUrl = route('portfolio.show', $project);
@endphp

{{--
    The outer <a> is the entire card link.
    `group` enables child `group-hover:*` utilities.

    Overlay strategy — NO translate tricks:
      • A persistent gradient + name bar is always visible at the card bottom.
      • A details block (client → time) sits below the name inside a
        CSS grid-rows wrapper: 0fr by default → 1fr on group-hover.
        This expands cleanly within the card bounds; no off-canvas elements,
        no pointer-event leakage.
--}}
<a href="{{ $showUrl }}"
   {{ $attributes->merge([
       'class' => 'group relative block aspect-square overflow-hidden rounded-lg shadow-md hover:shadow-lg transition-shadow duration-300' . ($animated ? ' anim-item' : ''),
   ]) }}>

    {{-- ── Image scroller / fallback placeholder ────────────────────── --}}
    @if($scrollerImages->isNotEmpty())
        <x-image-scroller
            class="w-full h-full"
            :images="$scrollerImages"
            :config="$resolvedScrollerConfig"
        />
    @else
        <div class="absolute inset-0 bg-grey-light flex items-center justify-center">
            <span class="text-grey-medium text-sm">
                {{ t('projects.card.no_photo') ?: 'No photo' }}
            </span>
        </div>
    @endif

    {{--
        ── Info panel ──────────────────────────────────────────────────
        Always anchored to the card bottom.
        Structure:
          1. Name           — always visible (the persistent "peek" strip).
          2. Details grid   — 0fr → 1fr on hover; client row first, time row second.
        The gradient behind the panel fades from opaque (bottom) to transparent
        (top) and is also taller on hover via max-height transition so it covers
        the revealed details area.
    --}}
    <div class="absolute inset-x-0 bottom-0 pointer-events-none">

        {{-- Gradient: persistent strip + expands taller on hover --}}
        <div class="bg-gradient-to-t from-black/85 via-black/60 to-transparent
                    transition-[padding] duration-300 ease-in-out
                    px-4 pb-3 pt-10
                    group-hover:pt-14">

            {{-- ── Project name — always visible ─────────────────────── --}}
            <h3 class="text-white font-semibold text-base leading-snug line-clamp-1 mb-0">
                {{ $name }}
            </h3>

            {{--
                ── Details — hidden by default, expand on hover ─────────
                grid-rows trick: height animates from 0 → auto via grid.
                pointer-events-auto re-enables clicks on the client link.
            --}}
            <div class="grid grid-rows-[0fr] group-hover:grid-rows-[1fr]
                        transition-[grid-template-rows] duration-300 ease-in-out">
                <div class="overflow-hidden">
                    <div class="flex flex-col gap-1.5 text-sm text-white/90 pt-2 pointer-events-auto">

                        {{-- Client row (first → visually directly below name) --}}
                        @if($project->client)
                            <div class="flex items-center gap-2">
                                {{-- User / client icon --}}
                                <svg xmlns="http://www.w3.org/2000/svg"
                                     class="h-4 w-4 shrink-0 opacity-80"
                                     fill="none" viewBox="0 0 24 24"
                                     stroke="currentColor" stroke-width="2"
                                     aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                                </svg>
                                @if($project->client_url)
                                    {{--
                                        NOTE: Cannot use <a> here — the card root is already <a>,
                                        and nested <a> tags are invalid HTML; browsers auto-close
                                        the outer one, ejecting the absolute overlay from the card.
                                        Use a <span role="link"> + onclick instead.
                                    --}}
                                    <span role="link" tabindex="0"
                                          class="underline underline-offset-2 hover:text-white truncate cursor-pointer"
                                          onclick="event.stopPropagation(); window.open('{{ $project->client_url }}', '_blank', 'noopener,noreferrer')"
                                          onkeydown="if(event.key==='Enter'||event.key===' '){event.stopPropagation();window.open('{{ $project->client_url }}','_blank','noopener,noreferrer')}">
                                        {{ $project->client }}
                                    </span>
                                @else
                                    <span class="truncate">{{ $project->client }}</span>
                                @endif
                            </div>
                        @endif

                        {{-- Execution time row (below client) --}}
                        @if($project->execution_time)
                            <div class="flex items-center gap-2">
                                {{-- Clock icon --}}
                                <svg xmlns="http://www.w3.org/2000/svg"
                                     class="h-4 w-4 shrink-0 opacity-80"
                                     fill="none" viewBox="0 0 24 24"
                                     stroke="currentColor" stroke-width="2"
                                     aria-hidden="true">
                                    <path stroke-linecap="round" stroke-linejoin="round"
                                          d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <span>{{ number_format((float) $project->execution_time, 0) }}h</span>
                            </div>
                        @endif

                    </div>
                </div>
            </div>

        </div>
    </div>

</a>
