<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-grey-dark">
            Shipping Tiers
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-4 flex justify-end">
            <a href="{{ route('admin.shipping-tiers.create') }}"
               class="bg-accent-primary hover:bg-accent-primary/90 text-light px-4 py-2 rounded">
                New Shipping Tier
            </a>
        </div>

        {{-- FILTERS --}}
        <form method="GET" class="mb-6 bg-white p-4 rounded shadow">
            <div class="grid grid-cols-1 md:grid-cols-5 gap-4">

                {{-- NAME --}}
                <input type="text"
                       name="name"
                       value="{{ request('name') }}"
                       placeholder="Name"
                       class="border-grey-medium focus:border-accent-primary focus:ring-accent-primary rounded-md shadow-sm">

                {{-- REGION --}}
                <select name="region_id" class="border-grey-medium focus:border-accent-primary focus:ring-accent-primary rounded-md shadow-sm">
                    <option value="">All Regions</option>
                    @foreach ($regions as $region)
                        <option value="{{ $region->id }}" @selected(request('region_id') == $region->id)>
                            {{ $region->name }}
                        </option>
                    @endforeach
                </select>

                {{-- COUNTRY --}}
                <select name="country_id" class="border-grey-medium focus:border-accent-primary focus:ring-accent-primary rounded-md shadow-sm">
                    <option value="">All Countries</option>
                    @foreach ($countries as $country)
                        <option value="{{ $country->id }}" @selected(request('country_id') == $country->id)>
                            {{ $country->name }}
                        </option>
                    @endforeach
                </select>

                {{-- POSTAL CODE --}}
                <input type="text"
                       name="postal_code"
                       value="{{ request('postal_code') }}"
                       placeholder="Postal Code"
                       class="border-grey-medium focus:border-accent-primary focus:ring-accent-primary rounded-md shadow-sm">

                {{-- ACTIONS --}}
                <div class="flex gap-2">
                    <a href="{{ route('admin.shipping-tiers.index') }}"
                       class="bg-grey-medium hover:bg-grey-dark text-light px-4 py-2 rounded">
                        Reset
                    </a>
                    <button class="bg-accent-primary hover:bg-accent-primary/90 text-light px-4 py-2 rounded">
                        Filter
                    </button>
                </div>
            </div>
        </form>

        {{-- TABLE --}}
        <div class="bg-white shadow rounded">
            <table class="min-w-full border">
                <thead class="bg-grey-light">
                    <tr>
                        <th class="px-4 py-2 text-left">Name</th>
                        <th class="px-4 py-2 text-left">Weight From (g)</th>
                        <th class="px-4 py-2 text-left">Weight To (g)</th>
                        <th class="px-4 py-2 text-left">Cost (gross)</th>
                        <th class="px-4 py-2 text-left">Shipping Days</th>
                        <th class="px-4 py-2 text-left">Countries</th>
                        <th class="px-4 py-2 text-left">Regions</th>
                        <th class="px-4 py-2 text-left">Active</th>
                        <th class="px-4 py-2 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tiers as $tier)
                        <tr class="border-t">
                            <td class="px-4 py-2">
                                <a href="{{ route('admin.shipping-tiers.show', $tier) }}" class="text-accent-secondary hover:underline font-medium">{{ $tier->translation()?->name }}</a>
                                <x-missing-locale-badge :model="$tier" />
                            </td>
                            <td class="px-4 py-2">{{ $tier->weight_from }}</td>
                            <td class="px-4 py-2">{{ $tier->weight_to }}</td>
                            <td class="px-4 py-2">
                                {{ number_format($tier->cost_gross, 2) }} €
                            </td>
                            <td class="px-4 py-2">{{ $tier->shipping_days }}</td>
                            <td class="px-4 py-2">
                                <span class="text-xs">{{ $tier->countries->count() }}</span>
                            </td>
                            <td class="px-4 py-2">
                                <span class="text-xs">{{ $tier->regions->count() }}</span>
                            </td>
                            <td class="px-4 py-2">
                                @if($tier->active)
                                    <span class="text-status-success font-bold">&#10003;</span>
                                @else
                                    <span class="text-status-error font-bold">&#10007;</span>
                                @endif
                            </td>
                            <td class="px-4 py-2 text-right space-x-2">
                                <a href="{{ route('admin.shipping-tiers.edit', $tier) }}"
                                   class="inline-flex items-center px-3 py-1 rounded bg-accent-primary text-light text-sm">
                                    Edit
                                </a>

                                <form method="POST"
                                      action="{{ route('admin.shipping-tiers.duplicate', $tier) }}"
                                      class="inline">
                                    @csrf
                                    <button class="inline-flex items-center px-3 py-1 rounded bg-grey-light text-grey-dark text-sm">
                                        Duplicate
                                    </button>
                                </form>

                                <form method="POST"
                                      action="{{ route('admin.shipping-tiers.destroy', $tier) }}"
                                      class="inline"
                                      onsubmit="return confirm('Delete this tier?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="inline-flex items-center px-3 py-1 rounded bg-status-error/10 text-status-error text-sm">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9"
                                class="px-4 py-6 text-center text-grey-medium">
                                No shipping tiers found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $tiers->links() }}
        </div>
    </div>
</x-app-layout>
