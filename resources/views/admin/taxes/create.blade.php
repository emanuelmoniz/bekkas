<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-grey-dark">
            New Tax
        </h2>
    </x-slot>

    <div class="py-6 max-w-xl mx-auto sm:px-6 lg:px-8">
        <form method="POST"
              action="{{ route('admin.taxes.store') }}"
              class="bg-white shadow rounded p-6 space-y-4">
            @csrf

            @foreach ($locales as $localeCode => $localeName)
            <div>
                <label class="block text-sm font-medium text-grey-dark">
                    Name ({{ $localeName }})
                </label>
                <input type="text"
                       name="translations[{{ $localeCode }}]"
                       value="{{ old("translations.{$localeCode}") }}"
                       class="mt-1 block w-full border rounded px-3 py-2 @error("translations.{$localeCode}") border-status-error @enderror">
                @error("translations.{$localeCode}")
                    <p class="text-status-error text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>
            @endforeach

            <div>
                <label class="block text-sm font-medium text-grey-dark">
                    Tax Percentage
                </label>
                <input type="number"
                       step="0.01"
                       name="percentage"
                       required
                       class="mt-1 block w-full border rounded px-3 py-2">
            </div>

            <label class="flex items-center gap-2">
                <input type="checkbox" name="is_active" checked>
                Active
            </label>

            <div class="flex justify-end">
                <button type="submit"
                        class="bg-accent-primary hover:bg-accent-primary/90 text-light px-4 py-2 rounded">
                    Save
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
