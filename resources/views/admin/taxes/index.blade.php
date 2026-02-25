<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-grey-dark">Taxes</h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-4 flex justify-end">
            <a href="{{ route('admin.taxes.create') }}"
               class="bg-accent-primary hover:bg-accent-primary/90 text-light px-4 py-2 rounded">
                New Tax
            </a>
        </div>

        <div class="bg-white shadow rounded">
            <table class="min-w-full border">
                <thead class="bg-grey-light">
                    <tr>
                        <th class="px-4 py-2">Name</th>
                        <th class="px-4 py-2">%</th>
                        <th class="px-4 py-2">Active</th>
                        <th class="px-4 py-2"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($taxes as $tax)
                        <tr class="border-t">
                            <td class="px-4 py-2">
                                {{ $tax->name }}
                                <x-missing-locale-badge :model="$tax" />
                            </td>
                            <td class="px-4 py-2">{{ $tax->percentage }}</td>
                            <td class="px-4 py-2">
                                @if($tax->is_active)
                                    <span class="text-status-success font-bold">&#10003;</span>
                                @else
                                    <span class="text-status-error font-bold">&#10007;</span>
                                @endif
                            </td>
                            <td class="px-4 py-2 text-right">
                                <a href="{{ route('admin.taxes.edit', $tax) }}"
                                   class="inline-flex items-center px-3 py-1 rounded bg-accent-primary text-light text-sm">
                                    Edit
                                </a>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
