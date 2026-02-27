@props([
    'href',
    'tier',
    'title',
    'description',
    'bullets' => [],
    'attach' => null,
    'button',
])

{{--
    Request service card used in the REQUEST SECTION of the custom page.

    **Required props:**
      - `href`        – URL the card links to (tickets.create with category).
      - `tier`        – Small badge text above the title (e.g. "R&D + Preparation + Print").
      - `title`       – Card heading (e.g. "I have an idea").
      - `description` – Body text describing the service.
      - `button`      – CTA button label text.

    **Optional props:**
      - `bullets`     – Array of strings shown as a bullet list.
      - `attach`      – Small helper text for attachments (shown below bullets).
--}}
<a href="{{ $href }}"
   class="bg-white rounded-xl shadow-md flex flex-col overflow-hidden hover:shadow-xl hover:-translate-y-1 transition-all duration-200 group">
    <div class="bg-dark px-6 py-5">
        <span class="text-xs font-bold uppercase tracking-widest text-accent-secondary">{{ $tier }}</span>
        <h3 class="text-2xl font-bold text-white mt-1">{{ $title }}</h3>
    </div>
    <div class="px-6 py-5 flex flex-col flex-1">
        <p class="text-grey-dark text-sm flex-1">{{ $description }}</p>

        @if (!empty($bullets))
            <ul class="mt-4 mb-5 space-y-1 text-sm text-grey-dark">
                @foreach ($bullets as $bullet)
                    <li>✓ {{ $bullet }}</li>
                @endforeach
            </ul>
        @endif

        @if ($attach)
            <p class="text-xs text-grey-dark/70 mb-5">{{ $attach }}</p>
        @endif

        <span class="mt-auto inline-flex items-center justify-center gap-2 bg-primary group-hover:bg-primary/90 text-white px-6 py-3 rounded-full uppercase font-semibold text-sm transition-colors text-center">
            {{ $button }}
        </span>
    </div>
</a>
