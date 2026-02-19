@props(['categories', 'materials', 'resetRoute' => 'store.index'])

<form method="GET" class="bg-white p-4 rounded shadow">
    <div class="grid grid-cols-1 md:grid-cols-7 gap-4">

        <input type="text"
               name="name"
               value="{{ request('name') }}"
               placeholder="{{ t('store.filter.name') ?: 'Name' }}"
               class="border rounded px-3 py-2">

        {{-- CATEGORY --}}
        <div x-data="{ open:false, search:'', selected:'{{ request('category_id') }}' }" class="relative">
            <input type="hidden" name="category_id" :value="selected">
            <button type="button" @click="open=!open"
                    class="w-full border rounded px-3 py-2 text-left">
                {{ optional($categories->firstWhere('id', request('category_id'))?->translation())->name ?? t('store.filter.category') ?: 'Category' }}
            </button>
            <div x-show="open" @click.outside="open=false"
                 class="absolute z-10 w-full bg-white border rounded shadow mt-1">
                <input x-model="search" class="w-full px-3 py-2 border-b" placeholder="Search...">
                @foreach ($categories as $category)
                    @php $name = optional($category->translation())->name; @endphp
                    <div x-show="'{{ strtolower($name) }}'.includes(search.toLowerCase())"
                         @click="selected='{{ $category->id }}'; open=false"
                         class="px-3 py-2 hover:bg-gray-100 cursor-pointer">
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
                {{ optional($materials->firstWhere('id', request('material_id'))?->translation())->name ?? t('store.filter.material') ?: 'Material' }}
            </button>
            <div x-show="open" @click.outside="open=false"
                 class="absolute z-10 w-full bg-white border rounded shadow mt-1">
                <input x-model="search" class="w-full px-3 py-2 border-b" placeholder="{{ t('store.filter.search') ?: 'Search...' }}">
                @foreach ($materials as $material)
                    @php $name = optional($material->translation())->name; @endphp
                    <div x-show="'{{ strtolower($name) }}'.includes(search.toLowerCase())"
                         @click="selected='{{ $material->id }}'; open=false"
                         class="px-3 py-2 hover:bg-gray-100 cursor-pointer">
                        {{ $name }}
                    </div>
                @endforeach
            </div>
        </div>

        <select name="is_new" class="border rounded px-3 py-2">
            <option value="">{{ t('store.filter.new') ?: 'New' }}</option>
            <option value="1" @selected(request('is_new')==='1')>{{ t('store.filter.only_new') ?: 'Only New' }}</option>
            <option value="0" @selected(request('is_new')==='0')>{{ t('store.filter.not_new') ?: 'Not New' }}</option>
        </select>

        <select name="is_promo" class="border rounded px-3 py-2">
            <option value="">{{ t('store.filter.promo') ?: 'Promo' }}</option>
            <option value="1" @selected(request('is_promo')==='1')>{{ t('store.filter.only_promo') ?: 'Only Promo' }}</option>
            <option value="0" @selected(request('is_promo')==='0')>{{ t('store.filter.not_promo') ?: 'Not Promo' }}</option>
        </select>

        <label class="flex items-center gap-2">
            <input type="checkbox" name="available" value="1" @checked(request('available'))>
            {{ t('store.filter.in_stock') ?: 'In stock' }}
        </label>

        <div class="flex gap-2">
            <a href="{{ route($resetRoute) }}" 
               class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                {{ t('store.filter.reset') ?: 'Reset' }}
            </a>
            <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                {{ t('store.filter.apply') ?: 'Filter' }}
            </button>
        </div>
    </div>
</form>
