<div class="option-type-block border p-4 relative" data-index="{{ $index }}">
    <button type="button" class="absolute top-2 right-2 text-red-600 remove-option-type">&times;</button>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
        @foreach (\App\Models\Locale::activeCodes() as $locale)
            <div>
                <label class="block mb-1">Name ({{ $locale }})</label>
                <input type="text"
                       name="option_types[{{ $index }}][name][{{ $locale }}]"
                       value="{{ old("option_types.$index.name.$locale", $data['name'][$locale] ?? '') }}"
                       class="w-full border rounded px-3 py-2">
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-4">
        @foreach (\App\Models\Locale::activeCodes() as $locale)
            <div>
                <label class="block mb-1">Description ({{ $locale }})</label>
                <textarea name="option_types[{{ $index }}][description][{{ $locale }}]"
                          class="w-full border rounded px-3 py-2">{{ old("option_types.$index.description.$locale", $data['description'][$locale] ?? '') }}</textarea>
            </div>
        @endforeach
    </div>

    <div class="flex flex-wrap items-center gap-6 mt-4">
        <label class="flex items-center gap-2">
            <input type="checkbox" name="option_types[{{ $index }}][is_active]"
                   value="1"
                   @checked(old("option_types.$index.is_active", $data['is_active'] ?? false))>
            Active
        </label>

        <label class="flex items-center gap-2">
            <input type="checkbox" name="option_types[{{ $index }}][have_stock]"
                   value="1"
                   class="have-stock-checkbox"
                   @checked(old("option_types.$index.have_stock", $data['have_stock'] ?? false))>
            Controls Stock
            <span class="text-xs text-grey-medium">(overrides product-level stock)</span>
        </label>

        <label class="flex items-center gap-2">
            <input type="checkbox" name="option_types[{{ $index }}][have_price]"
                   value="1"
                   class="have-price-checkbox"
                   @checked(old("option_types.$index.have_price", $data['have_price'] ?? false))>
            Controls Price
            <span class="text-xs text-grey-medium">(overrides product-level price)</span>
        </label>
    </div>

    <div class="options-list mt-4 space-y-2">
        @foreach ($data['options'] ?? [] as $j => $opt)
            <div class="option-item border p-3 relative" data-index="{{ $j }}">
                <button type="button" class="absolute top-2 right-2 text-red-600 remove-option">&times;</button>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                    @foreach (\App\Models\Locale::activeCodes() as $locale)
                        <div>
                            <label class="block mb-1">Option Name ({{ $locale }})</label>
                            <input type="text"
                                   name="option_types[{{ $index }}][options][{{ $j }}][name][{{ $locale }}]"
                                   value="{{ old("option_types.$index.options.$j.name.$locale", $opt['name'][$locale] ?? '') }}"
                                   class="w-full border rounded px-3 py-2">
                        </div>
                    @endforeach
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mt-2">
                    @foreach (\App\Models\Locale::activeCodes() as $locale)
                        <div>
                            <label class="block mb-1">Description ({{ $locale }})</label>
                            <textarea name="option_types[{{ $index }}][options][{{ $j }}][description][{{ $locale }}]"
                                      class="w-full border rounded px-3 py-2">{{ old("option_types.$index.options.$j.description.$locale", $opt['description'][$locale] ?? '') }}</textarea>
                        </div>
                    @endforeach
                </div>

                <label class="flex items-center gap-2 mt-2">
                    <input type="checkbox" name="option_types[{{ $index }}][options][{{ $j }}][is_active]" value="1"
                           @checked(old("option_types.$index.options.$j.is_active", $opt['is_active'] ?? false))>
                    Active
                </label>

                <div class="mt-2">
                    <label class="block mb-1">Stock</label>
                    <input type="number" min="0"
                           name="option_types[{{ $index }}][options][{{ $j }}][stock]"
                           value="{{ old("option_types.$index.options.$j.stock", $opt['stock'] ?? 0) }}"
                           class="w-full border rounded px-3 py-2">
                </div>

                <div class="option-price-fields mt-2 grid grid-cols-1 lg:grid-cols-2 gap-4"
                     style="{{ ($data['have_price'] ?? false) ? '' : 'display:none' }}">
                    <div>
                        <label class="block mb-1">Price (gross)</label>
                        <input type="number" step="0.01" min="0"
                               name="option_types[{{ $index }}][options][{{ $j }}][price]"
                               value="{{ old("option_types.$index.options.$j.price", $opt['price'] ?? '') }}"
                               class="w-full border rounded px-3 py-2">
                    </div>
                    <div>
                        <label class="block mb-1">Promo Price</label>
                        <input type="number" step="0.01" min="0"
                               name="option_types[{{ $index }}][options][{{ $j }}][promo_price]"
                               value="{{ old("option_types.$index.options.$j.promo_price", $opt['promo_price'] ?? '') }}"
                               class="w-full border rounded px-3 py-2">
                    </div>
                </div>
            </div>
        @endforeach
    </div>

    <button type="button" class="mt-2 bg-grey-light hover:bg-grey-medium text-grey-dark px-2 py-2 rounded uppercase text-sm add-option">
        + Add option
    </button>
</div>
