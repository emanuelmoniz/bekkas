<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-grey-dark leading-tight">
            Projects
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">

        {{-- ACTION BAR --}}
        <div class="mb-4 flex justify-end">
            <button type="button" onclick="window.location.href='{{ route('admin.projects.create') }}'"
        class="inline-flex items-center bg-primary hover:bg-primary/90 text-white px-2 py-2 rounded uppercase text-sm">
                New Project
            </button>
        </div>

        {{-- FILTERS --}}
        <form method="GET" class="mb-6 bg-white p-4 rounded shadow">
            <div class="grid grid-cols-1 md:grid-cols-7 gap-4">

                {{-- NAME --}}
                <input type="text"
                       name="name"
                       value="{{ request('name') }}"
                       placeholder="Name"
                       class="border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">

                {{-- PRODUCTION DATE START --}}
                <input type="date"
                       name="production_date_start"
                       value="{{ request('production_date_start') }}"
                       class="border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">

                {{-- PRODUCTION DATE END --}}
                <input type="date"
                       name="production_date_end"
                       value="{{ request('production_date_end') }}"
                       class="border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">

                {{-- MATERIAL --}}
                <select name="material_id" class="border-grey-medium rounded-md shadow-sm">
                    <option value="">Material</option>
                    @foreach($materials as $material)
                        @php $name = optional($material->translation())->name; @endphp
                        <option value="{{ $material->id }}" @selected(request('material_id') == $material->id)>{{ $name }}</option>
                    @endforeach
                </select>

                {{-- ACTIVE FLAG --}}
                <select name="is_active" class="border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">
                    <option value="">Active</option>
                    <option value="1" @selected(request('is_active')==='1')>Yes</option>
                    <option value="0" @selected(request('is_active')==='0')>No</option>
                </select>

                {{-- FEATURED FLAG --}}
                <select name="is_featured" class="border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">
                    <option value="">Featured</option>
                    <option value="1" @selected(request('is_featured')==='1')>Yes</option>
                    <option value="0" @selected(request('is_featured')==='0')>No</option>
                </select>

                {{-- ACTIONS --}}
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="window.location.href='{{ route('admin.projects.index') }}'"
        class="bg-grey-light hover:bg-grey-medium text-grey-dark px-2 py-2 rounded uppercase text-sm">
                        Reset
                    </button>
                    <button type="submit" class="bg-primary hover:bg-primary/90 text-white px-2 py-2 rounded uppercase text-sm">
                        Filter
                    </button>
                </div>
            </div>
        </form>

        {{-- TABLE --}}
        <div class="bg-white shadow rounded">
            <table class="min-w-full border">
                <thead class="bg-grey-light">
                    <tr>
                        <th class="px-4 py-2 text-left">Name</th>
                        <th class="px-4 py-2 text-left">Production Date</th>
                        <th class="px-4 py-2 text-center">Featured</th>
                        <th class="px-4 py-2 text-left">Execution Time</th>
                        <th class="px-4 py-2 text-right">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($projects as $project)
                        <tr class="border-t">
                            <td class="px-4 py-2">
                                <a href="{{ route('admin.projects.show', $project) }}" class="text-accent-secondary hover:underline font-medium">
                                    {{ optional($project->translation())->name }}
                                </a>
                                <x-missing-locale-badge :model="$project" />
                            </td>
                            <td class="px-4 py-2">
                                {{ $project->production_date ? $project->production_date->format('Y-m-d') : '-' }}
                            </td>
                            <td class="px-4 py-2 text-center">
                                @if($project->is_featured)
                                    <span class="text-status-success font-bold">&#10003;</span>
                                @else
                                    <span class="text-status-error font-bold">&#10007;</span>
                                @endif
                            </td>
                            <td class="px-4 py-2">
                                {{ $project->execution_time }}
                            </td>
                            <td class="px-4 py-2 text-right space-x-2">
                                <button type="button" onclick="window.location.href='{{ route('admin.projects.edit', $project) }}'"
        class="inline-flex bg-primary text-white px-2 py-2 rounded uppercase text-sm">
                                    Edit
                                </button>
                                <form method="POST"
                                      action="{{ route('admin.projects.destroy', $project) }}"
                                      class="inline">
                                    @csrf
                                    @method('DELETE')
                                    <button onclick="return confirm('Delete this project?')"
                                            class="bg-grey-light text-grey-dark px-2 py-2 rounded uppercase text-sm">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="px-4 py-6 text-center text-grey-medium">
                                No projects found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-6">
            {{ $projects->links() }}
        </div>

    </div>
</x-app-layout>
