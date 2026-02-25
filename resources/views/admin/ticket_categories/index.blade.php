<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-grey-dark leading-tight">
            Ticket Categories
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <!-- page-level flash removed — use global flash (session('error') will render in layout) -->

            <div class="mb-4 flex justify-end">
                <a href="{{ route('admin.ticket-categories.create') }}"
                   class="bg-accent-primary hover:bg-accent-primary/90 text-light px-4 py-2 rounded">
                    New Category
                </a>
            </div>

            <div class="bg-white shadow rounded">
                <table class="min-w-full border">
                    <thead class="bg-grey-light">
                        <tr>
                            <th class="px-4 py-2 text-left">Name</th>
                            <th class="px-4 py-2 text-center">Status</th>
                            <th class="px-4 py-2 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($categories as $category)
                            <tr class="border-t">
                                <td class="px-4 py-2">
                                    {{ optional($category->translation())->name }}
                                </td>
                                <td class="px-4 py-2 text-center">
                                    @if ($category->active)
                                        <span class="text-status-success font-semibold">Active</span>
                                    @else
                                        <span class="text-status-error font-semibold">Inactive</span>
                                    @endif
                                </td>
                                <td class="px-4 py-2 text-right space-x-2">
                                    <a href="{{ route('admin.ticket-categories.edit', $category) }}"
                                       class="bg-accent-primary hover:bg-accent-primary/90 text-light px-3 py-1 rounded text-sm">
                                        Edit
                                    </a>

                                    <form method="POST"
                                          action="{{ route('admin.ticket-categories.destroy', $category) }}"
                                          class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit"
                                                onclick="return confirm('Delete this category?')"
                                                class="bg-grey-light hover:bg-grey-light/90 text-grey-dark px-3 py-1 rounded text-sm">
                                            Delete
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" class="px-4 py-6 text-center text-grey-medium">
                                    No categories found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

        </div>
    </div>
</x-app-layout>
