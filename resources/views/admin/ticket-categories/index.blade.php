<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Ticket Categories</h2>
    </x-slot>

    <div class="py-6 max-w-5xl mx-auto">
        <div class="mb-4 text-right">
            <a href="{{ route('admin.ticket-categories.create') }}"
               class="bg-accent-primary text-light px-4 py-2 rounded">
                New Category
            </a>
        </div>

        <div class="bg-light shadow rounded">
            <table class="min-w-full border">
                <thead class="bg-grey-light">
                    <tr>
                        <th class="px-4 py-2 text-left">Name (PT)</th>
                        <th class="px-4 py-2 text-left">Name (EN)</th>
                        <th class="px-4 py-2 text-left">Active</th>
                        <th class="px-4 py-2 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($categories as $cat)
                        <tr class="border-t">
                            <td class="px-4 py-2">
                                {{ optional($cat->translations->where('locale','pt-PT')->first())->name }}
                                <x-missing-locale-badge :model="$cat" />
                            </td>
                            <td class="px-4 py-2">
                                {{ optional($cat->translations->where('locale','en-UK')->first())->name }}
                            </td>
                            <td class="px-4 py-2">
                                {{ $cat->active ? 'Yes' : 'No' }}
                            </td>
                            <td class="px-4 py-2 text-right">
                                <a href="{{ route('admin.ticket-categories.edit', $cat) }}"
                                   class="text-accent-secondary hover:underline">
                                    Edit
                                </a>

                                <form method="POST"
                                      action="{{ route('admin.ticket-categories.destroy', $cat) }}"
                                      class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-grey-dark ml-2"
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
