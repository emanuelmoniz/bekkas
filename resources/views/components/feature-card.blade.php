@props([
    'image',
    'title',
    'description',
])

{{--
    Generic feature card with a square image header.

    **Required props:**
      - `image`       – URL / asset path for the card image (square crop).
      - `title`       – Card heading text.
      - `description` – Card body text.
--}}
<div class="bg-white rounded-lg overflow-hidden shadow-lg hover:shadow-xl transition-shadow h-full flex flex-col">
    <img src="{{ $image }}" alt="{{ $title }}" class="w-full aspect-square object-cover">
    <div class="p-6 py-8 flex flex-col flex-grow items-center text-center">
        <h3 class="uppercase text-2xl font-bold mb-4 text-dark">{{ $title }}</h3>
        <p class="text-grey-dark flex-grow">{{ $description }}</p>
    </div>
</div>
