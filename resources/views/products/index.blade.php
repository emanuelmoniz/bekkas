<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            Products
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

            {{-- FILTERS --}}
            <form method="GET" class="bg-white p-4 rounded shadow">
                <div class="grid grid-cols-1 md:grid-cols-7 gap-4">

                    <input type="text"
                           name="name"
                           value="{{ request('name') }}"
                           placeholder="Name"
                           class="border rounded px-3 py-2">

                    {{-- CATEGORY --}}
                    <div x-data="{ open:false, search:'', selected:'{{ request('category_id') }}' }" class="relative">
                        <input type="hidden" name="category_id" :value="selected">
                        <button type="button" @click="open=!open"
                                class="w-full border rounded px-3 py-2 text-left">
                            {{ optional($categories->firstWhere('id', request('category_id'))?->translation())->name ?? 'Category' }}
                        </button>
                        <div x-show="open" @click.outside="open=false"
                             class="absolute z-10 w-full bg-white border rounded shadow mt-1">
                            <input x-model="search" class="w-full px-3 py-2 border-b" placeholder="Search...">
                            @foreach ($categories as $category)
                                @php $name = optional($category->translation())->name; @endphp
                                <div x-show="'{{ strtolower($name) }}'.includes(search.toLowerCase())"
                                     @click="selected='{{ $category->id }}'; open=false"
                                     class="px-3 py-2 hover:bg-gray-100 cursor-pointer">
                                    {{ $name }}
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- MATERIAL --}}
                    <div x-data="{ open:false, search:'', selected:'{{ request('material_id') }}' }" class="relative">
                        <input type="hidden" name="material_id" :value="selected">
                        <button type="button" @click="open=!open"
                                class="w-full border rounded px-3 py-2 text-left">
                            {{ optional($materials->firstWhere('id', request('material_id'))?->translation())->name ?? 'Material' }}
                        </button>
                        <div x-show="open" @click.outside="open=false"
                             class="absolute z-10 w-full bg-white border rounded shadow mt-1">
                            <input x-model="search" class="w-full px-3 py-2 border-b" placeholder="Search...">
                            @foreach ($materials as $material)
                                @php $name = optional($material->translation())->name; @endphp
                                <div x-show="'{{ strtolower($name) }}'.includes(search.toLowerCase())"
                                     @click="selected='{{ $material->id }}'; open=false"
                                     class="px-3 py-2 hover:bg-gray-100 cursor-pointer">
                                    {{ $name }}
                                </div>
                            @endforeach
                        </div>
                    </div>

                    <select name="is_new" class="border rounded px-3 py-2">
                        <option value="">New</option>
                        <option value="1" @selected(request('is_new')==='1')>Only New</option>
                        <option value="0" @selected(request('is_new')==='0')>Not New</option>
                    </select>

                    <select name="is_promo" class="border rounded px-3 py-2">
                        <option value="">Promo</option>
                        <option value="1" @selected(request('is_promo')==='1')>Only Promo</option>
                        <option value="0" @selected(request('is_promo')==='0')>Not Promo</option>
                    </select>

                    <label class="flex items-center gap-2">
                        <input type="checkbox" name="available" value="1" @checked(request('available'))>
                        In stock
                    </label>

                    <div class="flex gap-2">
                        <button class="bg-indigo-600 text-white px-4 py-2 rounded">
                            Filter
                        </button>
                        <a href="{{ route('products.index') }}" class="underline text-sm">
                            Reset
                        </a>
                    </div>
                </div>
            </form>

            {{-- PRODUCTS --}}
            <div class="grid grid-cols-1 sm:grid-cols-3 lg:grid-cols-4 gap-6">
                @forelse ($products as $product)
                    <a href="{{ route('products.show', $product) }}"
                       class="bg-white rounded shadow p-4 hover:shadow-lg transition">
                        <img src="{{ asset('storage/' . optional($product->primaryPhoto)->path) }}"
                             class="h-40 w-full object-cover rounded mb-3">
                        <div class="font-semibold">
                            {{ optional($product->translation())->name }}
                        </div>
                        <div class="text-sm text-gray-600">
                            €{{ number_format($product->promo_price ?? $product->price, 2) }}
                        </div>
                    </a>
                @empty
                    <p class="text-gray-600 col-span-full text-center">
                        No products found.
                    </p>
                @endforelse
            </div>

            {{ $products->links() }}
        </div>
    </div>
</x-app-layout>
