<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            {{ optional($product->translation())->name }}
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-5xl mx-auto sm:px-6 lg:px-8 grid grid-cols-1 md:grid-cols-2 gap-6">

            {{-- GALLERY --}}
            <div class="space-y-4">
                @forelse ($product->photos as $photo)
                    <img src="{{ asset('storage/' . $photo->path) }}"
                         class="w-full rounded shadow">
                @empty
                    <div class="bg-gray-200 h-64 flex items-center justify-center rounded">
                        <span class="text-gray-500">No photos available</span>
                    </div>
                @endforelse
            </div>

            {{-- DETAILS --}}
            <div class="bg-white p-6 rounded shadow space-y-4">

                {{-- PRICE --}}
                <div class="text-xl font-semibold">
                    €{{ number_format($product->promo_price ?? $product->price, 2) }}
                </div>

                {{-- DESCRIPTION --}}
                @if (optional($product->translation())->description)
                    <div class="text-gray-700">
                        {!! nl2br(e(optional($product->translation())->description)) !!}
                    </div>
                @endif

                {{-- WEIGHT --}}
                <div class="text-sm text-gray-600">
                    Weight: {{ $product->weight }} g
                </div>

                {{-- STOCK --}}
                <div class="text-sm">
                    Stock:
                    @if ($product->stock > 0)
                        <span class="text-green-600 font-medium">Available</span>
                    @else
                        <span class="text-red-600 font-medium">Out of stock</span>
                    @endif
                </div>

                {{-- CATEGORIES --}}
                @if ($product->categories->isNotEmpty())
                    <div>
                        <h4 class="font-medium text-sm text-gray-700 mb-1">
                            Categories
                        </h4>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($product->categories as $category)
                                <span class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded">
                                    {{ optional($category->translation())->name }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif

                {{-- MATERIALS --}}
                @if ($product->materials->isNotEmpty())
                    <div>
                        <h4 class="font-medium text-sm text-gray-700 mb-1">
                            Materials
                        </h4>
                        <div class="flex flex-wrap gap-2">
                            @foreach ($product->materials as $material)
                                <span class="bg-gray-100 text-gray-700 text-xs px-2 py-1 rounded">
                                    {{ optional($material->translation())->name }}
                                </span>
                            @endforeach
                        </div>
                    </div>
                @endif

            </div>
        </div>
    </div>
</x-app-layout>
