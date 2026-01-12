<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            Shipping Configuration
        </h2>
    </x-slot>

    <div class="py-6 max-w-4xl mx-auto sm:px-6 lg:px-8">

        <form method="POST" action="{{ route('admin.shipping-config.update') }}" class="bg-white p-6 rounded shadow space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label for="free_shipping_over" class="block font-semibold mb-2">
                    Free Shipping Over (€)
                </label>
                <input type="number" 
                       id="free_shipping_over" 
                       name="free_shipping_over" 
                       step="0.01"
                       min="0"
                       value="{{ old('free_shipping_over', $freeShippingOver) }}"
                       class="w-full border rounded px-3 py-2"
                       required>
                @error('free_shipping_over')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                <p class="text-sm text-gray-600 mt-1">
                    Set to 0 to disable free shipping threshold
                </p>
            </div>

            <div>
                <label for="default_shipping_tier_id" class="block font-semibold mb-2">
                    Default Shipping Tier
                </label>
                <select id="default_shipping_tier_id" 
                        name="default_shipping_tier_id" 
                        class="w-full border rounded px-3 py-2">
                    <option value="">— Select Default Tier —</option>
                    @foreach($shippingTiers as $tier)
                        <option value="{{ $tier->id }}" 
                                @selected(old('default_shipping_tier_id', $defaultShippingTierId) == $tier->id)>
                            {{ $tier->name_en }} ({{ $tier->weight_from }}-{{ $tier->weight_to }}g, {{ $tier->shipping_days }} days)
                            @if(!$tier->active) - INACTIVE @endif
                        </option>
                    @endforeach
                </select>
                @error('default_shipping_tier_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
                <p class="text-sm text-gray-600 mt-1">
                    Used as fallback when no matching tier is found (includes inactive tiers)
                </p>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.orders.index') }}" 
                   class="bg-gray-300 hover:bg-gray-400 px-6 py-2 rounded">
                    Cancel
                </a>
                <button type="submit" 
                        class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded">
                    Save Changes
                </button>
            </div>
        </form>

    </div>
</x-app-layout>
