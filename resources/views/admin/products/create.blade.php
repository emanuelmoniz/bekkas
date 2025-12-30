<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Create Product
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <form method="POST" action="{{ route('admin.products.store') }}">
                @csrf

                {{-- TRANSLATIONS --}}
                <div class="bg-white p-6 rounded shadow mb-6">
                    <h3 class="font-semibold mb-4">Translations</h3>

                    @foreach (['pt-PT' => 'Português', 'en-UK' => 'English'] as $locale => $label)
                        <div class="border p-4 mb-4">
                            <h4 class="font-medium mb-2">{{ $label }}</h4>

                            <div class="mb-3">
                                <label class="block text-sm">Name</label>
                                <input type="text"
                                       name="name[{{ $locale }}]"
                                       class="w-full border rounded px-3 py-2"
                                       required>
                            </div>

                            <div>
                                <label class="block text-sm">Description</label>
                                <textarea name="description[{{ $locale }}]"
                                          class="w-full border rounded px-3 py-2"
                                          rows="4"></textarea>
                            </div>
                        </div>
                    @endforeach
                </div>

                {{-- PRICING --}}
                <div class="bg-white p-6 rounded shadow mb-6">
                    <h3 class="font-semibold mb-4">Pricing</h3>

                    <div class="grid grid-cols-3 gap-4">
                        <input name="price" type="number" step="0.01" placeholder="Price" class="border rounded px-3 py-2" required>
                        <input name="promo_price" type="number" step="0.01" placeholder="Promo Price" class="border rounded px-3 py-2">
                        <input name="tax" type="number" step="0.01" placeholder="Tax (%)" class="border rounded px-3 py-2">
                    </div>
                </div>

                {{-- STOCK --}}
                <div class="bg-white p-6 rounded shadow mb-6">
                    <h3 class="font-semibold mb-4">Stock</h3>
                    <input name="stock" type="number" class="border rounded px-3 py-2" value="0">
                </div>

                {{-- CATEGORIES --}}
                <div class="bg-white p-6 rounded shadow mb-6">
                    <h3 class="font-semibold mb-4">Categories</h3>

                    @foreach ($categories as $category)
                        <label class="block">
                            <input type="checkbox" name="categories[]" value="{{ $category->id }}">
                            {{ optional($category->translation())->name }}
                        </label>
                    @endforeach
                </div>

                {{-- MATERIALS --}}
                <div class="bg-white p-6 rounded shadow mb-6">
                    <h3 class="font-semibold mb-4">Materials</h3>

                    @foreach ($materials as $material)
                        <label class="block">
                            <input type="checkbox" name="materials[]" value="{{ $material->id }}">
                            {{ optional($material->translation())->name }}
                        </label>
                    @endforeach
                </div>

                {{-- FLAGS --}}
                <div class="bg-white p-6 rounded shadow mb-6">
                    <label class="block">
                        <input type="checkbox" name="is_new" value="1"> New
                    </label>
                    <label class="block">
                        <input type="checkbox" name="is_promo" value="1"> Promo
                    </label>
                    <label class="block">
                        <input type="checkbox" name="active" value="1" checked> Active
                    </label>
                </div>

                <button class="bg-green-600 text-white px-6 py-2 rounded">
                    Save Product
                </button>

            </form>

        </div>
    </div>
</x-app-layout>
