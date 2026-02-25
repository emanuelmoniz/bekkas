<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-grey-dark">
            Edit Shipping Tier
        </h2>
    </x-slot>

    <div class="py-6 max-w-4xl mx-auto sm:px-6 lg:px-8">
        <form method="POST"
              action="{{ route('admin.shipping-tiers.update', $shippingTier) }}"
              class="bg-white shadow rounded p-6 space-y-4"
              x-data="{
                  selectedCountries: {{ json_encode($shippingTier->countries->pluck('id')->map(fn ($id) => (string) $id)->toArray()) }},
                  availableRegions: [],
                  selectedRegions: {{ json_encode($shippingTier->regions->pluck('id')->map(fn ($id) => (string) $id)->toArray()) }},
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
                @foreach ($locales as $locale => $label)
                <div>
                    <x-input-label>Name ({{ $label }}) <span class="text-status-error">*</span></x-input-label>
                    <input type="text"
                           name="name[{{ $locale }}]"
                           value="{{ old('name.'.$locale, $shippingTier->translations->where('locale', $locale)->first()?->name) }}"
                           required
                           class="mt-1 block w-full border-grey-medium focus:border-accent-primary focus:ring-accent-primary rounded-md shadow-sm">
                    <x-input-error :messages="$errors->get('name.'.$locale)" class="mt-2" />
                </div>
                @endforeach
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="weight_from">Weight From (g) <span class="text-status-error">*</span></x-input-label>
                    <x-text-input id="weight_from" name="weight_from" type="number" class="mt-1 block w-full" :value="old('weight_from', $shippingTier->weight_from)" required />
                    <x-input-error :messages="$errors->get('weight_from')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="weight_to">Weight To (g) <span class="text-status-error">*</span></x-input-label>
                    <x-text-input id="weight_to" name="weight_to" type="number" class="mt-1 block w-full" :value="old('weight_to', $shippingTier->weight_to)" required />
                    <x-input-error :messages="$errors->get('weight_to')" class="mt-2" />
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="cost_gross">Cost (gross) <span class="text-status-error">*</span></x-input-label>
                    <x-text-input id="cost_gross" name="cost_gross" type="number" step="0.01" class="mt-1 block w-full" :value="old('cost_gross', $shippingTier->cost_gross)" required />
                    <x-input-error :messages="$errors->get('cost_gross')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="shipping_days">Shipping Days <span class="text-status-error">*</span></x-input-label>
                    <x-text-input id="shipping_days" name="shipping_days" type="number" class="mt-1 block w-full" :value="old('shipping_days', $shippingTier->shipping_days)" min="1" required />
                    <x-input-error :messages="$errors->get('shipping_days')" class="mt-2" />
                </div>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                    <x-input-label for="tax_id">Tax <span class="text-status-error">*</span></x-input-label>
                    <select id="tax_id" name="tax_id"
                            class="mt-1 block w-full border-grey-medium focus:border-accent-primary focus:ring-accent-primary rounded-md shadow-sm"
                            required>
                        @foreach ($taxes as $tax)
                            <option value="{{ $tax->id }}"
                                @selected(old('tax_id', $shippingTier->tax_id) == $tax->id)>
                                {{ $tax->name }} ({{ $tax->percentage }}%)
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('tax_id')" class="mt-2" />
                </div>
            </div>

            <div>
                <x-input-label class="mb-2">Countries <span class="text-status-error">*</span> <span class="font-normal text-grey-dark">(select at least one)</span></x-input-label>
                <div class="border-grey-medium border rounded-md p-3 max-h-48 overflow-y-auto">
                    @foreach ($countries as $country)
                        <label class="flex items-center gap-2 py-1">
                            <input type="checkbox"
                                   name="countries[]"
                                   value="{{ $country->id }}"
                                   @change="loadRegions()"
                                   x-model="selectedCountries"
                                   @checked(in_array($country->id, old('countries', $shippingTier->countries->pluck('id')->toArray())))
                                   class="rounded">
                            <span>{{ $country->name }}</span>
                        </label>
                    @endforeach
                </div>
                <x-input-error :messages="$errors->get('countries')" class="mt-2" />
            </div>

            <div>
                <x-input-label class="mb-2">Regions <span class="text-status-error">*</span> <span class="font-normal text-grey-dark">(select countries first)</span></x-input-label>
                <div class="border-grey-medium border rounded-md p-3 max-h-48 overflow-y-auto"
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
                <p class="text-sm text-grey-medium mt-1" x-show="selectedCountries.length === 0">
                    Please select at least one country first
                </p>
                <p class="text-sm text-grey-medium mt-1" x-show="selectedCountries.length > 0 && availableRegions.length === 0">
                    No regions available for selected countries
                </p>
                @error('regions')
                    <p class="text-status-error text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <label class="flex items-center gap-2">
                <input type="checkbox" name="active" value="1"
                       @checked(old('active', $shippingTier->active))>
                Active
            </label>

            <div class="flex justify-between">
                <a href="{{ route('admin.shipping-tiers.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-white border border-grey-medium rounded-md font-semibold text-xs text-grey-dark uppercase tracking-widest shadow-sm hover:bg-grey-light transition ease-in-out duration-150">
                    Cancel
                </a>
                <x-primary-button>Update</x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>
