<form method="POST"
      action="{{ $mode === 'edit'
            ? route('admin.products.update', $product)
            : route('admin.products.store') }}"
      class="bg-light p-6 rounded shadow space-y-6">

    @csrf

    {{-- show validation errors if any (previously missing) --}}
    @if ($errors->any())
        <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
            <ul class="list-disc pl-5">
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

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
                   class="w-full border rounded px-3 py-2 @error('price') border-status-error @enderror">
            @error('price')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
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
                    class="w-full border rounded px-3 py-2 @error('tax_id') border-status-error @enderror">
            @error('tax_id')
                <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
            @enderror
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

{{-- CATEGORIES --}}
<div>
    <label class="block font-medium mb-1">Categories</label>
    <select name="categories[]"
            multiple
            class="w-full border rounded px-3 py-2">
        @foreach ($categories as $category)
            <option value="{{ $category->id }}"
                @selected(
                    isset($product) &&
                    $product->categories->contains($category->id)
                )>
                {{ optional($category->translation())->name }}
            </option>
        @endforeach
    </select>
</div>

{{-- MATERIALS --}}
<div>
    <label class="block font-medium mb-1">Materials</label>
    <select name="materials[]"
            multiple
            class="w-full border rounded px-3 py-2">
        @foreach ($materials as $material)
            <option value="{{ $material->id }}"
                @selected(
                    isset($product) &&
                    $product->materials->contains($material->id)
                )>
                {{ optional($material->translation())->name }}
            </option>
        @endforeach
    </select>
</div>

{{-- OPTION TYPES (for products with choices like size/color) --}}
<div class="bg-light p-6 rounded shadow mb-6">
    <h3 class="font-semibold mb-4">Option Types</h3>
    <div id="option-types-container" class="space-y-4">
        @php
            $oldTypes = old('option_types', []);
        @endphp

        @foreach ($oldTypes as $i => $type)
            @include('admin.products._option_type_block', [
                'index' => $i,
                'data' => $type,
            ])
        @endforeach

        @if (empty($oldTypes) && isset($product))
            @foreach ($product->optionTypes as $i => $typeModel)
                @php
                    // convert model to array to reuse same partial
                    $type = [
                        'is_active' => $typeModel->is_active,
                        'name' => [],
                        'description' => [],
                        'options' => [],
                    ];

                    foreach (config('app.locales') as $locale => $label) {
                        $trans = $typeModel->translations->firstWhere('locale', $locale);
                        $type['name'][$locale] = $trans?->name;
                        $type['description'][$locale] = $trans?->description;
                    }

                    foreach ($typeModel->options as $opt) {
                        $optArr = [
                            'is_active' => $opt->is_active,
                            'stock' => $opt->stock,
                            'name' => [],
                            'description' => [],
                        ];
                        foreach (config('app.locales') as $loc => $lbl) {
                            $oTrans = $opt->translations->firstWhere('locale', $loc);
                            $optArr['name'][$loc] = $oTrans?->name;
                            $optArr['description'][$loc] = $oTrans?->description;
                        }
                        $type['options'][] = $optArr;
                    }
                @endphp
                @include('admin.products._option_type_block', [
                    'index' => $i,
                    'data' => $type,
                ])
            @endforeach
        @endif
    </div>

    <button type="button" id="add-option-type"
            class="mt-2 bg-grey-light hover:bg-grey-medium text-grey-dark px-3 py-1 rounded">
        + Add option type
    </button>
</div>



{{-- STOCK / DIMENSIONS / FLAGS --}}
<div class="grid grid-cols-1 md:grid-cols-4 gap-6">
    <div>
        <label class="block font-medium mb-1">Stock</label>
        <input type="number"
               name="stock"
               value="{{ old('stock', $product->stock ?? 0) }}"
               min="0"
               required
               class="w-full border rounded px-3 py-2 @error('stock') border-status-error @enderror">
        @error('stock')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block font-medium mb-1">Production Time (days)</label>
        <input type="number"
               name="production_time"
               value="{{ old('production_time', $product->production_time ?? 0) }}"
               min="0"
               required
               class="w-full border rounded px-3 py-2 @error('production_time') border-status-error @enderror">
        @error('production_time')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div>
        <label class="block font-medium mb-1">Weight (grams)</label>
        <input type="number"
               name="weight"
               min="0"
               value="{{ old('weight', $product->weight ?? '') }}"
               required
               class="w-full border rounded px-3 py-2 @error('weight') border-status-error @enderror">
        @error('weight')
            <p class="text-red-600 text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <label class="flex items-center gap-2 mt-7">
        <input type="checkbox"
               name="is_backorder"
               @checked(old('is_backorder', $product->is_backorder ?? true))>
        Allow Back Order
    </label>
</div>

