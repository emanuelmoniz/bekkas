<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
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

                    @foreach (['pt-PT' => 'Português', 'en-UK' => 'English'] as $locale => $label)
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
                       class="bg-gray-300 hover:bg-gray-400 text-gray-800 px-6 py-3 rounded">
                        Cancel
                    </a>

                    <button type="submit"
                            class="bg-green-600 hover:bg-green-700 text-white font-semibold px-6 py-3 rounded">
                        Update Material
                    </button>
                </div>

            </form>

        </div>
    </div>
</x-app-layout>
