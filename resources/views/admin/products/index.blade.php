<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-grey-dark leading-tight">
            Products
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- ACTION BAR --}}
            <div class="mb-4 flex justify-end">
                <a href="{{ route('admin.products.create') }}"
                   class="inline-flex items-center bg-accent-primary hover:bg-accent-primary/90 text-light font-semibold px-4 py-2 rounded">
                    New Product
                </a>
            </div>

            {{-- FILTERS --}}
            <form method="GET" class="mb-6 bg-light p-4 rounded shadow">
                <div class="grid grid-cols-1 md:grid-cols-7 gap-4">

                    {{-- NAME --}}
                    <input type="text"
                           name="name"
                           value="{{ request('name') }}"
                           placeholder="Name"
                           class="border rounded px-3 py-2">

                    {{-- STOCK (at least X) --}}
                    <input type="number"
                           name="stock"
                           value="{{ request('stock') }}"
                           placeholder="Stock ≥"
                           min="0"
                           class="border rounded px-3 py-2">

                    {{-- CATEGORY --}}
                    <div x-data="{ open:false, search:'', selected:'{{ request('category_id') }}' }" class="relative">
                        <input type="hidden" name="category_id" :value="selected">
                        <button type="button" @click="open=!open"
                                class="w-full border rounded px-3 py-2 text-left">
                            {{ optional($categories->firstWhere('id', request('category_id'))?->translation())->name ?? 'Category' }}
                        </button>
                        <div x-show="open" @click.outside="open=false"
                             class="absolute z-10 w-full bg-light border rounded shadow mt-1">
                            <input x-model="search" class="w-full px-3 py-2 border-b" placeholder="Search...">
                            @foreach($categories as $category)
                                @php $name = optional($category->translation())->name; @endphp
                                <div x-show="'{{ strtolower($name) }}'.includes(search.toLowerCase())"
                                     @click="selected='{{ $category->id }}'; open=false"
                                     class="px-3 py-2 hover:bg-grey-light cursor-pointer">
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
                             class="absolute z-10 w-full bg-light border rounded shadow mt-1">
                            <input x-model="search" class="w-full px-3 py-2 border-b" placeholder="Search...">
                            @foreach($materials as $material)
                                @php $name = optional($material->translation())->name; @endphp
                                <div x-show="'{{ strtolower($name) }}'.includes(search.toLowerCase())"
                                     @click="selected='{{ $material->id }}'; open=false"
                                     class="px-3 py-2 hover:bg-grey-light cursor-pointer">
                                    {{ $name }}
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- FLAGS --}}
                    @foreach (['is_featured'=>'Featured','is_promo'=>'Promo','active'=>'Active'] as $key=>$label)
                        <select name="{{ $key }}" class="border rounded px-3 py-2">
                            <option value="">{{ $label }}</option>
                            <option value="1" @selected(request($key)==='1')>Yes</option>
                            <option value="0" @selected(request($key)==='0')>No</option>
                        </select>
                    @endforeach

                    {{-- ACTIONS --}}
                    <div class="flex gap-2">
                        <a href="{{ route('admin.products.index') }}"
                           class="bg-grey-medium hover:bg-grey-dark text-light px-4 py-2 rounded">
                            Reset
                        </a>
                        <button class="bg-accent-primary hover:bg-accent-primary/90 text-light px-4 py-2 rounded">
                            Filter
                        </button>
                    </div>
                </div>
            </form>

            {{-- TABLE (UNCHANGED) --}}
            <div class="bg-light shadow rounded">
                <table class="min-w-full border">
                    <thead class="bg-grey-light">
                        <tr>
                            <th class="px-4 py-2 text-left">Name</th>
                            <th class="px-4 py-2 text-left">Price</th>
                            <th class="px-4 py-2 text-left">Stock</th>
                            <th class="px-4 py-2 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($products as $product)
                            <tr class="border-t">
                                <td class="px-4 py-2">
                                    {{ optional($product->translation())->name }}
                                    <x-missing-locale-badge :model="$product" />
                                </td>
                                <td class="px-4 py-2">
                                    {{ number_format($product->price, 2) }}
                                </td>
                                <td class="px-4 py-2">
                                    {{ $product->stock }}
                                </td>
                                <td class="px-4 py-2 text-right space-x-2">
                                    <a href="{{ route('admin.products.edit', $product) }}"
                                       class="inline-flex bg-accent-primary text-light px-3 py-1 rounded text-sm">
                                        Edit
                                    </a>
                                    <form method="POST"
                                          action="{{ route('admin.products.destroy', $product) }}"
                                          class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button onclick="return confirm('Delete this product?')"
                                                class="bg-grey-light text-grey-dark px-3 py-1 rounded text-sm">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-4 py-6 text-center text-grey-medium">
                                    No products found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $products->links() }}
            </div>

        </div>
    </div>
</x-app-layout>
