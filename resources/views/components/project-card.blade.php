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
      - Project name rests over the image at the bottom behind a gradient
        overlay for legibility—always visible.
      - On hover the overlay slides up to reveal production time and client.
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
    `isolate` prevents the inner stacking context from leaking outside.
--}}
<a href="{{ $showUrl }}"
   {{ $attributes->merge([
       'class' => 'group relative block aspect-square overflow-hidden rounded-lg shadow-md hover:shadow-xl transition-shadow duration-300 isolate' . ($animated ? ' anim-item' : ''),
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
        ── Overlay panel ───────────────────────────────────────────────
        Default state : shifted down so only the bottom strip (name row)
        is visible.  The strip height is roughly 3.5 rem
        (pb-4 ≈ 1 rem + name line-height ≈ 1.75 rem + a touch of gradient).

        Hover state   : translate-y-0 → the full panel slides up, revealing
        the production-time and client rows above the name.
    --}}
    <div class="absolute inset-x-0 bottom-0
                translate-y-[calc(100%-3.5rem)]
                group-hover:translate-y-0
                transition-transform duration-300 ease-in-out">

        {{-- Gradient background: opaque at bottom, fades out upward --}}
        <div class="bg-gradient-to-t from-black/80 via-black/55 to-transparent
                    px-4 pt-10 pb-4">

            {{-- ── Details (hidden initially, slide-revealed on hover) ── --}}
            <div class="mb-2 flex flex-col gap-1.5 text-sm text-white/90">

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
                            <a href="{{ $project->client_url }}"
                               target="_blank" rel="noopener noreferrer"
                               class="underline underline-offset-2 hover:text-white truncate"
                               onclick="event.stopPropagation()">
                                {{ $project->client }}
                            </a>
                        @else
                            <span class="truncate">{{ $project->client }}</span>
                        @endif
                    </div>
                @endif

            </div>

            {{-- ── Project name (always visible at card bottom) ───────── --}}
            <h3 class="text-white font-semibold text-base leading-snug line-clamp-1">
                {{ $name }}
            </h3>

        </div>
    </div>

</a>
