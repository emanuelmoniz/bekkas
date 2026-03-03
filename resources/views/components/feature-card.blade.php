@props([
    'image',
    'title',
    'description',
    'bullets' => [],
])

{{--
    Generic feature card with a square image header.

    **Required props:**
      - `image`       – URL / asset path for the card image (square crop).
      - `title`       – Card heading text.
      - `description` – Card body text.

    **Optional props:**
      - `bullets`     – Array of strings shown as a bullet list below the description.
--}}
<div class="bg-white rounded-lg overflow-hidden shadow-lg hover:shadow-xl transition-shadow h-full flex flex-col">
    <img src="{{ $image }}" alt="{{ $title }}" class="w-full aspect-square object-cover">
    <div class="p-6 py-8 flex flex-col flex-grow items-center text-center">
        <h3 class="uppercase text-2xl font-bold mb-4 text-dark">{{ $title }}</h3>
        <p class="text-grey-dark">{{ $description }}</p>
        @if (!empty($bullets))
            <ul class="mt-4 space-y-1 text-sm text-grey-dark w-full text-left">
                @foreach ($bullets as $bullet)
                    <li class="flex justify-center gap-2">
                        <!-- <span class="text-primary font-bold mt-0.5">&#10003;</span> -->
                        <span>{{ $bullet }}</span>
                    </li>
                @endforeach
            </ul>
        @endif
    </div>
</div>
