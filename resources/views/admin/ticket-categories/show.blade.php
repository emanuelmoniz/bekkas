<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-grey-dark leading-tight">Ticket Category Details</h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">

        <div class="bg-white shadow rounded p-6 mb-6">
            <h3 class="text-xs text-grey-dark uppercase mb-4">Basic Information</h3>
            <dl class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <div>
                    <p class="text-xs text-grey-dark uppercase tracking-widest">Active</p>
                    <p class="text-sm text-grey-dark mt-1">
                        @if($category->active)
                            <span class="text-status-success font-bold">&#10003;</span>
                        @else
                            <span class="text-status-error font-bold">&#10007;</span>
                        @endif
                    </p>
                </div>
                <div>
                    <p class="text-xs text-grey-dark uppercase tracking-widest">Slug</p>
                    <p class="text-sm text-grey-dark mt-1 font-mono">{{ $category->slug ?: '—' }}</p>
                </div>
            </dl>
        </div>

        <div class="bg-white shadow rounded p-6 mb-6">
            <h3 class="text-xs text-grey-dark uppercase mb-4">Translations</h3>
            <div class="space-y-4">
                @foreach($category->translations as $translation)
                    <div class="border border-grey-light rounded p-4 space-y-3">
                        <p class="text-xs font-bold text-grey-dark uppercase">{{ $translation->locale }}</p>
                        <div>
                            <p class="text-xs text-grey-medium uppercase">Name</p>
                            <p class="text-sm text-grey-dark mt-1">{{ $translation->name ?: '—' }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-grey-medium uppercase">Description</p>
                            <p class="text-sm text-grey-dark mt-1 whitespace-pre-line">{{ $translation->description ?: '—' }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="flex justify-between mt-6">
            <x-default-button type="button" onclick="window.location.href='{{ route('admin.ticket-categories.index') }}'">
                Back
            </x-default-button>
            <x-default-button type="button" onclick="window.location.href='{{ route('admin.ticket-categories.edit', $category) }}'">
                Edit Category
            </x-default-button>
        </div>

    </div>
</x-app-layout>
