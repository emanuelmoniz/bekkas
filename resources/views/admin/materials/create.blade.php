<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-grey-dark leading-tight">
            Create Material
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">

        <form method="POST" action="{{ route('admin.materials.store') }}">
            @csrf

            {{-- TRANSLATIONS --}}
            <div class="bg-white p-6 rounded shadow mb-6">
                <h3 class="font-semibold mb-4">Translations</h3>

                @php $defaultLocale = \App\Models\Locale::defaultLocale()?->code ?? 'en-UK'; @endphp
                @foreach (\App\Models\Locale::activeList() as $locale => $label)
                    <div class="mb-4">
                        <x-input-label>{{ $label }} @if($locale === $defaultLocale)<span class="text-status-error">*</span>@endif</x-input-label>
                        <input type="text"
                               name="name[{{ $locale }}]"
                               class="mt-1 block w-full border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm"
                               placeholder="Material name"
                               @if($locale === $defaultLocale) required @endif>
                        <x-input-error :messages="$errors->get('name.'.$locale)" class="mt-2" />
                    </div>
                @endforeach
            </div>

            {{-- ACTIONS --}}
            <div class="bg-white p-6 rounded shadow flex justify-between">
                <x-default-button type="button" onclick="window.location.href='{{ route('admin.materials.index') }}'">
                    Cancel
                </x-default-button>
                <x-default-button>Create Material</x-default-button>
            </div>

        </form>

    </div>
</x-app-layout>
