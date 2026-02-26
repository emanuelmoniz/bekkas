<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-grey-dark leading-tight">Tax Details</h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">

        <div class="bg-white shadow rounded p-6 mb-6">
            <h3 class="text-xs text-grey-dark font-medium uppercase tracking-widest mb-4">Basic Information</h3>
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-xs text-grey-dark font-medium uppercase tracking-widest">Percentage</p>
                    <p class="text-sm text-grey-dark mt-1">{{ $tax->percentage }}%</p>
                </div>
                <div>
                    <p class="text-xs text-grey-dark font-medium uppercase tracking-widest">Active</p>
                    <p class="text-sm text-grey-dark mt-1">
                        @if($tax->is_active)
                            <span class="text-status-success font-bold">&#10003;</span>
                        @else
                            <span class="text-status-error font-bold">&#10007;</span>
                        @endif
                    </p>
                </div>
            </dl>
        </div>

        <div class="bg-white shadow rounded p-6 mb-6">
            <h3 class="text-xs text-grey-dark font-medium uppercase tracking-widest mb-4">Translations</h3>
            <div class="space-y-4">
                @foreach($tax->translations as $translation)
                    <div class="border border-grey-light rounded p-4">
                        <p class="text-xs text-grey-dark font-medium uppercase tracking-widest mb-2">{{ $translation->locale }}</p>
                        <div>
                            <p class="text-xs text-grey-medium uppercase">Name</p>
                            <p class="text-sm text-grey-dark mt-1">{{ $translation->name ?: '—' }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="flex justify-between mt-6">
            <a href="{{ route('admin.taxes.index') }}"
               class="inline-flex items-center px-4 py-2 bg-white border border-grey-medium rounded-md font-semibold text-xs text-grey-dark uppercase tracking-widest shadow-sm hover:bg-grey-light transition ease-in-out duration-150">
                Back
            </a>
            <a href="{{ route('admin.taxes.edit', $tax) }}"
               class="inline-flex items-center px-4 py-2 bg-accent-primary border border-transparent rounded-md font-semibold text-xs text-light uppercase tracking-widest hover:bg-accent-primary/90 transition ease-in-out duration-150">
                Edit Tax
            </a>
        </div>

    </div>
</x-app-layout>
