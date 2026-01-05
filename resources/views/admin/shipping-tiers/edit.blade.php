<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            Edit Shipping Tier
        </h2>
    </x-slot>

    <div class="py-6 max-w-xl mx-auto sm:px-6 lg:px-8">
        <form method="POST"
              action="{{ route('admin.shipping-tiers.update', $shippingTier) }}"
              class="bg-white shadow rounded p-6 space-y-4">
            @csrf
            @method('PATCH')

            <div>
                <label class="block text-sm font-medium">Weight From (g)</label>
                <input type="number" name="weight_from"
                       value="{{ $shippingTier->weight_from }}"
                       class="border rounded w-full px-2 py-1"
                       required>
            </div>

            <div>
                <label class="block text-sm font-medium">Weight To (g)</label>
                <input type="number" name="weight_to"
                       value="{{ $shippingTier->weight_to }}"
                       class="border rounded w-full px-2 py-1"
                       required>
            </div>

            <div>
                <label class="block text-sm font-medium">Cost (gross)</label>
                <input type="number" step="0.01" name="cost_gross"
                       value="{{ $shippingTier->cost_gross }}"
                       class="border rounded w-full px-2 py-1"
                       required>
            </div>

            <div>
                <label class="block text-sm font-medium">Tax</label>
                <select name="tax_id"
                        class="border rounded w-full px-2 py-1"
                        required>
                    @foreach (\App\Models\Tax::where('is_active', true)->get() as $tax)
                        <option value="{{ $tax->id }}"
                            @selected($shippingTier->tax_id === $tax->id)>
                            {{ $tax->name }} ({{ $tax->percentage }}%)
                        </option>
                    @endforeach
                </select>
            </div>

            <label class="flex items-center gap-2">
                <input type="checkbox" name="active"
                       @checked($shippingTier->active)>
                Active
            </label>

            <button class="bg-blue-600 text-white px-4 py-2 rounded">
                Update
            </button>
        </form>
    </div>
</x-app-layout>
