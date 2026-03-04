<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-grey-dark leading-tight">Product Details</h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">

        {{-- BASIC INFO --}}
        <div class="bg-white shadow rounded p-6 mb-6">
            <h3 class="text-xs text-grey-dark uppercase mb-4">Basic Information</h3>
            <dl class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div>
                    <p class="text-xs text-grey-dark uppercase tracking-widest">Price</p>
                    <p class="text-sm text-grey-dark mt-1">{{ number_format($product->price, 2) }}</p>
                </div>
                <div>
                    <p class="text-xs text-grey-dark uppercase tracking-widest">Promo Price</p>
                    <p class="text-sm text-grey-dark mt-1">{{ $product->promo_price ? number_format($product->promo_price, 2) : '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-grey-dark uppercase tracking-widest">Stock</p>
                    <p class="text-sm text-grey-dark mt-1">{{ $product->stock }}</p>
                </div>
                <div>
                    <p class="text-xs text-grey-dark uppercase tracking-widest">Production Time (days)</p>
                    <p class="text-sm text-grey-dark mt-1">{{ $product->production_time }}</p>
                </div>
                <div>
                    <p class="text-xs text-grey-dark uppercase tracking-widest">Weight (g)</p>
                    <p class="text-sm text-grey-dark mt-1">{{ $product->weight ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-grey-dark uppercase tracking-widest">Dimensions (W × L × H mm)</p>
                    <p class="text-sm text-grey-dark mt-1">{{ $product->width ?? '—' }} × {{ $product->length ?? '—' }} × {{ $product->height ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-grey-dark uppercase tracking-widest">Tax</p>
                    <p class="text-sm text-grey-dark mt-1">{{ optional($product->tax)->name ?? '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-grey-dark uppercase tracking-widest">Categories</p>
                    <p class="text-sm text-grey-dark mt-1">{{ $product->categories->map(fn($c) => optional($c->translation())->name)->filter()->join(', ') ?: '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-grey-dark uppercase tracking-widest">Materials</p>
                    <p class="text-sm text-grey-dark mt-1">{{ $product->materials->map(fn($m) => optional($m->translation())->name)->filter()->join(', ') ?: '—' }}</p>
                </div>
                <div>
                    <p class="text-xs text-grey-dark uppercase tracking-widest">Featured</p>
                    <p class="text-sm text-grey-dark mt-1">
                        @if($product->is_featured)
                            <span class="text-status-success font-bold">&#10003;</span>
                        @else
                            <span class="text-status-error font-bold">&#10007;</span>
                        @endif
                    </p>
                </div>
                <div>
                    <p class="text-xs text-grey-dark uppercase tracking-widest">Promo</p>
                    <p class="text-sm text-grey-dark mt-1">
                        @if($product->is_promo)
                            <span class="text-status-success font-bold">&#10003;</span>
                        @else
                            <span class="text-status-error font-bold">&#10007;</span>
                        @endif
                    </p>
                </div>
                <div>
                    <p class="text-xs text-grey-dark uppercase tracking-widest">Active</p>
                    <p class="text-sm text-grey-dark mt-1">
                        @if($product->active)
                            <span class="text-status-success font-bold">&#10003;</span>
                        @else
                            <span class="text-status-error font-bold">&#10007;</span>
                        @endif
                    </p>
                </div>
            </dl>
        </div>

        {{-- TRANSLATIONS --}}
        <div class="bg-white shadow rounded p-6 mb-6">
            <h3 class="text-xs text-grey-dark uppercase mb-4">Translations</h3>
            <div class="space-y-4">
                @foreach($product->translations as $translation)
                    <div class="border border-grey-light rounded p-4">
                        <p class="text-xs text-grey-dark uppercase mb-2">{{ $translation->locale }}</p>
                        <dl class="grid grid-cols-1 lg:grid-cols-2 gap-4">
                            <div>
                                <p class="text-xs text-grey-medium uppercase">Name</p>
                                <p class="text-sm text-grey-dark mt-1">{{ $translation->name ?: '—' }}</p>
                            </div>
                            <div class="lg:col-span-2">
                                <p class="text-xs text-grey-medium uppercase">Description</p>
                                <p class="text-sm text-grey-dark mt-1 whitespace-pre-line">{{ $translation->description ?: '—' }}</p>
                            </div>
                            <div class="lg:col-span-2">
                                <p class="text-xs text-grey-medium uppercase">Technical Info</p>
                                <p class="text-sm text-grey-dark mt-1 whitespace-pre-line">{{ $translation->technical_info ?: '—' }}</p>
                            </div>
                        </dl>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- PHOTOS --}}
        <div class="bg-white shadow rounded p-6 mb-6">
            <h3 class="text-xs text-grey-dark uppercase mb-4">Photos</h3>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                @foreach($product->photos as $photo)
                    <div class="border border-grey-medium p-2 rounded text-center">
                        <img src="{{ asset('storage/'.$photo->path) }}" class="h-32 w-full object-cover rounded mb-2">
                        @if($photo->is_primary)
                            <div class="text-status-success text-sm mb-1">Primary</div>
                        @endif
                    </div>
                @endforeach
                @if($product->photos->isEmpty())
                    <p class="text-sm text-grey-medium">No photos available.</p>
                @endif
            </div>
        </div>

        <div class="flex justify-between mt-6">
            <button type="button"
               onclick="window.location.href='{{ route('admin.products.index') }}'"
               class="inline-flex items-center px-2 py-2 bg-white border border-grey-medium rounded text-sm text-grey-dark uppercase shadow-sm hover:bg-grey-light">
                Back
            </button>
            <button type="button"
               onclick="window.location.href='{{ route('admin.products.edit', $product) }}'"
               class="inline-flex items-center px-2 py-2 bg-primary border border-transparent rounded text-sm text-white uppercase hover:bg-primary/90 transition ease-in-out duration-150">
                Edit Product
            </button>
        </div>

    </div>
</x-app-layout>
