<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-grey-dark">Order Statuses</h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-4 flex justify-end">
            <a href="{{ route('admin.order-statuses.create') }}"
               class="bg-accent-primary hover:bg-accent-primary/90 text-light px-4 py-2 rounded">
                Create Order Status
            </a>
        </div>

        <div class="bg-light shadow rounded">
            <table class="min-w-full border">
                <thead class="bg-grey-light">
                    <tr>
                        <th class="px-4 py-2 text-left">Code</th>
                        <th class="px-4 py-2 text-left">Name (pt-PT)</th>
                        <th class="px-4 py-2 text-left">Name (en-UK)</th>
                        <th class="px-4 py-2 text-left">Sort Order</th>
                        <th class="px-4 py-2"></th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($statuses as $status)
                        <tr class="border-t">
                            <td class="px-4 py-2">{{ $status->code }}</td>
                            <td class="px-4 py-2">
                                {{ optional($status->translations->firstWhere('locale', 'pt-PT'))->name }}
                                <x-missing-locale-badge :model="$status" />
                            </td>
                            <td class="px-4 py-2">
                                {{ optional($status->translations->firstWhere('locale', 'en-UK'))->name }}
                            </td>
                            <td class="px-4 py-2">{{ $status->sort_order }}</td>
                            <td class="px-4 py-2 text-right space-x-2">
                                <a href="{{ route('admin.order-statuses.edit', $status) }}"
                                   class="text-accent-secondary hover:underline">
                                    Edit
                                </a>
                                <form action="{{ route('admin.order-statuses.destroy', $status) }}"
                                      method="POST"
                                      class="inline"
                                      onsubmit="return confirm('Are you sure?')">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="text-grey-dark hover:underline">
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
