<form method="POST"
      action="{{ $mode === 'edit'
            ? route('admin.products.update', $product)
            : route('admin.products.store') }}"
      class="bg-white p-6 rounded shadow space-y-6">

    @csrf
    @if ($mode === 'edit')
        @method('PATCH')
    @endif

    {{-- TRANSLATIONS --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach (['pt-PT', 'en-UK'] as $locale)
            <div>
                <label class="block font-medium mb-1">
                    Name ({{ $locale }})
                </label>
                <input type="text"
                       name="name[{{ $locale }}]"
                       value="{{ old("name.$locale",
                            $mode === 'edit'
                                ? optional($product->translations->where('locale', $locale)->first())->name
                                : '') }}"
                       required
                       class="w-full border rounded px-3 py-2">
            </div>
        @endforeach
    </div>

    {{-- PRICE / TAX --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div>
            <label class="block font-medium mb-1">Price (gross)</label>
            <input type="number"
                   step="0.01"
                   name="price"
                   value="{{ old('price', $product->price ?? '') }}"
                   required
                   class="w-full border rounded px-3 py-2">
        </div>

        <div>
            <label class="block font-medium mb-1">Promo Price</label>
            <input type="number"
                   step="0.01"
                   name="promo_price"
                   value="{{ old('promo_price', $product->promo_price ?? '') }}"
                   class="w-full border rounded px-3 py-2">
        </div>

        {{-- ✅ TAX SELECTOR --}}
        <div>
            <label class="block font-medium mb-1">Tax</label>
            <select name="tax_id"
                    required
                    class="w-full border rounded px-3 py-2">
                <option value="">— Select tax —</option>

                @foreach ($taxes as $tax)
                    <option value="{{ $tax->id }}"
                        @selected(
                            old('tax_id', $product->tax_id ?? null) === $tax->id
                        )>
                        {{ $tax->name }} ({{ $tax->percentage }}%)
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- STOCK / FLAGS --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div>
            <label class="block font-medium mb-1">Stock</label>
            <input type="number"
                   name="stock"
                   value="{{ old('stock', $product->stock ?? 0) }}"
                   required
                   class="w-full border rounded px-3 py-2">
        </div>

        <label class="flex items-center gap-2 mt-7">
            <input type="checkbox"
                   name="is_new"
                   @checked(old('is_new', $product->is_new ?? false))>
            New
        </label>

        <label class="flex items-center gap-2 mt-7">
            <input type="checkbox"
                   name="is_promo"
                   @checked(old('is_promo', $product->is_promo ?? false))>
            Promo
        </label>
    </div>

    {{-- ACTIVE --}}
    <label class="flex items-center gap-2">
        <input type="checkbox"
               name="active"
               @checked(old('active', $product->active ?? true))>
        Active
    </label>

    {{-- SUBMIT --}}
    <div class="pt-4">
        <button type="submit"
                class="bg-blue-600 hover:bg-blue-700 text-white font-semibold px-6 py-3 rounded">
            {{ $mode === 'edit' ? 'Update Product' : 'Create Product' }}
        </button>
    </div>
</form>
