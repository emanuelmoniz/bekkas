<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-grey-dark leading-tight">
            Create Ticket Category
        </h2>
    </x-slot>

    <div class="py-6 max-w-4xl mx-auto">

        <form method="POST" action="{{ route('admin.ticket-categories.store') }}">
            @csrf

            <div class="bg-white p-6 rounded shadow mb-6">
                <h3 class="font-semibold mb-4">Translations</h3>

                @foreach (['pt-PT' => 'Português', 'en-UK' => 'English'] as $locale => $label)
                    <div class="mb-4">
                        <label class="block mb-1 font-semibold">{{ $label }}</label>
                        <input type="text"
                               name="name[{{ $locale }}]"
                               class="w-full border rounded px-3 py-2"
                               required>
                    </div>
                @endforeach
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.ticket-categories.index') }}"
                   class="bg-grey-medium px-6 py-2 rounded">
                    Cancel
                </a>
                <button class="bg-accent-primary hover:bg-accent-primary/90 text-light px-6 py-2 rounded">
                    Create
                </button>
            </div>
        </form>

    </div>
</x-app-layout>
