<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Ticket Categories</h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-4 flex justify-end">
            <a href="{{ route('admin.ticket-categories.create') }}"
               class="bg-primary text-white px-8 py-3 rounded-full uppercase">
                New Category
            </a>
        </div>

        <form method="GET" class="mb-6 bg-white p-4 rounded shadow">
            <div class="flex flex-wrap gap-4">
                <input type="text" name="name" value="{{ request('name') }}" placeholder="Name"
                       class="border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">
                <div class="flex gap-2 ml-auto">
                    <a href="{{ route('admin.ticket-categories.index') }}"
                       class="bg-grey-medium hover:bg-grey-dark text-white px-8 py-3 rounded-full uppercase">Reset</a>
                    <button class="bg-primary hover:bg-primary/90 text-white px-8 py-3 rounded-full uppercase">Filter</button>
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
                                <a href="{{ route('admin.ticket-categories.show', $cat) }}" class="text-accent-secondary hover:underline font-medium">{{ optional($cat->translations->where('locale','en-UK')->first())->name }}</a>
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
                                   class="inline-flex items-center px-3 py-1 rounded bg-primary text-white text-sm">
                                    Edit
                                </a>

                                <form method="POST"
                                      action="{{ route('admin.ticket-categories.destroy', $cat) }}"
                                      class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="inline-flex items-center px-8 py-3 rounded-full uppercase bg-status-error/10 text-status-error text-sm"
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
