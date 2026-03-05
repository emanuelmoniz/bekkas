<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-grey-dark">Locales</h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">

        <div class="mb-4 flex justify-end">
            <x-default-button type="button" onclick="window.location.href='{{ route('admin.locales.create') }}'">
                Add Locale
            </x-default-button>
        </div>

        <form method="GET" class="mb-6 bg-white p-4 rounded shadow">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
                <input type="text" name="code" value="{{ request('code') }}" placeholder="Code"
                       class="border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">
                <input type="text" name="name" value="{{ request('name') }}" placeholder="Name"
                       class="border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">
                <select name="active" class="border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">
                    <option value="">Active (all)</option>
                    <option value="1" @selected(request('active') === '1')>Yes</option>
                    <option value="0" @selected(request('active') === '0')>No</option>
                </select>
                <div class="flex justify-end gap-2">
                    <x-default-button type="button" onclick="window.location.href='{{ route('admin.locales.index') }}'">Reset</x-default-button>
                    <x-default-button type="submit">Filter</x-default-button>
                </div>
            </div>
        </form>

        <div class="bg-white shadow rounded">
            <table class="min-w-full border">
                <thead class="bg-grey-light">
                    <tr>
                        <th class="px-4 py-2 text-left">Code</th>
                        <th class="px-4 py-2 text-left">Name</th>
                        <th class="px-4 py-2 text-left">Flag</th>
                        <th class="px-4 py-2 text-left">Country</th>
                        <th class="px-4 py-2 text-left">Active</th>
                        <th class="px-4 py-2 text-left">Default</th>
                        <th class="px-4 py-2"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($locales as $loc)
                        <tr class="border-t">
                            <td class="px-4 py-2 font-mono text-sm">{{ $loc->code }}</td>
                            <td class="px-4 py-2"><a href="{{ route('admin.locales.show', $loc) }}" class="font-medium text-accent-primary hover:text-accent-primary/90 no-underline">{{ $loc->name }}</a></td>
                            <td class="px-4 py-2 text-xl">{{ $loc->flag_emoji }}</td>
                            <td class="px-4 py-2">{{ optional($loc->country)->name }}</td>
                            <td class="px-4 py-2">
                                @if ($loc->is_active)
                                    <span class="text-status-success font-bold">&#10003;</span>
                                @else
                                    <span class="text-status-error font-bold">&#10007;</span>
                                @endif
                            </td>
                            <td class="px-4 py-2">
                                @if ($loc->is_default)
                                    <span class="text-status-success font-bold">&#10003;</span>
                                @else
                                    <span class="text-status-error font-bold">&#10007;</span>
                                @endif
                            </td>
                            <td class="px-4 py-2 text-right space-x-2">
                                <x-default-button type="button" onclick="window.location.href='{{ route('admin.locales.edit', $loc) }}'">
Edit</x-default-button>
                                <form action="{{ route('admin.locales.destroy', $loc) }}"
                                      method="POST" class="inline"
                                      onsubmit="return confirm('Delete this locale?')">
                                    @csrf
                                    @method('DELETE')
                                    <x-default-button type="submit">Delete</x-default-button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
