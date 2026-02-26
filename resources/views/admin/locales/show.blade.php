<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-grey-dark leading-tight">Locale Details</h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">

        <div class="bg-white shadow rounded p-6 mb-6">
            <h3 class="text-xs text-grey-dark font-medium uppercase tracking-widest mb-4">Basic Information</h3>
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-xs text-grey-dark font-medium uppercase tracking-widest">Code</p>
                    <p class="text-sm text-grey-dark mt-1 font-mono">{{ $locale->code }}</p>
                </div>
                <div>
                    <p class="text-xs text-grey-dark font-medium uppercase tracking-widest">Name</p>
                    <p class="text-sm text-grey-dark mt-1">{{ $locale->name }}</p>
                </div>
                <div>
                    <p class="text-xs text-grey-dark font-medium uppercase tracking-widest">Flag</p>
                    <p class="text-2xl mt-1">{{ $locale->flag_emoji ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-grey-dark font-medium uppercase tracking-widest">Country</p>
                    <p class="text-sm text-grey-dark mt-1">{{ optional($locale->country)->name ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-grey-dark font-medium uppercase tracking-widest">Active</p>
                    <p class="text-sm text-grey-dark mt-1">
                        @if($locale->is_active)
                            <span class="text-status-success font-bold">&#10003;</span>
                        @else
                            <span class="text-status-error font-bold">&#10007;</span>
                        @endif
                    </p>
                </div>
                <div>
                    <p class="text-xs text-grey-dark font-medium uppercase tracking-widest">Default</p>
                    <p class="text-sm text-grey-dark mt-1">
                        @if($locale->is_default)
                            <span class="text-status-success font-bold">&#10003;</span>
                        @else
                            <span class="text-status-error font-bold">&#10007;</span>
                        @endif
                    </p>
                </div>
            </dl>
        </div>

        <div class="flex justify-between mt-6">
            <a href="{{ route('admin.locales.index') }}"
               class="inline-flex items-center px-4 py-2 bg-white border border-grey-medium rounded-md font-semibold text-xs text-grey-dark uppercase tracking-widest shadow-sm hover:bg-grey-light transition ease-in-out duration-150">
                Back
            </a>
            <a href="{{ route('admin.locales.edit', $locale) }}"
               class="inline-flex items-center px-4 py-2 bg-accent-primary border border-transparent rounded-md font-semibold text-xs text-light uppercase tracking-widest hover:bg-accent-primary/90 transition ease-in-out duration-150">
                Edit Locale
            </a>
        </div>

    </div>
</x-app-layout>
