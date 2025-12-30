@php
    $isEdit = ($mode ?? 'create') === 'edit';
@endphp

<form method="POST"
      action="{{ $isEdit ? route('admin.products.update', $product) : route('admin.products.store') }}">
    @csrf
    @if ($isEdit)
        @method('PUT')
    @endif

    {{-- TRANSLATIONS --}}
    <div class="bg-white p-6 rounded shadow mb-6">
        <h3 class="font-semibold mb-4">Translations</h3>

        @foreach (['pt-PT' => 'Português', 'en-UK' => 'English'] as $locale => $label)
            @php
                $translation = $isEdit
                    ? $product->translations->firstWhere('locale', $locale)
                    : null;
            @endphp

            <div class="border p-4 mb-4">
                <h4 class="font-medium mb-2">{{ $label }}</h4>

                <div class="mb-3">
                    <label class="block text-sm mb-1">Name</label>
                    <input type="text"
                           name="name[{{ $locale }}]"
                           value="{{ $translation?->name }}"
                           class="w-full border rounded px-3 py-2"
                           required>
                </div>

                <div>
                    <label class="block text-sm mb-1">Description</label>
                    <textarea name="description[{{ $locale }}]"
                              class="w-full border rounded px-3 py-2"
                              rows="4">{{ $translation?->description }}</textarea>
                </div>
            </div>
        @endforeach
    </div>

    {{-- PRICING --}}
    <div class="bg-white p-6 rounded shadow mb-6">
        <h3 class="font-semibold mb-4">Pricing</h3>

        <div class="grid grid-cols-3 gap-4">
            <input type="number" step="0.01" name="price"
                   value="{{ $product->price ?? '' }}"
                   class="border rounded px-3 py-2"
                   placeholder="Price" required>

            <input type="number" step="0.01" name="promo_price"
                   value="{{ $product->promo_price ?? '' }}"
                   class="border rounded px-3 py-2"
                   placeholder="Promo Price">

            <input type="number" step="0.01" name="tax"
                   value="{{ $product->tax ?? '' }}"
                   class="border rounded px-3 py-2"
                   placeholder="Tax (%)">
        </div>
    </div>

    {{-- STOCK --}}
    <div class="bg-white p-6 rounded shadow mb-6">
        <h3 class="font-semibold mb-4">Stock</h3>

        <input type="number"
               name="stock"
               value="{{ $product->stock ?? 0 }}"
               class="border rounded px-3 py-2">
    </div>

    {{-- CATEGORIES --}}
    <div class="bg-white p-6 rounded shadow mb-6">
        <h3 class="font-semibold mb-4">Categories</h3>

        @foreach ($categories as $category)
            <label class="block">
                <input type="checkbox"
                       name="categories[]"
                       value="{{ $category->id }}"
                       @checked($isEdit && $product->categories->contains($category->id))>
                {{ optional($category->translation())->name }}
            </label>
        @endforeach
    </div>

    {{-- MATERIALS --}}
    <div class="bg-white p-6 rounded shadow mb-6">
        <h3 class="font-semibold mb-4">Materials</h3>

        @foreach ($materials as $material)
            <label class="block">
                <input type="checkbox"
                       name="materials[]"
                       value="{{ $material->id }}"
                       @checked($isEdit && $product->materials->contains($material->id))>
                {{ optional($material->translation())->name }}
            </label>
        @endforeach
    </div>

    {{-- FLAGS --}}
    <div class="bg-white p-6 rounded shadow mb-6 space-y-2">
        <label class="block">
            <input type="checkbox" name="is_new" value="1"
                   @checked($product->is_new ?? false)>
            New
        </label>

        <label class="block">
            <input type="checkbox" name="is_promo" value="1"
                   @checked($product->is_promo ?? false)>
            Promo
        </label>

        <label class="block">
            <input type="checkbox" name="active" value="1"
                   @checked($product->active ?? true)>
            Active
        </label>
    </div>

    {{-- ACTIONS --}}
    <div class="bg-white p-6 rounded shadow flex justify-end">
        <button type="submit"
                class="bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-3 rounded">
            {{ $isEdit ? 'Update Product' : 'Create Product' }}
        </button>
    </div>
</form>
