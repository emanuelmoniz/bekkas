<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-grey-dark leading-tight">
            Categories
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- ACTION BAR --}}
            <div class="mb-4 flex justify-end">
                <a href="{{ route('admin.categories.create') }}"
                   class="inline-flex items-center bg-accent-primary hover:bg-accent-primary/90 text-light font-semibold px-4 py-2 rounded">
                    New Category
                </a>
            </div>

            {{-- TABLE --}}
            <div class="bg-light shadow rounded">
                <table class="min-w-full border">
                    <thead class="bg-grey-light">
                        <tr>
                            <th class="px-4 py-2 text-left">Name</th>
                            <th class="px-4 py-2 text-left">Parent</th>
                            <th class="px-4 py-2 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($categories as $category)
                            <tr class="border-t">
                                <td class="px-4 py-2">
                                    {{ optional($category->translation())->name }}
                                    <x-missing-locale-badge :model="$category" />
                                </td>

                                <td class="px-4 py-2 text-sm text-grey-dark">
                                    {{ optional(optional($category->parent)->translation())->name ?? '—' }}
                                </td>

                                <td class="px-4 py-2 text-right space-x-2">
                                    <a href="{{ route('admin.categories.edit', $category) }}"
                                       class="inline-flex bg-accent-primary hover:bg-accent-primary/90 text-light px-3 py-1 rounded text-sm">
                                        Edit
                                    </a>

                                    <form method="POST"
                                          action="{{ route('admin.categories.destroy', $category) }}"
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
