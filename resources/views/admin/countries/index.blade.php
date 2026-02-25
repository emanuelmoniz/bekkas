<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-grey-dark">
            Countries
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-4 flex justify-end">
            <a href="{{ route('admin.countries.create') }}"
               class="bg-accent-primary hover:bg-accent-primary/90 text-light px-4 py-2 rounded">
                New Country
            </a>
        </div>

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
                                {{ $country->translation('en-UK')?->name ?? '—' }}
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
                                <a href="{{ route('admin.countries.edit', $country) }}"
                                   class="inline-flex items-center px-3 py-1 rounded bg-accent-primary text-light text-sm">
                                    Edit
                                </a>

                                <form method="POST"
                                      action="{{ route('admin.countries.destroy', $country) }}"
                                      class="inline"
                                      onsubmit="return confirm('Delete this country?')">
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

