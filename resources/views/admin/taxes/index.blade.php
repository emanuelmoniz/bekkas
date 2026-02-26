<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-grey-dark">Taxes</h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-4 flex justify-end">
            <a href="{{ route('admin.taxes.create') }}"
               class="bg-primary hover:bg-primary/90 text-white px-8 py-3 rounded-full uppercase">
                New Tax
            </a>
        </div>

        <form method="GET" class="mb-6 bg-white p-4 rounded shadow">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <input type="text" name="name" value="{{ request('name') }}" placeholder="Name"
                       class="border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">
                <select name="active" class="border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">
                    <option value="">Active (all)</option>
                    <option value="1" @selected(request('active') === '1')>Yes</option>
                    <option value="0" @selected(request('active') === '0')>No</option>
                </select>
                <div class="flex gap-2">
                    <a href="{{ route('admin.taxes.index') }}"
                       class="bg-grey-medium hover:bg-grey-dark text-white px-8 py-3 rounded-full uppercase">Reset</a>
                    <button class="bg-primary hover:bg-primary/90 text-white px-8 py-3 rounded-full uppercase">Filter</button>
                </div>
            </div>
        </form>

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
                                <a href="{{ route('admin.taxes.show', $tax) }}" class="text-accent-secondary hover:underline font-medium">{{ $tax->name }}</a>
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
                                   class="inline-flex items-center px-3 py-1 rounded bg-primary text-white text-sm">
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
