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
    @php $defaultLocale = \App\Models\Locale::defaultLocale()?->code ?? 'en-UK'; @endphp
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach (\App\Models\Locale::activeCodes() as $locale)
            <div>
                <x-input-label>Name ({{ $locale }}) @if($locale === $defaultLocale)<span class="text-status-error">*</span>@endif</x-input-label>
                <input type="text"
                       name="name[{{ $locale }}]"
                       value="{{ old("name.$locale",
                            $mode === 'edit'
                                ? optional($product->translations->where('locale', $locale)->first())->name
                                : '') }}"
                       @if($locale === $defaultLocale) required @endif
                       class="mt-1 block w-full border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">
                <x-input-error :messages="$errors->get('name.'.$locale)" class="mt-2" />
            </div>
        @endforeach
    </div>

    {{-- DESCRIPTION --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach (\App\Models\Locale::activeCodes() as $locale)
            <div>
                <x-input-label>Description ({{ $locale }})</x-input-label>
                <textarea name="description[{{ $locale }}]"
                          rows="3"
                          class="mt-1 block w-full border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">{{ old("description.$locale",
                            $mode === 'edit'
                                ? optional($product->translations->where('locale', $locale)->first())->description
                                : '') }}</textarea>
            </div>
        @endforeach
    </div>

    {{-- TECHNICAL INFORMATION --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach (\App\Models\Locale::activeCodes() as $locale)
            <div>
                <x-input-label>Technical Info ({{ $locale }})</x-input-label>
                <textarea name="technical_info[{{ $locale }}]"
                          rows="3"
                          class="mt-1 block w-full border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">{{ old("technical_info.$locale",
                            $mode === 'edit'
                                ? optional($product->translations->where('locale', $locale)->first())->technical_info
                                : '') }}</textarea>
            </div>
        @endforeach
    </div>

    {{-- PRICE / TAX --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div id="product-price-wrapper">
            <x-input-label for="price">Price (gross)</x-input-label>
            <input type="number"
                   step="0.01"
                   name="price"
                   value="{{ old('price', $product->price ?? '') }}"
                   required
                   class="mt-1 block w-full border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">
            <p class="option-override-notice text-sm text-amber-600 mt-1" style="display:none">&#9888; Overridden by option type price</p>
            <x-input-error :messages="$errors->get('price')" class="mt-2" />
        </div>

        <div id="product-promo-price-wrapper">
            <x-input-label for="promo_price">Promo Price</x-input-label>
            <input type="number"
                   step="0.01"
                   name="promo_price"
                   value="{{ old('promo_price', $product->promo_price ?? '') }}"
                   class="mt-1 block w-full border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">
            <p class="option-override-notice text-sm text-amber-600 mt-1" style="display:none">&#9888; Overridden by option type price</p>
        </div>

        {{-- ✅ TAX SELECTOR --}}
        <div>
            <x-input-label for="tax_id">Tax</x-input-label>
            <select name="tax_id"
                    required
                    class="mt-1 block w-full border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">
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
            <x-input-error :messages="$errors->get('tax_id')" class="mt-2" />
        </div>
    </div>

{{-- CATEGORIES --}}
<div>
    <x-input-label>Categories</x-input-label>
    <select name="categories[]"
            multiple
            class="mt-1 block w-full border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">
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
    <x-input-label>Materials</x-input-label>
    <select name="materials[]"
            multiple
            class="mt-1 block w-full border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">
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
<div class="bg-white p-6 rounded shadow mb-6">
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
                        'is_active'  => $typeModel->is_active,
                        'have_stock' => $typeModel->have_stock,
                        'have_price' => $typeModel->have_price,
                        'name'        => [],
                        'description' => [],
                        'options'     => [],
                    ];

                    foreach (\App\Models\Locale::activeList() as $locale => $label) {
                        $trans = $typeModel->translations->firstWhere('locale', $locale);
                        $type['name'][$locale] = $trans?->name;
                        $type['description'][$locale] = $trans?->description;
                    }

                    foreach ($typeModel->options as $opt) {
                        $optArr = [
                            'is_active'   => $opt->is_active,
                            'stock'       => $opt->stock,
                            'price'       => $opt->price,
                            'promo_price' => $opt->promo_price,
                            'name'        => [],
                            'description' => [],
                        ];
                        foreach (\App\Models\Locale::activeList() as $loc => $lbl) {
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
            class="mt-2 bg-grey-light hover:bg-grey-medium text-grey-dark px-2 py-2 rounded uppercase text-sm">
        + Add option type
    </button>
</div>



{{-- STOCK / DIMENSIONS / FLAGS --}}
<div class="grid grid-cols-1 md:grid-cols-6 gap-6">
    <div>
        <x-input-label for="stock">Stock</x-input-label>
        <div id="product-stock-wrapper">
        <input type="number"
               name="stock"
               value="{{ old('stock', $product->stock ?? 0) }}"
               min="0"
               required
               class="mt-1 block w-full border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">
        <p class="option-override-notice text-sm text-amber-600 mt-1" style="display:none">&#9888; Overridden by option type stock</p>
        <x-input-error :messages="$errors->get('stock')" class="mt-2" />
        </div>
    </div>

    <div>
        <x-input-label for="production_time">Production Time (days)</x-input-label>
        <input type="number"
               name="production_time"
               value="{{ old('production_time', $product->production_time ?? 0) }}"
               min="0"
               required
               class="mt-1 block w-full border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">
        <x-input-error :messages="$errors->get('production_time')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="weight">Weight (grams)</x-input-label>
        <input type="number"
               name="weight"
               min="0"
               value="{{ old('weight', $product->weight ?? '') }}"
               required
               class="mt-1 block w-full border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">
        <x-input-error :messages="$errors->get('weight')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="width">Width (mm)</x-input-label>
        <input type="number"
               step="0.01"
               name="width"
               value="{{ old('width', $product->width ?? '') }}"
               class="mt-1 block w-full border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">
        <x-input-error :messages="$errors->get('width')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="length">Length (mm)</x-input-label>
        <input type="number"
               step="0.01"
               name="length"
               value="{{ old('length', $product->length ?? '') }}"
               class="mt-1 block w-full border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">
        <x-input-error :messages="$errors->get('length')" class="mt-2" />
    </div>

    <div>
        <x-input-label for="height">Height (mm)</x-input-label>
        <input type="number"
               step="0.01"
               name="height"
               value="{{ old('height', $product->height ?? '') }}"
               class="mt-1 block w-full border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">
        <x-input-error :messages="$errors->get('height')" class="mt-2" />
    </div>
</div>

<div class="mt-4">
    <label class="flex items-center gap-2">
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
    <div class="pt-4 flex justify-between">
        <button type="button"
           onclick="window.location.href='{{ route('admin.products.index') }}'"
           class="inline-flex items-center px-2 py-2 bg-white border border-grey-medium rounded text-sm text-grey-dark uppercase shadow-sm hover:bg-grey-light transition ease-in-out duration-150">
            Cancel
        </button>
        <x-primary-button>{{ $mode === 'edit' ? 'Update Product' : 'Create Product' }}</x-primary-button>
    </div>
</form>

{{-- templates used by JS to clone new option types / options --}}
<template id="option-type-template">
    <div class="option-type-block border p-4 relative" data-index="__INDEX__">
        <button type="button" class="absolute top-2 right-2 text-red-600 remove-option-type">&times;</button>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach (\App\Models\Locale::activeCodes() as $locale)
                <div>
                    <label class="block mb-1">Name ({{ $locale }})</label>
                    <input type="text"
                           name="option_types[__INDEX__][name][{{ $locale }}]"
                           class="w-full border rounded px-3 py-2">
                </div>
            @endforeach
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
            @foreach (\App\Models\Locale::activeCodes() as $locale)
                <div>
                    <label class="block mb-1">Description ({{ $locale }})</label>
                    <textarea name="option_types[__INDEX__][description][{{ $locale }}]" class="w-full border rounded px-3 py-2"></textarea>
                </div>
            @endforeach
        </div>
        <div class="flex flex-wrap items-center gap-6 mt-4">
            <label class="flex items-center gap-2">
                <input type="checkbox" name="option_types[__INDEX__][is_active]" value="1">
                Active
            </label>
            <label class="flex items-center gap-2">
                <input type="checkbox" name="option_types[__INDEX__][have_stock]" value="1" class="have-stock-checkbox">
                Controls Stock
                <span class="text-xs text-grey-medium">(overrides product-level stock)</span>
            </label>
            <label class="flex items-center gap-2">
                <input type="checkbox" name="option_types[__INDEX__][have_price]" value="1" class="have-price-checkbox">
                Controls Price
                <span class="text-xs text-grey-medium">(overrides product-level price)</span>
            </label>
        </div>
        <div class="options-list mt-4 space-y-2"></div>
        <button type="button" class="mt-2 bg-grey-light hover:bg-grey-medium text-grey-dark px-2 py-2 rounded uppercase text-sm add-option">
            + Add option
        </button>
    </div>
</template>

<template id="option-template">
    <div class="option-item border p-3 relative" data-index="__OPT_INDEX__">
        <button type="button" class="absolute top-2 right-2 text-red-600 remove-option">&times;</button>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            @foreach (\App\Models\Locale::activeCodes() as $locale)
                <div>
                    <label class="block mb-1">Option Name ({{ $locale }})</label>
                    <input type="text" name="option_types[__INDEX__][options][__OPT_INDEX__][name][{{ $locale }}]" class="w-full border rounded px-3 py-2">
                </div>
            @endforeach
        </div>
        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
            @foreach (\App\Models\Locale::activeCodes() as $locale)
                <div>
                    <label class="block mb-1">Description ({{ $locale }})</label>
                    <textarea name="option_types[__INDEX__][options][__OPT_INDEX__][description][{{ $locale }}]" class="w-full border rounded px-3 py-2"></textarea>
                </div>
            @endforeach
        </div>
        <label class="flex items-center gap-2 mt-2">
            <input type="checkbox" name="option_types[__INDEX__][options][__OPT_INDEX__][is_active]" value="1">
            Active
        </label>
        <div class="mt-2">
            <label class="block mb-1">Stock</label>
            <input type="number" min="0" name="option_types[__INDEX__][options][__OPT_INDEX__][stock]"
                   value="0" placeholder="0"
                   class="w-full border rounded px-3 py-2">
        </div>
        <div class="option-price-fields mt-2 grid grid-cols-1 md:grid-cols-2 gap-4" style="display:none">
            <div>
                <label class="block mb-1">Price (gross)</label>
                <input type="number" step="0.01" min="0"
                       name="option_types[__INDEX__][options][__OPT_INDEX__][price]"
                       class="w-full border rounded px-3 py-2">
            </div>
            <div>
                <label class="block mb-1">Promo Price</label>
                <input type="number" step="0.01" min="0"
                       name="option_types[__INDEX__][options][__OPT_INDEX__][promo_price]"
                       class="w-full border rounded px-3 py-2">
            </div>
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

        /**
         * Enforce mutual exclusion: only one type block may have have_stock = true
         * and only one may have have_price = true across all blocks.
         * When a checkbox is checked, disable all other same-class checkboxes.
         * When unchecked, re-enable them.
         */
        function syncOptionTypeFlags() {
            const stockBoxes = Array.from(container.querySelectorAll('.have-stock-checkbox'));
            const priceBoxes = Array.from(container.querySelectorAll('.have-price-checkbox'));

            const anyStock = stockBoxes.some(cb => cb.checked);
            const anyPrice = priceBoxes.some(cb => cb.checked);

            stockBoxes.forEach(cb => {
                cb.disabled = anyStock && !cb.checked;
            });

            priceBoxes.forEach(cb => {
                cb.disabled = anyPrice && !cb.checked;
            });

            // Show/hide price fields per type block based on have_price
            Array.from(container.querySelectorAll('.option-type-block')).forEach(block => {
                const priceCheckbox = block.querySelector('.have-price-checkbox');
                const showPrice = priceCheckbox && priceCheckbox.checked;
                block.querySelectorAll('.option-price-fields').forEach(el => {
                    el.style.display = showPrice ? '' : 'none';
                });
            });

            // Visual indicator on product-level price/stock fields
            const productPriceWrapper = document.getElementById('product-price-wrapper');
            const productStockWrapper = document.getElementById('product-stock-wrapper');
            const productPromoPriceWrapper = document.getElementById('product-promo-price-wrapper');
            if (productPriceWrapper) {
                productPriceWrapper.classList.toggle('opacity-50', anyPrice);
                const notice = productPriceWrapper.querySelector('.option-override-notice');
                if (notice) notice.style.display = anyPrice ? '' : 'none';
            }
            if (productPromoPriceWrapper) {
                productPromoPriceWrapper.classList.toggle('opacity-50', anyPrice);
                const promoPriceNotice = productPromoPriceWrapper.querySelector('.option-override-notice');
                if (promoPriceNotice) promoPriceNotice.style.display = anyPrice ? '' : 'none';
            }
            if (productStockWrapper) {
                productStockWrapper.classList.toggle('opacity-50', anyStock);
                const notice = productStockWrapper.querySelector('.option-override-notice');
                if (notice) notice.style.display = anyStock ? '' : 'none';
            }
        }

        // Run on page load to reflect saved state
        syncOptionTypeFlags();

        document.getElementById('add-option-type').addEventListener('click', () => {
            const tpl = document.getElementById('option-type-template').innerHTML;
            const html = tpl.replace(/__INDEX__/g, nextTypeIndex);
            container.insertAdjacentHTML('beforeend', html);
            nextTypeIndex++;
            syncOptionTypeFlags();
        });

        container.addEventListener('click', (e) => {
            if (e.target.matches('.remove-option-type')) {
                e.target.closest('.option-type-block').remove();
                syncOptionTypeFlags();
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
                // Show price fields immediately if parent type has have_price
                const priceCheckbox = typeBlock.querySelector('.have-price-checkbox');
                if (priceCheckbox && priceCheckbox.checked) {
                    const newItem = optionsList.lastElementChild;
                    if (newItem) {
                        newItem.querySelectorAll('.option-price-fields').forEach(el => {
                            el.style.display = '';
                        });
                    }
                }
                return;
            }

            if (e.target.matches('.remove-option')) {
                e.target.closest('.option-item').remove();
                return;
            }
        });

        // Listen for have_stock / have_price toggle changes
        container.addEventListener('change', (e) => {
            if (e.target.matches('.have-stock-checkbox') || e.target.matches('.have-price-checkbox')) {
                syncOptionTypeFlags();
            }
        });
    })();
</script>
