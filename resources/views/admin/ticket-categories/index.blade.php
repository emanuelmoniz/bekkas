<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Ticket Categories</h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-4 flex justify-end">
            <a href="{{ route('admin.ticket-categories.create') }}"
               class="bg-accent-primary text-light px-4 py-2 rounded">
                New Category
            </a>
        </div>

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
                                {{ optional($cat->translations->where('locale','en-UK')->first())->name }}
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
                                <a href="{{ route('admin.ticket-categories.edit', $cat) }}"
                                   class="inline-flex items-center px-3 py-1 rounded bg-accent-primary text-light text-sm">
                                    Edit
                                </a>

                                <form method="POST"
                                      action="{{ route('admin.ticket-categories.destroy', $cat) }}"
                                      class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="inline-flex items-center px-3 py-1 rounded bg-status-error/10 text-status-error text-sm"
                                            onclick="return confirm('Delete category?')">
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
