<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-grey-dark">Order Statuses</h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-4 flex justify-end">
            <button type="submit" type="button" onclick="window.location.href='{{ route('admin.order-statuses.create') }}'"
        class="bg-primary hover:bg-primary/90 text-white px-2 py-2 rounded uppercase text-sm">
                Create Order Status
            </button>
        </div>

        <form method="GET" class="mb-6 bg-white p-4 rounded shadow">
            <div class="grid grid-cols-1 md:grid-cols-3 gap-4">
                <input type="text" name="name" value="{{ request('name') }}" placeholder="Name"
                       class="border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">
                <input type="text" name="code" value="{{ request('code') }}" placeholder="Code"
                       class="border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="window.location.href='{{ route('admin.order-statuses.index') }}'"
        class="bg-grey-light hover:bg-grey-medium text-grey-dark px-2 py-2 rounded uppercase text-sm">Reset</button>
                    <button type="submit" class="bg-primary hover:bg-primary/90 text-white px-2 py-2 rounded uppercase text-sm">Filter</button>
                </div>
            </div>
        </form>

        <div class="bg-white shadow rounded">
            <table class="min-w-full border">
                <thead class="bg-grey-light">
                    <tr>
                        <th class="px-4 py-2 text-left">Code</th>
                        <th class="px-4 py-2 text-left">Name</th>
                        <th class="px-4 py-2 text-left">Sort Order</th>
                        <th class="px-4 py-2"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($statuses as $status)
                        <tr class="border-t">
                            <td class="px-4 py-2">{{ $status->code }}</td>
                            <td class="px-4 py-2">
                                {{ optional($status->translations->firstWhere('locale', 'en-UK'))->name }}
                                <x-missing-locale-badge :model="$status" />
                            </td>
                            <td class="px-4 py-2">{{ $status->sort_order }}</td>
                            <td class="px-4 py-2 text-right space-x-2">
                                <button type="button"
                                   onclick="window.location.href='{{ route('admin.order-statuses.edit', $status) }}'"
                                   class="inline-flex items-center px-2 py-2 rounded bg-primary text-white text-sm uppercase">
                                    Edit
                                </button>
                                <form action="{{ route('admin.order-statuses.destroy', $status) }}"
                                      method="POST"
                                      class="inline"
                                      onsubmit="return confirm('Are you sure?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="inline-flex items-center px-2 py-2 rounded uppercase bg-status-error/10 text-status-error text-sm">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
