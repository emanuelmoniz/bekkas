<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-grey-dark leading-tight">
            Categories
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">

        {{-- ACTION BAR --}}
        <div class="mb-4 flex justify-end">
            <button type="button" onclick="window.location.href='{{ route('admin.categories.create') }}'"
        class="inline-flex items-center bg-primary hover:bg-primary/90 text-white px-2 py-2 rounded uppercase text-sm">
                New Category
            </button>
        </div>

        {{-- FILTERS --}}
        <form method="GET" class="mb-6 bg-white p-4 rounded shadow">
            <div class="flex items-center gap-4">
                <input type="text" name="name" value="{{ request('name') }}" placeholder="Name"
                       class="flex-1 border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">
                <div class="flex gap-2">
                    <button type="button" onclick="window.location.href='{{ route('admin.categories.index') }}'"
        class="bg-grey-light hover:bg-grey-medium text-grey-dark px-2 py-2 rounded uppercase text-sm">Reset</button>
                    <button type="submit" class="bg-primary hover:bg-primary/90 text-white px-2 py-2 rounded uppercase text-sm">Filter</button>
                </div>
            </div>
        </form>

        {{-- TABLE --}}
        <div class="bg-white shadow rounded">
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
                                <a href="{{ route('admin.categories.show', $category) }}" class="text-accent-secondary hover:underline font-medium">{{ optional($category->translation())->name }}</a>
                                <x-missing-locale-badge :model="$category" />
                            </td>

                            <td class="px-4 py-2 text-sm text-grey-dark">
                                {{ optional(optional($category->parent)->translation())->name ?? '—' }}
                            </td>

                            <td class="px-4 py-2 text-right space-x-2">
                                <button type="button" onclick="window.location.href='{{ route('admin.categories.edit', $category) }}'"
        class="inline-flex bg-primary hover:bg-primary/90 text-white px-2 py-2 rounded uppercase text-sm">
                                    Edit
                                </button>

                                <form method="POST"
                                      action="{{ route('admin.categories.destroy', $category) }}"
                                      class="inline">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            onclick="return confirm('Delete this category?')"
                                            class="bg-grey-light hover:bg-grey-light/90 text-grey-dark px-2 py-2 rounded uppercase text-sm">
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
</x-app-layout>
