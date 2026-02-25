<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-grey-dark">
            Region Details
        </h2>
    </x-slot>

    <div class="py-6 max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded p-6 space-y-4">
            <div>
                <strong class="text-grey-dark">Country:</strong>
                <p class="text-dark">
                    {{ app()->getLocale() === 'pt' ? $region->country->name_pt : $region->country->name_en }}
                </p>
            </div>

            <div>
                <strong class="text-grey-dark">Name:</strong>
                @foreach ($locales as $localeCode => $localeName)
                    @php $t = $region->translations->where('locale', $localeCode)->first(); @endphp
                    <p class="text-dark text-sm">
                        <span class="text-grey-dark font-medium">{{ $localeName }}:</span>
                        {{ $t?->name ?? '—' }}
                    </p>
                @endforeach
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <strong class="text-grey-dark">Postal Code From:</strong>
                    <p class="text-dark">{{ $region->postal_code_from }}</p>
                </div>

                <div>
                    <strong class="text-grey-dark">Postal Code To:</strong>
                    <p class="text-dark">{{ $region->postal_code_to }}</p>
                </div>
            </div>

            <div>
                <strong class="text-grey-dark">Active:</strong>
                <p class="text-dark">
                    <span class="px-2 py-1 rounded text-xs {{ $region->is_active ? 'bg-status-success text-status-success' : 'bg-status-error/10 text-status-error' }}">
                        {{ $region->is_active ? 'Yes' : 'No' }}
                    </span>
                </p>
            </div>
        </div>

        <div class="mt-6 flex justify-between">
            <a href="{{ route('admin.regions.index') }}"
               class="bg-grey-medium hover:bg-grey-dark text-light px-6 py-3 rounded">
                Back
            </a>

            <a href="{{ route('admin.regions.edit', $region) }}"
               class="bg-accent-primary hover:bg-accent-primary/90 text-light px-6 py-3 rounded">
                Edit Region
            </a>
        </div>
    </div>
</x-app-layout>
