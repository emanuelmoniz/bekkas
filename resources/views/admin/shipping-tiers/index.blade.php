<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            Shipping Tiers
        </h2>
    </x-slot>

    <div class="py-6 max-w-6xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-4 flex justify-end">
            <a href="{{ route('admin.shipping-tiers.create') }}"
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                New Shipping Tier
            </a>
        </div>

        <div class="bg-white shadow rounded">
            <table class="min-w-full border">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left">Weight From (g)</th>
                        <th class="px-4 py-2 text-left">Weight To (g)</th>
                        <th class="px-4 py-2 text-left">Cost (gross)</th>
                        <th class="px-4 py-2 text-left">Tax</th>
                        <th class="px-4 py-2 text-left">Active</th>
                        <th class="px-4 py-2 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tiers as $tier)
                        <tr class="border-t">
                            <td class="px-4 py-2">{{ $tier->weight_from }}</td>
                            <td class="px-4 py-2">{{ $tier->weight_to }}</td>
                            <td class="px-4 py-2">
                                {{ number_format($tier->cost_gross, 2) }} €
                            </td>
                            <td class="px-4 py-2">
                                {{ $tier->tax->name }} ({{ $tier->tax->percentage }}%)
                            </td>
                            <td class="px-4 py-2">
                                {{ $tier->active ? 'Yes' : 'No' }}
                            </td>
                            <td class="px-4 py-2 text-right space-x-2">
                                <a href="{{ route('admin.shipping-tiers.edit', $tier) }}"
                                   class="text-blue-600 hover:underline">
                                    Edit
                                </a>

                                <form method="POST"
                                      action="{{ route('admin.shipping-tiers.destroy', $tier) }}"
                                      class="inline"
                                      onsubmit="return confirm('Delete this tier?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-red-600 hover:underline">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6"
                                class="px-4 py-6 text-center text-gray-500">
                                No shipping tiers found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
