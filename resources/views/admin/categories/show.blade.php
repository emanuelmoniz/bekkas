<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-grey-dark leading-tight">Category Details</h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">

        <div class="bg-white shadow rounded p-6 mb-6">
            <h3 class="text-xs text-grey-dark font-medium uppercase tracking-widest mb-4">Basic Information</h3>
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-xs text-grey-dark font-medium uppercase tracking-widest">Parent Category</p>
                    <p class="text-sm text-grey-dark mt-1">
                        @if($category->parent)
                            <a href="{{ route('admin.categories.show', $category->parent) }}" class="text-accent-secondary hover:underline">
                                {{ optional($category->parent->translation())->name ?? '—' }}
                            </a>
                        @else
                            —
                        @endif
                    </p>
                </div>
            </dl>
        </div>

        <div class="bg-white shadow rounded p-6 mb-6">
            <h3 class="text-xs text-grey-dark font-medium uppercase tracking-widest mb-4">Translations</h3>
            <div class="space-y-4">
                @foreach($category->translations as $translation)
                    <div class="border border-grey-light rounded p-4">
                        <p class="text-xs text-grey-dark font-medium uppercase tracking-widest mb-2">{{ $translation->locale }}</p>
                        <div>
                            <p class="text-xs text-grey-medium uppercase">Name</p>
                            <p class="text-sm text-grey-dark mt-1">{{ $translation->name ?: '—' }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="flex justify-between mt-6">
            <a href="{{ route('admin.categories.index') }}"
               class="inline-flex items-center px-4 py-2 bg-white border border-grey-medium rounded-full font-semibold text-xs text-grey-dark uppercase tracking-widest shadow-sm hover:bg-grey-light transition ease-in-out duration-150">
                Back
            </a>
            <a href="{{ route('admin.categories.edit', $category) }}"
               class="inline-flex items-center px-4 py-2 bg-primary border border-transparent rounded-full font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary/90 transition ease-in-out duration-150">
                Edit Category
            </a>
        </div>

    </div>
</x-app-layout>
