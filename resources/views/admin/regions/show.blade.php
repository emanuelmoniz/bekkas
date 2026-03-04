<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-grey-dark">
            Region Details
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded p-6">
            <dl class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div>
                    <p class="text-xs text-grey-dark uppercase tracking-widest">Country</p>
                    <p class="text-sm text-grey-dark mt-1">{{ $region->country?->name }}</p>
                </div>

                <div>
                    <p class="text-xs text-grey-dark uppercase tracking-widest">Postal Code From</p>
                    <p class="text-sm text-grey-dark mt-1">{{ $region->postal_code_from }}</p>
                </div>

                <div>
                    <p class="text-xs text-grey-dark uppercase tracking-widest">Postal Code To</p>
                    <p class="text-sm text-grey-dark mt-1">{{ $region->postal_code_to }}</p>
                </div>

                <div>
                    <p class="text-xs text-grey-dark uppercase tracking-widest">Active</p>
                    <p class="text-sm text-grey-dark mt-1">
                        @if($region->is_active)
                            <span class="text-status-success font-bold">&#10003;</span>
                        @else
                            <span class="text-status-error font-bold">&#10007;</span>
                        @endif
                    </p>
                </div>

                <div class="lg:col-span-2">
                    <p class="text-xs text-grey-dark uppercase tracking-widest">Name</p>
                    @foreach ($locales as $localeCode => $localeName)
                        @php $t = $region->translations->where('locale', $localeCode)->first(); @endphp
                        <p class="text-sm text-grey-dark mt-1">
                            <span class="font-medium">{{ $localeName }}:</span>
                            {{ $t?->name ?? '—' }}
                        </p>
                    @endforeach
                </div>
            </dl>
        </div>

        <div class="mt-6 flex justify-between">
            <x-default-button type="button" onclick="window.location.href='{{ route('admin.regions.index') }}'">
                Back
            </x-default-button>

            <x-default-button type="button" onclick="window.location.href='{{ route('admin.regions.edit', $region) }}'">
                Edit Region
            </x-default-button>
        </div>
    </div>
</x-app-layout>
