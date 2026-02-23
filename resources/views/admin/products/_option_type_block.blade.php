<div class="option-type-block border p-4 relative" data-index="{{ $index }}">
    <button type="button" class="absolute top-2 right-2 text-red-600 remove-option-type">&times;</button>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
        @foreach (['pt-PT', 'en-UK'] as $locale)
            <div>
                <label class="block font-medium mb-1">Name ({{ $locale }})</label>
                <input type="text"
                       name="option_types[{{ $index }}][name][{{ $locale }}]"
                       value="{{ old("option_types.$index.name.$locale", $data['name'][$locale] ?? '') }}"
                       class="w-full border rounded px-3 py-2">
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4">
        @foreach (['pt-PT', 'en-UK'] as $locale)
            <div>
                <label class="block font-medium mb-1">Description ({{ $locale }})</label>
                <textarea name="option_types[{{ $index }}][description][{{ $locale }}]"
                          class="w-full border rounded px-3 py-2">{{ old("option_types.$index.description.$locale", $data['description'][$locale] ?? '') }}</textarea>
            </div>
        @endforeach
    </div>

    <label class="flex items-center gap-2 mt-4">
        <input type="checkbox" name="option_types[{{ $index }}][is_active]"
               value="1"
               @checked(old("option_types.$index.is_active", $data['is_active'] ?? false))>
        Active
    </label>

    <div class="options-list mt-4 space-y-2">
        @foreach ($data['options'] ?? [] as $j => $opt)
            <div class="option-item border p-3 relative" data-index="{{ $j }}">
                <button type="button" class="absolute top-2 right-2 text-red-600 remove-option">&times;</button>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    @foreach (['pt-PT', 'en-UK'] as $locale)
                        <div>
                            <label class="block font-medium mb-1">Option Name ({{ $locale }})</label>
                            <input type="text"
                                   name="option_types[{{ $index }}][options][{{ $j }}][name][{{ $locale }}]"
                                   value="{{ old("option_types.$index.options.$j.name.$locale", $opt['name'][$locale] ?? '') }}"
                                   class="w-full border rounded px-3 py-2">
                        </div>
                    @endforeach
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-2">
                    @foreach (['pt-PT', 'en-UK'] as $locale)
                        <div>
                            <label class="block font-medium mb-1">Description ({{ $locale }})</label>
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
                    <label class="block font-medium mb-1">Stock</label>
                    <input type="number" min="0"
                           name="option_types[{{ $index }}][options][{{ $j }}][stock]"
                           value="{{ old("option_types.$index.options.$j.stock", $opt['stock'] ?? 0) }}"
                           class="w-full border rounded px-3 py-2">
                </div>
            </div>
        @endforeach
    </div>

    <button type="button" class="mt-2 bg-grey-light hover:bg-grey-medium text-grey-dark px-3 py-1 rounded add-option">
        + Add option
    </button>
</div>
