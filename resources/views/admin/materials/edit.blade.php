<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-grey-dark leading-tight">
            Edit Material
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <form method="POST" action="{{ route('admin.materials.update', $material) }}">
                @csrf
                @method('PUT')

                {{-- TRANSLATIONS --}}
                <div class="bg-white p-6 rounded shadow mb-6">
                    <h3 class="font-semibold mb-4">Translations</h3>

                    @foreach (\App\Models\Locale::activeList() as $locale => $label)
                        @php
                            $translation = $material->translations->firstWhere('locale', $locale);
                        @endphp

                        <div class="border p-4 mb-4">
                            <h4 class="font-medium mb-2">{{ $label }}</h4>

                            <input type="text"
                                   name="name[{{ $locale }}]"
                                   value="{{ $translation?->name }}"
                                   class="w-full border rounded px-3 py-2"
                                   required>
                        </div>
                    @endforeach
                </div>

                {{-- ACTIONS --}}
                <div class="bg-white p-6 rounded shadow flex justify-between">
                    <a href="{{ route('admin.materials.index') }}"
                       class="bg-grey-medium hover:bg-grey-medium text-grey-dark px-6 py-3 rounded">
                        Cancel
                    </a>

                    <button type="submit"
                            class="bg-accent-primary hover:bg-accent-primary/90 text-light font-semibold px-6 py-3 rounded">
                        Update Material
                    </button>
                </div>

            </form>

        </div>
    </div>
</x-app-layout>
