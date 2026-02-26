<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-grey-dark leading-tight">Shipping Tier Details</h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">

        <div class="bg-white shadow rounded p-6 mb-6">
            <h3 class="text-xs text-grey-dark uppercase mb-4">Basic Information</h3>
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-xs text-grey-dark uppercase tracking-widest">Weight From (g)</p>
                    <p class="text-sm text-grey-dark mt-1">{{ $tier->weight_from }}</p>
                </div>
                <div>
                    <p class="text-xs text-grey-dark uppercase tracking-widest">Weight To (g)</p>
                    <p class="text-sm text-grey-dark mt-1">{{ $tier->weight_to }}</p>
                </div>
                <div>
                    <p class="text-xs text-grey-dark uppercase tracking-widest">Cost (gross)</p>
                    <p class="text-sm text-grey-dark mt-1">{{ number_format($tier->cost_gross, 2) }}</p>
                </div>
                <div>
                    <p class="text-xs text-grey-dark uppercase tracking-widest">Shipping Days</p>
                    <p class="text-sm text-grey-dark mt-1">{{ $tier->shipping_days ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-grey-dark uppercase tracking-widest">Tax</p>
                    <p class="text-sm text-grey-dark mt-1">{{ optional($tier->tax)->name ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-grey-dark uppercase tracking-widest">Active</p>
                    <p class="text-sm text-grey-dark mt-1">
                        @if($tier->active)
                            <span class="text-status-success font-bold">&#10003;</span>
                        @else
                            <span class="text-status-error font-bold">&#10007;</span>
                        @endif
                    </p>
                </div>
                <div>
                    <p class="text-xs text-grey-dark uppercase tracking-widest">Countries</p>
                    <p class="text-sm text-grey-dark mt-1">{{ $tier->countries->map(fn($c) => $c->name)->filter()->join(', ') ?: '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-grey-dark uppercase tracking-widest">Regions</p>
                    <p class="text-sm text-grey-dark mt-1">{{ $tier->regions->map(fn($r) => $r->name)->filter()->join(', ') ?: '—' }}</p>
                </div>
            </dl>
        </div>

        <div class="bg-white shadow rounded p-6 mb-6">
            <h3 class="text-xs text-grey-dark uppercase mb-4">Translations</h3>
            <div class="space-y-4">
                @foreach($tier->translations as $translation)
                    <div class="border border-grey-light rounded p-4">
                        <p class="text-xs text-grey-dark uppercase mb-2">{{ $translation->locale }}</p>
                        <div>
                            <p class="text-xs text-grey-medium uppercase">Name</p>
                            <p class="text-sm text-grey-dark mt-1">{{ $translation->name ?: '—' }}</p>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>

        <div class="flex justify-between mt-6">
            <button type="button"
               onclick="window.location.href='{{ route('admin.shipping-tiers.index') }}'"
               class="inline-flex items-center px-2 py-2 bg-white border border-grey-medium rounded text-sm text-grey-dark uppercase shadow-sm hover:bg-grey-light transition ease-in-out duration-150">
                Back
            </button>
            <button type="button"
               onclick="window.location.href='{{ route('admin.shipping-tiers.edit', $tier) }}'"
               class="inline-flex items-center px-2 py-2 bg-primary border border-transparent rounded text-sm text-white uppercase hover:bg-primary/90 transition ease-in-out duration-150">
                Edit Shipping Tier
            </button>
        </div>

    </div>
</x-app-layout>
