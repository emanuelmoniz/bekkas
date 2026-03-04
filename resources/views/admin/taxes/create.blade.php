<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-grey-dark">
            New Tax
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <form method="POST"
              action="{{ route('admin.taxes.store') }}"
              class="bg-white shadow rounded p-6 space-y-4">
            @csrf

            @foreach ($locales as $localeCode => $localeName)
            <div>
                <label class="block text-sm text-grey-dark">
                    Name ({{ $localeName }})
                </label>
                <input type="text"
                       name="translations[{{ $localeCode }}]"
                       value="{{ old("translations.{$localeCode}") }}"
                       class="mt-1 block w-full border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">
                <x-input-error :messages="$errors->get('translations.'.$localeCode)" class="mt-2" />
            </div>
            @endforeach

            <div>
                <x-input-label for="percentage">Tax Percentage</x-input-label>
                <x-text-input id="percentage" name="percentage" type="number" step="0.01" class="mt-1 block w-full" required />
            </div>

            <label class="flex items-center gap-2">
                <input type="checkbox" name="is_active" checked>
                Active
            </label>

            <div class="flex justify-between">
                <button type="button"
                   onclick="window.location.href='{{ route('admin.taxes.index') }}'"
                   class="inline-flex items-center px-2 py-2 bg-white border border-grey-medium rounded text-sm text-grey-dark uppercase shadow-sm hover:bg-grey-light">
                    Cancel
                </button>
                <x-primary-button>Save</x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>
