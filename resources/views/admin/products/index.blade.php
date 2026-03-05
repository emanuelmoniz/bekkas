<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-grey-dark leading-tight">
            Products
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">

        {{-- ACTION BAR --}}
        <div class="mb-4 flex justify-end">
            <x-default-button type="button" onclick="window.location.href='{{ route('admin.products.create') }}'">
                New Product
            </x-default-button>
        </div>

        {{-- FILTERS --}}
        <form method="GET" class="mb-6 bg-white p-4 rounded shadow">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-4 pb-4">

                {{-- NAME --}}
                <input type="text"
                       name="name"
                       value="{{ request('name') }}"
                       placeholder="Name"
                       class="border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">

                {{-- STOCK (at least X) --}}
                <input type="number"
                       name="stock"
                       value="{{ request('stock') }}"
                       placeholder="Stock ≥"
                       min="0"
                       class="border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">

                {{-- CATEGORY --}}
                <select name="category_id" class="border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">
                    <option value="">Category</option>
                    @foreach($categories as $category)
                        @php $name = optional($category->translation())->name; @endphp
                        <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>{{ $name }}</option>
                    @endforeach
                </select>

                {{-- MATERIAL --}}
                <select name="material_id" class="border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">
                    <option value="">Material</option>
                    @foreach($materials as $material)
                        @php $name = optional($material->translation())->name; @endphp
                        <option value="{{ $material->id }}" @selected(request('material_id') == $material->id)>{{ $name }}</option>
                    @endforeach
                </select>
            </div>
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
                {{-- FLAGS --}}
                @foreach (['is_featured'=>'Featured','is_promo'=>'Promo','active'=>'Active'] as $key=>$label)
                    <select name="{{ $key }}" class="border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">
                        <option value="">{{ $label }}</option>
                        <option value="1" @selected(request($key)==='1')>Yes</option>
                        <option value="0" @selected(request($key)==='0')>No</option>
                    </select>
                @endforeach

                {{-- ACTIONS --}}
                <div class="flex justify-end gap-2">
                    <x-default-button type="button" onclick="window.location.href='{{ route('admin.products.index') }}'">
                        Reset
                    </x-default-button>
                    <x-default-button type="submit">
                        Filter
                    </x-default-button>
                </div>
            </div>
        </form>

        {{-- TABLE (UNCHANGED) --}}
        <div class="bg-white shadow rounded">
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
                                <a href="{{ route('admin.products.show', $product) }}" class="font-medium text-accent-primary hover:text-accent-primary/90 no-underline">{{ optional($product->translation())->name }}</a>
                                <x-missing-locale-badge :model="$product" />
                            </td>
                            <td class="px-4 py-2">
                                {{ number_format($product->price, 2) }}
                            </td>
                            <td class="px-4 py-2">
                                {{ $product->stock }}
                            </td>
                            <td class="px-4 py-2 text-right space-x-2">
                                <x-default-button type="button" onclick="window.location.href='{{ route('admin.products.edit', $product) }}'">
                                    Edit
                                </x-default-button>
                                <form method="POST"
                                      action="{{ route('admin.products.destroy', $product) }}"
                                      class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <x-default-button type="submit" onclick="return confirm('Delete this product?')">
                                        Delete
                                    </x-default-button>
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
</x-app-layout>
