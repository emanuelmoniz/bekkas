<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-grey-dark">
            Regions
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-4 flex justify-end">
            <a href="{{ route('admin.regions.create') }}"
               class="bg-accent-primary hover:bg-accent-primary/90 text-light px-4 py-2 rounded">
                New Region
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
                       class="border rounded px-3 py-2">

                {{-- COUNTRY --}}
                <select name="country_id" class="border rounded px-3 py-2">
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
                       class="border rounded px-3 py-2">

                {{-- IS_ACTIVE --}}
                <select name="is_active" class="border rounded px-3 py-2">
                    <option value="">Active</option>
                    <option value="1" @selected(request('is_active')==='1')>Yes</option>
                    <option value="0" @selected(request('is_active')==='0')>No</option>
                </select>

                {{-- ACTIONS --}}
                <div class="flex gap-2">
                    <a href="{{ route('admin.regions.index') }}"
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
                        <th class="px-4 py-2 text-left">Country</th>
                        <th class="px-4 py-2 text-left">Postal Code From</th>
                        <th class="px-4 py-2 text-left">Postal Code To</th>
                        <th class="px-4 py-2 text-left">Active</th>
                        <th class="px-4 py-2 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($regions as $region)
                        <tr class="border-t">
                            <td class="px-4 py-2">
                                <a href="{{ route('admin.regions.show', $region) }}" class="text-accent-secondary hover:underline font-medium">
                                    {{ $region->name }}
                                </a>
                                <x-missing-locale-badge :model="$region" />
                            </td>
                            <td class="px-4 py-2">
                                {{ $region->country?->name }}
                            </td>
                            <td class="px-4 py-2">{{ $region->postal_code_from }}</td>
                            <td class="px-4 py-2">{{ $region->postal_code_to }}</td>
                            <td class="px-4 py-2">
                                @if($region->is_active)
                                    <span class="text-status-success font-bold">&#10003;</span>
                                @else
                                    <span class="text-status-error font-bold">&#10007;</span>
                                @endif
                            </td>
                            <td class="px-4 py-2 text-right space-x-2">
                                <a href="{{ route('admin.regions.edit', $region) }}"
                                   class="inline-flex items-center px-3 py-1 rounded bg-accent-primary text-light text-sm">
                                    Edit
                                </a>

                                <form method="POST"
                                      action="{{ route('admin.regions.destroy', $region) }}"
                                      class="inline"
                                      onsubmit="return confirm('Delete this region?')">
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
                            <td colspan="6"
                                class="px-4 py-6 text-center text-grey-medium">
                                No regions found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $regions->links() }}
        </div>
    </div>
</x-app-layout>
