<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-grey-dark">
            Countries
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-4 flex justify-end">
            <button type="submit" type="button" onclick="window.location.href='{{ route('admin.countries.create') }}'"
        class="bg-primary hover:bg-primary/90 text-white px-2 py-2 rounded uppercase text-sm">
                New Country
            </button>
        </div>

        <form method="GET" class="mb-6 bg-white p-4 rounded shadow">
            <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                <input type="text" name="name" value="{{ request('name') }}" placeholder="Name"
                       class="border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">
                <input type="text" name="iso_alpha_2" value="{{ request('iso_alpha_2') }}" placeholder="ISO Alpha-2"
                       class="border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">
                <select name="active" class="border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">
                    <option value="">Active (all)</option>
                    <option value="1" @selected(request('active') === '1')>Yes</option>
                    <option value="0" @selected(request('active') === '0')>No</option>
                </select>
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="window.location.href='{{ route('admin.countries.index') }}'"
        class="bg-grey-light hover:bg-grey-medium text-grey-dark px-2 py-2 rounded uppercase text-sm">Reset</button>
                    <button type="submit" class="bg-primary hover:bg-primary/90 text-white px-2 py-2 rounded uppercase text-sm">Filter</button>
                </div>
            </div>
        </form>

        <div class="bg-white shadow rounded">
            <table class="min-w-full border">
                <thead class="bg-grey-light">
                    <tr>
                        <th class="px-4 py-2 text-left">Name</th>
                        <th class="px-4 py-2 text-left">ISO Alpha-2</th>
                        <th class="px-4 py-2 text-left">Country Code</th>
                        <th class="px-4 py-2 text-left">Active</th>
                        <th class="px-4 py-2 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($countries as $country)
                        <tr class="border-t">
                            <td class="px-4 py-2">
                                <a href="{{ route('admin.countries.show', $country) }}" class="text-accent-secondary hover:underline font-medium">{{ $country->translation('en-UK')?->name ?? '—' }}</a>
                                <x-missing-locale-badge :model="$country" />
                            </td>
                            <td class="px-4 py-2">{{ $country->iso_alpha2 }}</td>
                            <td class="px-4 py-2">{{ $country->country_code }}</td>
                            <td class="px-4 py-2">
                                @if($country->is_active)
                                    <span class="text-status-success font-bold">&#10003;</span>
                                @else
                                    <span class="text-status-error font-bold">&#10007;</span>
                                @endif
                            </td>
                            <td class="px-4 py-2 text-right space-x-2">
                                <button type="button"
                                   onclick="window.location.href='{{ route('admin.countries.edit', $country) }}'"
                                   class="inline-flex items-center px-2 py-2 rounded bg-primary text-white text-sm uppercase">
                                    Edit
                                </button>

                                <form method="POST"
                                      action="{{ route('admin.countries.destroy', $country) }}"
                                      class="inline"
                                      onsubmit="return confirm('Delete this country?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="inline-flex items-center px-2 py-2 rounded uppercase bg-status-error/10 text-status-error text-sm">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5"
                                class="px-4 py-6 text-center text-grey-medium">
                                No countries found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>

