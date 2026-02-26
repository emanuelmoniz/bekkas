<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-grey-dark leading-tight">
            Project Details
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">

        <div class="bg-white shadow rounded p-6 mb-6">
            <h3 class="text-xs text-grey-dark font-medium uppercase tracking-widest mb-4">Basic Information</h3>

            <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-xs text-grey-dark font-medium uppercase tracking-widest">Name</p>
                    <p class="text-sm text-grey-dark mt-1">{{ optional($project->translation())->name }}</p>
                </div>
                <div>
                    <p class="text-xs text-grey-dark font-medium uppercase tracking-widest">Production Date</p>
                    <p class="text-sm text-grey-dark mt-1">{{ $project->production_date ? $project->production_date->format('Y-m-d') : '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-grey-dark font-medium uppercase tracking-widest">Execution Time</p>
                    <p class="text-sm text-grey-dark mt-1">{{ $project->execution_time }}</p>
                </div>
                <div>
                    <p class="text-xs text-grey-dark font-medium uppercase tracking-widest">Width (mm)</p>
                    <p class="text-sm text-grey-dark mt-1">{{ $project->width ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-grey-dark font-medium uppercase tracking-widest">Length (mm)</p>
                    <p class="text-sm text-grey-dark mt-1">{{ $project->length ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-grey-dark font-medium uppercase tracking-widest">Height (mm)</p>
                    <p class="text-sm text-grey-dark mt-1">{{ $project->height ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-grey-dark font-medium uppercase tracking-widest">Weight</p>
                    <p class="text-sm text-grey-dark mt-1">{{ $project->weight ?? '-' }}</p>
                </div>
                <div>
                    <p class="text-xs text-grey-dark font-medium uppercase tracking-widest">Materials</p>
                    <p class="text-sm text-grey-dark mt-1">
                        {{ $project->materials->map(fn($m)=>optional($m->translation())->name)->filter()->join(', ') ?: '-' }}
                    </p>
                </div>
                <div>
                    <p class="text-xs text-grey-dark font-medium uppercase tracking-widest">Active</p>
                    <p class="text-sm text-grey-dark mt-1">
                        @if($project->is_active)
                            <span class="text-status-success font-bold">&#10003;</span>
                        @else
                            <span class="text-status-error font-bold">&#10007;</span>
                        @endif
                    </p>
                </div>
            </dl>
        </div>

        {{-- PHOTOS --}}
        <div class="bg-white shadow rounded p-6 mb-6">
            <h3 class="text-xs text-grey-dark font-medium uppercase tracking-widest mb-4">Photos</h3>
            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                @foreach($project->photos as $photo)
                    <div class="border border-grey-medium p-2 rounded text-center">
                        <img src="{{ asset('storage/'.$photo->path) }}" class="h-32 w-full object-cover rounded mb-2">
                        @if($photo->is_primary)
                            <div class="text-status-success font-semibold text-sm mb-1">Primary</div>
                        @endif
                    </div>
                @endforeach
                @if($project->photos->isEmpty())
                    <p class="text-sm text-grey-medium">No photos available.</p>
                @endif
            </div>
        </div>

        <div class="flex justify-between mt-6">
            <a href="{{ route('admin.projects.index') }}"
               class="inline-flex items-center px-4 py-2 bg-white border border-grey-medium rounded-full font-semibold text-xs text-grey-dark uppercase tracking-widest shadow-sm hover:bg-grey-light transition ease-in-out duration-150">
                Back
            </a>
            <a href="{{ route('admin.projects.edit', $project) }}"
               class="inline-flex items-center px-4 py-2 bg-primary border border-transparent rounded-full font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary/90 transition ease-in-out duration-150">
                Edit Project
            </a>
        </div>

    </div>
</x-app-layout>