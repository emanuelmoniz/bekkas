<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-grey-dark leading-tight">
            Create Category
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <form method="POST" action="{{ route('admin.categories.store') }}">
                @csrf

                {{-- TRANSLATIONS --}}
                <div class="bg-white p-6 rounded shadow mb-6">
                    <h3 class="font-semibold mb-4">Translations</h3>

                    @foreach (\App\Models\Locale::activeList() as $locale => $label)
                        <div class="border p-4 mb-4">
                            <h4 class="font-medium mb-2">{{ $label }}</h4>

                            <input type="text"
                                   name="name[{{ $locale }}]"
                                   placeholder="Category name"
                                   class="w-full border rounded px-3 py-2"
                                   required>
                        </div>
                    @endforeach
                </div>

                {{-- PARENT CATEGORY --}}
                <div class="bg-white p-6 rounded shadow mb-6">
                    <h3 class="font-semibold mb-4">Parent Category</h3>

                    <select name="parent_id" class="border rounded px-3 py-2 w-full">
                        <option value="">— None —</option>

                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">
                                {{ optional($category->translation())->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <button type="submit"
                    class="bg-accent-primary text-light px-6 py-2 rounded">
                        Create Category
                </button>

            </form>

        </div>
    </div>
</x-app-layout>
