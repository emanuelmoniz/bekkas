<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-grey-dark leading-tight">
            Create Material
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            <form method="POST" action="{{ route('admin.materials.store') }}">
                @csrf

                {{-- TRANSLATIONS --}}
                <div class="bg-light p-6 rounded shadow mb-6">
                    <h3 class="font-semibold mb-4">Translations</h3>

                    @foreach (\App\Models\Locale::activeList() as $locale => $label)
                        <div class="border p-4 mb-4">
                            <h4 class="font-medium mb-2">{{ $label }}</h4>

                            <input type="text"
                                   name="name[{{ $locale }}]"
                                   class="w-full border rounded px-3 py-2"
                                   placeholder="Material name"
                                   required>
                        </div>
                    @endforeach
                </div>

                    <button type="submit"
                            class="bg-accent-primary text-light font-semibold px-6 py-2 rounded">
                        Create Material
                    </button>

            </form>

        </div>
    </div>
</x-app-layout>
