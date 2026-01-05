<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Taxes</h2>
    </x-slot>

    <div class="py-6 max-w-5xl mx-auto sm:px-6 lg:px-8">
        <a href="{{ route('admin.taxes.create') }}"
           class="bg-blue-600 text-white px-4 py-2 rounded mb-4 inline-block">
            New Tax
        </a>

        <div class="bg-white shadow rounded">
            <table class="min-w-full border">
                <thead class="bg-gray-100">
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
                            <td class="px-4 py-2">{{ $tax->name }}</td>
                            <td class="px-4 py-2">{{ $tax->percentage }}</td>
                            <td class="px-4 py-2">{{ $tax->is_active ? 'Yes' : 'No' }}</td>
                            <td class="px-4 py-2 text-right">
                                <a href="{{ route('admin.taxes.edit', $tax) }}"
                                   class="text-blue-600 hover:underline">
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
