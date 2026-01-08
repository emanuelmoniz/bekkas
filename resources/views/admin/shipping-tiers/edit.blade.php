<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            Edit Shipping Tier
        </h2>
    </x-slot>

    <div class="py-6 max-w-3xl mx-auto sm:px-6 lg:px-8">
        <form method="POST"
              action="{{ route('admin.shipping-tiers.update', $shippingTier) }}"
              class="bg-white shadow rounded p-6 space-y-4"
              x-data="{
                  selectedCountries: {{ json_encode($shippingTier->countries->pluck('id')->toArray()) }},
                  availableRegions: [],
                  selectedRegions: {{ json_encode($shippingTier->regions->pluck('id')->toArray()) }},
                  async loadRegions() {
                      if (this.selectedCountries.length === 0) {
                          this.availableRegions = [];
                          this.selectedRegions = [];
                          return;
                      }
                      
                      const response = await fetch('{{ route('admin.shipping-tiers.get-regions') }}', {
                          method: 'POST',
                          headers: {
                              'Content-Type': 'application/json',
                              'X-CSRF-TOKEN': '{{ csrf_token() }}'
                          },
                          body: JSON.stringify({ country_ids: this.selectedCountries })
                      });
                      
                      this.availableRegions = await response.json();
                      // Remove selected regions that don't belong to selected countries
                      this.selectedRegions = this.selectedRegions.filter(regionId => 
                          this.availableRegions.some(r => r.id == regionId)
                      );
                  }
              }"
              x-init="loadRegions()">
            @csrf
            @method('PATCH')

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium">Name PT *</label>
                    <input type="text"
                           name="name_pt"
                           value="{{ old('name_pt', $shippingTier->name_pt) }}"
                           required
                           class="w-full border rounded px-3 py-2 @error('name_pt') border-red-500 @enderror">
                    @error('name_pt')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium">Name EN *</label>
                    <input type="text"
                           name="name_en"
                           value="{{ old('name_en', $shippingTier->name_en) }}"
                           required
                           class="w-full border rounded px-3 py-2 @error('name_en') border-red-500 @enderror">
                    @error('name_en')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium">Weight From (g) *</label>
                    <input type="number" name="weight_from"
                           value="{{ old('weight_from', $shippingTier->weight_from) }}"
                           class="w-full border rounded px-3 py-2 @error('weight_from') border-red-500 @enderror"
                           required>
                    @error('weight_from')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium">Weight To (g) *</label>
                    <input type="number" name="weight_to"
                           value="{{ old('weight_to', $shippingTier->weight_to) }}"
                           class="w-full border rounded px-3 py-2 @error('weight_to') border-red-500 @enderror"
                           required>
                    @error('weight_to')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium">Cost (gross) *</label>
                    <input type="number" step="0.01" name="cost_gross"
                           value="{{ old('cost_gross', $shippingTier->cost_gross) }}"
                           class="w-full border rounded px-3 py-2 @error('cost_gross') border-red-500 @enderror"
                           required>
                    @error('cost_gross')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block text-sm font-medium">Shipping Days *</label>
                    <input type="number" name="shipping_days"
                           value="{{ old('shipping_days', $shippingTier->shipping_days) }}"
                           min="1"
                           class="w-full border rounded px-3 py-2 @error('shipping_days') border-red-500 @enderror"
                           required>
                    @error('shipping_days')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <label class="block text-sm font-medium">Tax *</label>
                    <select name="tax_id"
                            class="w-full border rounded px-3 py-2 @error('tax_id') border-red-500 @enderror"
                            required>
                        @foreach ($taxes as $tax)
                            <option value="{{ $tax->id }}"
                                @selected(old('tax_id', $shippingTier->tax_id) == $tax->id)>
                                {{ $tax->name }} ({{ $tax->percentage }}%)
                            </option>
                        @endforeach
                    </select>
                    @error('tax_id')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="block text-sm font-medium mb-2">Countries * (select at least one)</label>
                <div class="border rounded p-3 max-h-48 overflow-y-auto @error('countries') border-red-500 @enderror">
                    @foreach ($countries as $country)
                        <label class="flex items-center gap-2 py-1">
                            <input type="checkbox"
                                   name="countries[]"
                                   value="{{ $country->id }}"
                                   @change="loadRegions()"
                                   x-model="selectedCountries"
                                   @checked(in_array($country->id, old('countries', $shippingTier->countries->pluck('id')->toArray())))
                                   class="rounded">
                            <span>{{ app()->getLocale() === 'pt' ? $country->name_pt : $country->name_en }}</span>
                        </label>
                    @endforeach
                </div>
                @error('countries')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium mb-2">Regions * (select countries first)</label>
                <div class="border rounded p-3 max-h-48 overflow-y-auto @error('regions') border-red-500 @enderror"
                     x-show="availableRegions.length > 0">
                    <template x-for="region in availableRegions" :key="region.id">
                        <label class="flex items-center gap-2 py-1">
                            <input type="checkbox"
                                   name="regions[]"
                                   :value="region.id"
                                   x-model="selectedRegions"
                                   class="rounded">
                            <span x-text="region.name"></span>
                        </label>
                    </template>
                </div>
                <p class="text-sm text-gray-500 mt-1" x-show="selectedCountries.length === 0">
                    Please select at least one country first
                </p>
                <p class="text-sm text-gray-500 mt-1" x-show="selectedCountries.length > 0 && availableRegions.length === 0">
                    No regions available for selected countries
                </p>
                @error('regions')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <label class="flex items-center gap-2">
                <input type="checkbox" name="active" value="1"
                       @checked(old('active', $shippingTier->active))>
                Active
            </label>

            <div class="flex justify-between">
                <a href="{{ route('admin.shipping-tiers.index') }}"
                   class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                    Cancel
                </a>
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                    Update
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
