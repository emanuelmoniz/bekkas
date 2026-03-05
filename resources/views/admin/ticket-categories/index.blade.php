<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Ticket Categories</h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-4 flex justify-end">
            <x-default-button type="button" onclick="window.location.href='{{ route('admin.ticket-categories.create') }}'">
                New Category
            </x-default-button>
        </div>

        <form method="GET" class="mb-6 bg-white p-4 rounded shadow">
            <div class="flex items-center gap-4">
                <input type="text" name="name" value="{{ request('name') }}" placeholder="Name"
                       class="flex-1 border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">
                <div class="flex gap-2">
                    <x-default-button type="button" onclick="window.location.href='{{ route('admin.ticket-categories.index') }}'">Reset</x-default-button>
                    <x-default-button type="submit">Filter</x-default-button>
                </div>
            </div>
        </form>

        <div class="bg-white shadow rounded">
            <table class="min-w-full border">
                <thead class="bg-grey-light">
                    <tr>
                        <th class="px-4 py-2 text-left">Name</th>
                        <th class="px-4 py-2 text-left">Active</th>
                        <th class="px-4 py-2 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($categories as $cat)
                        <tr class="border-t">
                            <td class="px-4 py-2">
                                <a href="{{ route('admin.ticket-categories.show', $cat) }}" class="font-medium text-accent-primary hover:text-accent-primary/90 no-underline">{{ optional($cat->translations->where('locale','en-UK')->first())->name }}</a>
                                <x-missing-locale-badge :model="$cat" />
                            </td>
                            <td class="px-4 py-2">
                                @if($cat->active)
                                    <span class="text-status-success font-bold">&#10003;</span>
                                @else
                                    <span class="text-status-error font-bold">&#10007;</span>
                                @endif
                            </td>
                            <td class="px-4 py-2 text-right">
                                <x-default-button type="button" onclick="window.location.href='{{ route('admin.ticket-categories.edit', $cat) }}'">
                                    Edit
                                </x-default-button>

                                <form method="POST"
                                      action="{{ route('admin.ticket-categories.destroy', $cat) }}"
                                      class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <x-default-button type="submit" onclick="return confirm('Delete category?')">
                                        Delete
                                    </x-default-button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
