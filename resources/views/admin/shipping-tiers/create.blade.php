<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            New Shipping Tier
        </h2>
    </x-slot>

    <div class="py-6 max-w-xl mx-auto sm:px-6 lg:px-8">
        <form method="POST"
              action="{{ route('admin.shipping-tiers.store') }}"
              class="bg-white shadow rounded p-6 space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium">Weight from (g)</label>
                <input type="number"
                       name="weight_from"
                       value="{{ old('weight_from') }}"
                       required
                       class="w-full border rounded px-3 py-2">
            </div>

            <div>
                <label class="block text-sm font-medium">Weight to (g)</label>
                <input type="number"
                       name="weight_to"
                       value="{{ old('weight_to') }}"
                       required
                       class="w-full border rounded px-3 py-2">
            </div>

            <div>
                <label class="block text-sm font-medium">Cost (gross)</label>
                <input type="number"
                       step="0.01"
                       name="cost_gross"
                       value="{{ old('cost_gross') }}"
                       required
                       class="w-full border rounded px-3 py-2">
            </div>

            <div>
                <label class="block text-sm font-medium">Tax</label>
                <select name="tax_id"
                        required
                        class="w-full border rounded px-3 py-2">
                    <option value="">— Select tax —</option>
                    @foreach ($taxes as $tax)
                        <option value="{{ $tax->id }}"
                            @selected(old('tax_id') == $tax->id)>
                            {{ $tax->name }} ({{ $tax->percentage }}%)
                        </option>
                    @endforeach
                </select>
            </div>

            <label class="flex items-center gap-2">
                <input type="checkbox" name="active" checked>
                Active
            </label>

            <div class="flex justify-end">
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                    Save
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
