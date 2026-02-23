<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-grey-dark leading-tight">
            Project Details
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <div class="bg-light p-6 rounded shadow mb-6 space-y-4">
                <h3 class="text-lg font-semibold border-b pb-2">Basic Information</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <strong class="text-grey-dark">Name:</strong>
                        <p class="text-dark">{{ optional($project->translation())->name }}</p>
                    </div>
                    <div>
                        <strong class="text-grey-dark">Production Date:</strong>
                        <p class="text-dark">{{ $project->production_date ? $project->production_date->format('Y-m-d') : '-' }}</p>
                    </div>
                    <div>
                        <strong class="text-grey-dark">Execution Time:</strong>
                        <p class="text-dark">{{ $project->execution_time }}</p>
                    </div>
                    <div>
                        <strong class="text-grey-dark">Dimensions:</strong>
                        <p class="text-dark">{{ $project->dimensions ?? '-' }}</p>
                    </div>
                    <div>
                        <strong class="text-grey-dark">Weight:</strong>
                        <p class="text-dark">{{ $project->weight ?? '-' }}</p>
                    </div>
                    <div>
                        <strong class="text-grey-dark">Materials:</strong>
                        <p class="text-dark">
                            {{ $project->materials->map(fn($m)=>optional($m->translation())->name)->filter()->join(', ') ?: '-' }}
                        </p>
                    </div>
                    <div>
                        <strong class="text-grey-dark">Active:</strong>
                        <p class="text-dark">{{ $project->is_active ? 'Yes' : 'No' }}</p>
                    </div>
                </div>
            </div>

            {{-- PHOTOS --}}
            <div class="bg-light p-6 rounded shadow mb-6">
                <h3 class="text-lg font-semibold border-b pb-2 mb-4">Photos</h3>
                <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                    @foreach($project->photos as $photo)
                        <div class="border p-2 rounded text-center">
                            <img src="{{ asset('storage/'.$photo->path) }}" class="h-32 w-full object-cover rounded mb-2">
                            @if($photo->is_primary)
                                <div class="text-status-success font-semibold text-sm mb-1">Primary</div>
                            @endif
                        </div>
                    @endforeach
                    @if($project->photos->isEmpty())
                        <p class="text-grey-medium">No photos available.</p>
                    @endif
                </div>
            </div>

            <div class="flex justify-between">
                <a href="{{ route('admin.projects.index') }}" class="bg-grey-medium hover:bg-grey-dark text-light px-6 py-3 rounded">
                    Back
                </a>
                <a href="{{ route('admin.projects.edit', $project) }}" class="bg-accent-primary hover:bg-accent-primary/90 text-light px-6 py-3 rounded">
                    Edit Project
                </a>
            </div>

        </div>
    </div>
</x-app-layout>