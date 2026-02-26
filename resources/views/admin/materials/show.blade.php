<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-grey-dark leading-tight">Material Details</h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">

        <div class="bg-white shadow rounded p-6 mb-6">
            <h3 class="text-xs text-grey-dark uppercase mb-4">Translations</h3>
            <div class="space-y-4">
                @foreach($material->translations as $translation)
                    <div class="border border-grey-light rounded p-4">
                        <p class="text-xs text-grey-dark uppercase mb-2">{{ $translation->locale }}</p>
                        <div>
                            <p class="text-xs text-grey-medium uppercase">Name</p>
                            <p class="text-sm text-grey-dark mt-1">{{ $translation->name ?: '—' }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="flex justify-between mt-6">
            <button type="button"
               onclick="window.location.href='{{ route('admin.materials.index') }}'"
               class="inline-flex items-center px-2 py-2 bg-white border border-grey-medium rounded text-sm text-grey-dark uppercase shadow-sm hover:bg-grey-light transition ease-in-out duration-150">
                Back
            </button>
            <button type="button"
               onclick="window.location.href='{{ route('admin.materials.edit', $material) }}'"
               class="inline-flex items-center px-2 py-2 bg-primary border border-transparent rounded text-sm text-white uppercase hover:bg-primary/90 transition ease-in-out duration-150">
                Edit Material
            </button>
        </div>

    </div>
</x-app-layout>