<div class="grid grid-cols-1 md:grid-cols-3 gap-6">
    <label class="flex items-center gap-2">
        <input type="checkbox"
               name="is_featured"
               @checked(old('is_featured', $product->is_featured ?? false))>
        Featured
    </label>

    <label class="flex items-center gap-2">
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
                class="bg-accent-primary hover:bg-accent-primary/90 text-light font-semibold px-6 py-3 rounded">
            {{ $mode === 'edit' ? 'Update Product' : 'Create Product' }}
        </button>
    </div>
</form>

{{-- templates used by JS to clone new option types / options --}}
<template id="option-type-template">
    <div class="option-type-block border p-4 relative" data-index="__INDEX__">
        <button type="button" class="absolute top-2 right-2 text-red-600 remove-option-type">&times;</button>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach (['pt-PT', 'en-UK'] as $locale)
                <div>
                    <label class="block font-medium mb-1">Name ({{ $locale }})</label>
                    <input type="text"
                           name="option_types[__INDEX__][name][{{ $locale }}]"
                           class="w-full border rounded px-3 py-2">
                </div>
            @endforeach
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
            @foreach (['pt-PT', 'en-UK'] as $locale)
                <div>
                    <label class="block font-medium mb-1">Description ({{ $locale }})</label>
                    <textarea name="option_types[__INDEX__][description][{{ $locale }}]" class="w-full border rounded px-3 py-2"></textarea>
                </div>
            @endforeach
        </div>
        <label class="flex items-center gap-2 mt-4">
            <input type="checkbox" name="option_types[__INDEX__][is_active]" value="1">
            Active
        </label>
        <div class="options-list mt-4 space-y-2"></div>
        <button type="button" class="mt-2 bg-grey-light hover:bg-grey-medium text-grey-dark px-3 py-1 rounded add-option">
            + Add option
        </button>
    </div>
</template>

<template id="option-template">
    <div class="option-item border p-3 relative" data-index="__OPT_INDEX__">
        <button type="button" class="absolute top-2 right-2 text-red-600 remove-option">&times;</button>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach (['pt-PT', 'en-UK'] as $locale)
                <div>
                    <label class="block font-medium mb-1">Option Name ({{ $locale }})</label>
                    <input type="text" name="option_types[__INDEX__][options][__OPT_INDEX__][name][{{ $locale }}]" class="w-full border rounded px-3 py-2">
                </div>
            @endforeach
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
            @foreach (['pt-PT', 'en-UK'] as $locale)
                <div>
                    <label class="block font-medium mb-1">Description ({{ $locale }})</label>
                    <textarea name="option_types[__INDEX__][options][__OPT_INDEX__][description][{{ $locale }}]" class="w-full border rounded px-3 py-2"></textarea>
                </div>
            @endforeach
        </div>
        <label class="flex items-center gap-2 mt-2">
            <input type="checkbox" name="option_types[__INDEX__][options][__OPT_INDEX__][is_active]" value="1">
            Active
        </label>
        <div class="mt-2">
            <label class="block font-medium mb-1">Stock</label>
            <input type="number" min="0" name="option_types[__INDEX__][options][__OPT_INDEX__][stock]" class="w-full border rounded px-3 py-2">
        </div>
    </div>
</template>

<script>
    (() => {
        const container = document.getElementById('option-types-container');
        // determine next free index by checking existing blocks, so removing
        // earlier ones doesn't create duplicates.
        let nextTypeIndex = 0;
        Array.from(container.querySelectorAll('.option-type-block'))
            .forEach(el => {
                const idx = parseInt(el.getAttribute('data-index'), 10);
                if (!isNaN(idx) && idx >= nextTypeIndex) {
                    nextTypeIndex = idx + 1;
                }
            });

        document.getElementById('add-option-type').addEventListener('click', () => {
            const tpl = document.getElementById('option-type-template').innerHTML;
            const html = tpl.replace(/__INDEX__/g, nextTypeIndex);
            container.insertAdjacentHTML('beforeend', html);
            nextTypeIndex++;
        });

        container.addEventListener('click', (e) => {
            if (e.target.matches('.remove-option-type')) {
                e.target.closest('.option-type-block').remove();
                return;
            }

            if (e.target.matches('.add-option')) {
                const typeBlock = e.target.closest('.option-type-block');
                const typeIndex = typeBlock.getAttribute('data-index');
                const optionsList = typeBlock.querySelector('.options-list');
                // compute next option index by looking at existing items
                let optIndex = 0;
                Array.from(optionsList.querySelectorAll('.option-item')).forEach(el => {
                    const i = parseInt(el.getAttribute('data-index'), 10);
                    if (!isNaN(i) && i >= optIndex) {
                        optIndex = i + 1;
                    }
                });
                let tpl = document.getElementById('option-template').innerHTML;
                tpl = tpl.replace(/__INDEX__/g, typeIndex).replace(/__OPT_INDEX__/g, optIndex);
                optionsList.insertAdjacentHTML('beforeend', tpl);
                return;
            }

            if (e.target.matches('.remove-option')) {
                e.target.closest('.option-item').remove();
                return;
            }
        });
    })();
</script>
