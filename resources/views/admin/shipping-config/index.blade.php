<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-grey-dark">
            Shipping Configuration
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">

        <form method="POST" action="{{ route('admin.shipping-config.update') }}" class="bg-white p-6 rounded shadow space-y-6">
            @csrf
            @method('PUT')

            <div>
                <label for="free_shipping_over" class="block mb-2">
                    Free Shipping Over (€)
                </label>
                <input type="number" 
                       id="free_shipping_over" 
                       name="free_shipping_over" 
                       step="0.01"
                       min="0"
                       value="{{ old('free_shipping_over', $freeShippingOver) }}"
                       class="w-full border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm"
                       required>
                @error('free_shipping_over')
                    <p class="text-status-error text-sm mt-1">{{ $message }}</p>
                @enderror
                <p class="text-sm text-grey-dark mt-1">
                    Set to 0 to disable free shipping threshold
                </p>
            </div>

            <div>
                <label for="default_shipping_tier_id" class="block mb-2">
                    Global Default Shipping Tier (Fallback)
                </label>
                <select id="default_shipping_tier_id" 
                        name="default_shipping_tier_id" 
                        class="w-full border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">
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
                    <p class="text-status-error text-sm mt-1">{{ $message }}</p>
                @enderror
                <p class="text-sm text-grey-dark mt-1">
                    Used as global fallback when no region-specific default is configured. Edit region-specific defaults in each region's settings.
                </p>
            </div>

            <div>
                <label for="tracking_statuses" class="block mb-2">
                    Order Statuses for Tracking URL
                </label>
                    @php
                        $allStatuses = \App\Models\OrderStatus::all();
                    @endphp
                    <select id="tracking_statuses" name="tracking_statuses[]" multiple class="form-multiselect block w-full mt-1">
                        @foreach ($allStatuses as $status)
                            <option value="{{ $status->code }}" {{ in_array($status->code, $trackingStatuses ?? []) ? 'selected' : '' }}>
                                {{ $status->code }}
                                @php $trans = $status->translation(); @endphp
                                @if($trans && $trans->name && $trans->name !== $status->code)
                                    - {{ $trans->name }}
                                @endif
                            </option>
                        @endforeach
                    </select>
                <p class="text-sm text-grey-dark mt-1">
                    Select which order statuses will display the tracking URL to the client.
                </p>
            </div>

            <div class="flex justify-end gap-3">
                <x-default-button type="button" onclick="window.location.href='{{ route('admin.orders.index') }}'">
                    Cancel
                </x-default-button>
                <x-default-button type="submit">
                    Save Changes
                </x-default-button>
            </div>
        </form>

    </div>
</x-app-layout>
