<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-grey-dark">
            Regions
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-4 flex justify-end">
            <x-default-button type="button" onclick="window.location.href='{{ route('admin.regions.create') }}'">
                New Region
            </x-default-button>
        </div>

        {{-- FILTERS --}}
        <form method="GET" class="mb-6 bg-white p-4 rounded shadow">
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-4">

                {{-- NAME --}}
                <input type="text"
                       name="name"
                       value="{{ request('name') }}"
                       placeholder="Name"
                       class="border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">

                {{-- COUNTRY --}}
                <select name="country_id" class="border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">
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
                       class="border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">

                {{-- IS_ACTIVE --}}
                <select name="is_active" class="border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">
                    <option value="">Active</option>
                    <option value="1" @selected(request('is_active')==='1')>Yes</option>
                    <option value="0" @selected(request('is_active')==='0')>No</option>
                </select>

                {{-- ACTIONS --}}
                <div class="flex justify-end gap-2">
                    <x-default-button type="button" onclick="window.location.href='{{ route('admin.regions.index') }}'">
                        Reset
                    </x-default-button>
                    <x-default-button type="submit">
                        Filter
                    </x-default-button>
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
                                <a href="{{ route('admin.regions.show', $region) }}" class="font-medium text-accent-primary hover:text-accent-primary/90 no-underline">
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
                                <x-default-button type="button" onclick="window.location.href='{{ route('admin.regions.edit', $region) }}'">
                                    Edit
                                </x-default-button>

                                <form method="POST"
                                      action="{{ route('admin.regions.destroy', $region) }}"
                                      class="inline"
                                      onsubmit="return confirm('Delete this region?')">
                                    @csrf
                                    @method('DELETE')
                                    <x-default-button type="submit">
                                        Delete
                                    </x-default-button>
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
