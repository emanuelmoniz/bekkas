<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-grey-dark">
            Edit Tax
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <form method="POST"
              action="{{ route('admin.taxes.update', $tax) }}"
              class="bg-white shadow rounded p-6 space-y-4">
            @csrf
            @method('PATCH')

            @foreach ($locales as $localeCode => $localeName)
            @php
                $existingName = $tax->translations->where('locale', $localeCode)->first()?->name;
            @endphp
            <div>
                <label class="block text-sm text-grey-dark">
                    Name ({{ $localeName }})
                </label>
                <input type="text"
                       name="translations[{{ $localeCode }}]"
                       value="{{ old("translations.{$localeCode}", $existingName) }}"
                       class="mt-1 block w-full border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">
                <x-input-error :messages="$errors->get('translations.'.$localeCode)" class="mt-2" />
            </div>
            @endforeach

            <div>
                <x-input-label for="percentage">Tax Percentage</x-input-label>
                <x-text-input id="percentage" name="percentage" type="number" step="0.01" class="mt-1 block w-full" :value="$tax->percentage" required />
            </div>

            <label class="flex items-center gap-2">
                <input type="checkbox" name="is_active"
                       @checked($tax->is_active)>
                Active
            </label>

            <div class="flex justify-between">
                <x-default-button type="button" onclick="window.location.href='{{ route('admin.taxes.index') }}'">
                    Cancel
                </x-default-button>
                <x-default-button>Update</x-default-button>
            </div>
        </form>
    </div>
</x-app-layout>
