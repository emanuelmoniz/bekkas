<form method="GET" class="bg-white border border-grey-light rounded p-4 space-y-5">

    {{-- ORDER --}}
    <select name="order"
            onchange="this.form.submit()"
            class="w-full border border-grey-light rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary">
        <option value="">{{ t('store.order.default') ?: 'Sort' }}</option>
        <option value="name_az" @selected(request('order') === 'name_az')>{{ t('store.order.name_az') ?: 'Name A-Z' }}</option>
        <option value="name_za" @selected(request('order') === 'name_za')>{{ t('store.order.name_za') ?: 'Name Z-A' }}</option>
        <option value="price_low_high" @selected(request('order') === 'price_low_high')>{{ t('store.order.price_low_high') ?: 'Price Low-High' }}</option>
        <option value="price_high_low" @selected(request('order') === 'price_high_low')>{{ t('store.order.price_high_low') ?: 'Price High-Low' }}</option>
        <option value="featured_first" @selected(request('order') === 'featured_first')>{{ t('store.order.featured_first') ?: 'Featured First' }}</option>
        <option value="promo_first" @selected(request('order') === 'promo_first')>{{ t('store.order.promo_first') ?: 'Promo First' }}</option>
    </select>

    {{-- NAME --}}
    <input type="text"
           name="name"
           value="{{ request('name') }}"
           placeholder="{{ t('store.filter.name') ?: 'Name' }}"
           class="w-full border border-grey-light rounded px-3 py-2 text-sm focus:outline-none focus:ring-1 focus:ring-primary">

    {{-- BOOLEAN FILTERS --}}
    <div class="space-y-2">
        <label class="flex items-center gap-2 cursor-pointer select-none">
            <input type="checkbox" name="available" value="1"
                   @checked(request()->boolean('available'))
                   class="rounded border-grey-medium text-accent-primary focus:ring-primary">
            <span class="text-sm">{{ t('store.filter.in_stock') ?: 'In Stock' }}</span>
        </label>

        <label class="flex items-center gap-2 cursor-pointer select-none">
            <input type="checkbox" name="is_promo" value="1"
                   @checked(request()->boolean('is_promo'))
                   class="rounded border-grey-medium text-accent-primary focus:ring-primary">
            <span class="text-sm">{{ t('store.filter.in_promotion') ?: 'In Promotion' }}</span>
        </label>

        <label class="flex items-center gap-2 cursor-pointer select-none">
            <input type="checkbox" name="is_featured" value="1"
                   @checked(request()->boolean('is_featured'))
                   class="rounded border-grey-medium text-accent-primary focus:ring-primary">
            <span class="text-sm">{{ t('store.filter.featured') ?: 'Featured' }}</span>
        </label>
    </div>

    {{-- PRICE RANGE --}}
    @if($priceCeiling > $priceFloor)
    <div class="space-y-3"
         x-data="{
             floor: {{ $priceFloor }},
             ceiling: {{ $priceCeiling }},
             minVal: {{ (int) request('price_min', $priceFloor) }},
             maxVal: {{ (int) request('price_max', $priceCeiling) }},
             startDrag(which) {
                 const onMove = (e) => {
                     const rect = this.$refs.track.getBoundingClientRect();
                     let pct = Math.max(0, Math.min(1, (e.clientX - rect.left) / rect.width));
                     let val = Math.round(this.floor + pct * (this.ceiling - this.floor));
                     if (which === 'min') this.minVal = Math.min(val, this.maxVal);
                     else this.maxVal = Math.max(val, this.minVal);
                 };
                 const onUp = () => {
                     window.removeEventListener('pointermove', onMove);
                     window.removeEventListener('pointerup', onUp);
                 };
                 window.addEventListener('pointermove', onMove);
                 window.addEventListener('pointerup', onUp);
             },
             pct(v) {
                 return this.ceiling === this.floor ? 0 : (v - this.floor) / (this.ceiling - this.floor) * 100;
             }
         }">

        <p class="text-sm font-semibold text-grey-dark">
            {{ t('store.filter.price_range') ?: 'Price Range' }}
        </p>

        {{-- Slider track --}}
        <div class="relative h-5 flex items-center select-none py-1" x-ref="track">
            {{-- Background rail --}}
            <div class="w-full h-1.5 bg-grey-light rounded-full"></div>
            {{-- Active fill --}}
            <div class="absolute h-1.5 bg-primary rounded-full pointer-events-none"
                 :style="`left: ${pct(minVal)}%; width: ${pct(maxVal) - pct(minVal)}%`"></div>
            {{-- Min thumb --}}
            <div class="absolute w-4 h-4 bg-white border-2 border-accent-primary rounded-full cursor-grab active:cursor-grabbing shadow touch-none z-10"
                 :style="`left: ${pct(minVal)}%; transform: translateX(-50%)`"
                 @pointerdown.prevent="startDrag('min')"></div>
            {{-- Max thumb --}}
            <div class="absolute w-4 h-4 bg-white border-2 border-accent-primary rounded-full cursor-grab active:cursor-grabbing shadow touch-none z-10"
                 :style="`left: ${pct(maxVal)}%; transform: translateX(-50%)`"
                 @pointerdown.prevent="startDrag('max')"></div>
        </div>

        {{-- Number inputs --}}
        <div class="flex items-center gap-2">
            <div class="relative flex-1">
                <span class="absolute left-2 top-1/2 -translate-y-1/2 text-xs text-grey-medium pointer-events-none">€</span>
                <input type="number"
                       name="price_min"
                       x-model.number="minVal"
                       @input="minVal = Math.max(floor, Math.min(minVal, maxVal));"
                       @change="minVal = Math.max(floor, Math.min(Number($event.target.value), maxVal))"
                       min="{{ $priceFloor }}"
                       max="{{ $priceCeiling }}"
                       class="w-full border border-grey-light rounded pl-5 pr-2 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-primary">
            </div>
            <span class="text-grey-medium text-xs shrink-0">—</span>
            <div class="relative flex-1">
                <span class="absolute left-2 top-1/2 -translate-y-1/2 text-xs text-grey-medium pointer-events-none">€</span>
                <input type="number"
                       name="price_max"
                       x-model.number="maxVal"
                       @input="maxVal = Math.min(ceiling, Math.max(maxVal, minVal));"
                       @change="maxVal = Math.min(ceiling, Math.max(Number($event.target.value), minVal))"
                       min="{{ $priceFloor }}"
                       max="{{ $priceCeiling }}"
                       class="w-full border border-grey-light rounded pl-5 pr-2 py-1.5 text-sm focus:outline-none focus:ring-1 focus:ring-primary">
            </div>
        </div>
    </div>
    @endif

    {{-- CATEGORIES --}}
    @if($categories->isNotEmpty())
        <div class="space-y-2">
            <p class="text-sm font-semibold text-grey-dark">
                {{ t('store.filter.categories') ?: 'Categories' }}
            </p>
            @foreach($categories as $category)
                @php
                    $catName = optional($category->translation())->name ?? '—';
                    $catCount = $categoryCounts[$category->id] ?? 0;
                    $checked = in_array($category->id, (array) request('category_ids', []));
                @endphp
                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <input type="checkbox"
                           name="category_ids[]"
                           value="{{ $category->id }}"
                           @checked($checked)
                           class="rounded border-grey-medium text-accent-primary focus:ring-primary">
                    <span class="text-sm">{{ $catName }} ({{ $catCount }})</span>
                </label>
            @endforeach
        </div>
    @endif

    {{-- MATERIALS --}}
    @if($materials->isNotEmpty())
        <div class="space-y-2">
            <p class="text-sm font-semibold text-grey-dark">
                {{ t('store.filter.materials') ?: 'Materials' }}
            </p>
            @foreach($materials as $material)
                @php
                    $matName = optional($material->translation())->name ?? '—';
                    $matCount = $materialCounts[$material->id] ?? 0;
                    $checked = in_array($material->id, (array) request('material_ids', []));
                @endphp
                <label class="flex items-center gap-2 cursor-pointer select-none">
                    <input type="checkbox"
                           name="material_ids[]"
                           value="{{ $material->id }}"
                           @checked($checked)
                           class="rounded border-grey-medium text-accent-primary focus:ring-primary">
                    <span class="text-sm">{{ $matName }} ({{ $matCount }})</span>
                </label>
            @endforeach
        </div>
    @endif

    {{-- ACTIONS --}}
    <div class="pt-1">
        <x-primary-cta type="submit" :full-width="true">
            {{ t('store.filter.apply') ?: 'Filter' }}
        </x-primary-cta>
        <x-optional-cta type="button" class="mt-2"
                        onclick="window.location='{{ route($resetRoute) }}'"
                        :full-width="true"
                       >
            {{ t('store.filter.reset') ?: 'Reset' }}
        </x-optional-cta>
    </div>

</form>