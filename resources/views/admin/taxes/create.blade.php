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
                <label class="block font-medium text-sm text-grey-dark">
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
                <a href="{{ route('admin.taxes.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-white border border-grey-medium rounded-full font-semibold text-xs text-grey-dark uppercase tracking-widest shadow-sm hover:bg-grey-light transition ease-in-out duration-150">
                    Cancel
                </a>
                <x-primary-button>Save</x-primary-button>
            </div>
        </form>
    </div>
</x-app-layout>
