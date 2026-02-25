<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-grey-dark">Locales</h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">

        @if (session('success'))
            <div class="mb-4 px-4 py-2 bg-status-success/10 text-status-success rounded">
                {{ session('success') }}
            </div>
        @endif

        <div class="mb-4 flex justify-end">
            <a href="{{ route('admin.locales.create') }}"
               class="bg-accent-primary hover:bg-accent-primary/90 text-light px-4 py-2 rounded">
                Add Locale
            </a>
        </div>

        <div class="bg-light shadow rounded">
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
                            <td class="px-4 py-2">{{ $loc->name }}</td>
                            <td class="px-4 py-2 text-xl">{{ $loc->flag_emoji }}</td>
                            <td class="px-4 py-2">{{ optional($loc->country)->name_en }}</td>
                            <td class="px-4 py-2">
                                @if ($loc->is_active)
                                    <span class="text-status-success font-semibold">Yes</span>
                                @else
                                    <span class="text-grey-medium">No</span>
                                @endif
                            </td>
                            <td class="px-4 py-2">
                                @if ($loc->is_default)
                                    <span class="text-accent-primary font-semibold">Yes</span>
                                @endif
                            </td>
                            <td class="px-4 py-2 text-right space-x-2">
                                <a href="{{ route('admin.locales.edit', $loc) }}"
                                   class="text-accent-secondary hover:underline">Edit</a>
                                <form action="{{ route('admin.locales.destroy', $loc) }}"
                                      method="POST" class="inline"
                                      onsubmit="return confirm('Delete this locale?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-grey-dark hover:underline">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
