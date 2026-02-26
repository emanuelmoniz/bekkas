<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-grey-dark leading-tight">
            Edit Project
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">

        {{-- PHOTOS --}}
        <div class="bg-white p-6 rounded shadow mb-6">
            <h3 class="font-semibold mb-4">Photos</h3>

            <form method="POST"
                  action="{{ route('admin.projects.photos.store', $project) }}"
                  enctype="multipart/form-data"
                  class="mb-4 flex flex-col sm:flex-row gap-4">
                @csrf

                <input type="file"
                       name="photos[]"
                       multiple
                       accept="image/*"
                       class="border rounded px-3 py-2">

                <button type="submit"
                        class="bg-primary hover:bg-primary/90 text-white px-2 py-2 rounded uppercase text-sm">
                    Upload Photos
                </button>
            </form>

            <div class="grid grid-cols-2 sm:grid-cols-4 gap-4">
                @foreach ($project->photos as $photo)
                    <div class="border p-2 rounded text-center">
                        <img src="{{ asset('storage/'.$photo->path) }}"
                             class="h-32 w-full object-cover rounded mb-2">

                        @if ($photo->is_primary)
                            <div class="text-status-success text-sm mb-1">
                                Primary
                            </div>
                        @else
                            <form method="POST"
                                  action="{{ route('admin.project-photos.primary', $photo) }}">
                                @csrf
                                <button class="text-accent-primary text-sm">
                                    Make Primary
                                </button>
                            </form>
                        @endif

                        <form method="POST"
                              action="{{ route('admin.project-photos.destroy', $photo) }}">
                            @csrf
                            @method('DELETE')
                            <button type="submit"
                                    onclick="return confirm('Delete photo?')"
                                    class="text-status-error text-sm mt-1">
                                Delete
                            </button>
                        </form>
                    </div>
                @endforeach
            </div>
        </div>

        {{-- PROJECT FORM --}}
        @include('admin.projects._form', ['mode' => 'edit'])

    </div>
</x-app-layout>
