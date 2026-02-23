<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-grey-dark leading-tight">
            Projects
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- ACTION BAR --}}
            <div class="mb-4 flex justify-end">
                <a href="{{ route('admin.projects.create') }}"
                   class="inline-flex items-center bg-accent-primary hover:bg-accent-primary/90 text-light font-semibold px-4 py-2 rounded">
                    New Project
                </a>
            </div>

            {{-- FILTERS --}}
            <form method="GET" class="mb-6 bg-light p-4 rounded shadow">
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">

                    {{-- NAME --}}
                    <input type="text"
                           name="name"
                           value="{{ request('name') }}"
                           placeholder="Name"
                           class="border rounded px-3 py-2">

                    {{-- PRODUCTION DATE START --}}
                    <input type="date"
                           name="production_date_start"
                           value="{{ request('production_date_start') }}"
                           class="border rounded px-3 py-2">

                    {{-- PRODUCTION DATE END --}}
                    <input type="date"
                           name="production_date_end"
                           value="{{ request('production_date_end') }}"
                           class="border rounded px-3 py-2">

                    {{-- MATERIAL --}}
                    <div x-data="{ open:false, search:'', selected:'{{ request('material_id') }}' }" class="relative">
                        <input type="hidden" name="material_id" :value="selected">
                        <button type="button" @click="open=!open"
                                class="w-full border rounded px-3 py-2 text-left">
                            {{ optional($materials->firstWhere('id', request('material_id'))?->translation())->name ?? 'Material' }}
                        </button>
                        <div x-show="open" @click.outside="open=false"
                             class="absolute z-10 w-full bg-light border rounded shadow mt-1">
                            <input x-model="search" class="w-full px-3 py-2 border-b" placeholder="Search...">
                            @foreach($materials as $material)
                                @php $name = optional($material->translation())->name; @endphp
                                <div x-show="'{{ strtolower($name) }}'.includes(search.toLowerCase())"
                                     @click="selected='{{ $material->id }}'; open=false"
                                     class="px-3 py-2 hover:bg-grey-light cursor-pointer">
                                    {{ $name }}
                                </div>
                            @endforeach
                        </div>
                    </div>

                    {{-- ACTIVE FLAG --}}
                    <select name="is_active" class="border rounded px-3 py-2">
                        <option value="">Active</option>
                        <option value="1" @selected(request('is_active')==='1')>Yes</option>
                        <option value="0" @selected(request('is_active')==='0')>No</option>
                    </select>

                    {{-- FEATURED FLAG --}}
                    <select name="is_featured" class="border rounded px-3 py-2">
                        <option value="">Featured</option>
                        <option value="1" @selected(request('is_featured')==='1')>Yes</option>
                        <option value="0" @selected(request('is_featured')==='0')>No</option>
                    </select>

                    {{-- ACTIONS --}}
                    <div class="flex gap-2">
                        <a href="{{ route('admin.projects.index') }}"
                           class="bg-grey-medium hover:bg-grey-dark text-light px-4 py-2 rounded">
                            Reset
                        </a>
                        <button class="bg-accent-primary hover:bg-accent-primary/90 text-light px-4 py-2 rounded">
                            Filter
                        </button>
                    </div>
                </div>
            </form>

            {{-- TABLE --}}
            <div class="bg-light shadow rounded">
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
                                    {{ optional($project->translation())->name }}
                                </td>
                                <td class="px-4 py-2">
                                    {{ $project->production_date ? $project->production_date->format('Y-m-d') : '-' }}
                                </td>
                                <td class="px-4 py-2 text-center">
                                    @if($project->is_featured)
                                        ✔️
                                    @else
                                        –
                                    @endif
                                </td>
                                <td class="px-4 py-2">
                                    {{ $project->execution_time }}
                                </td>
                                <td class="px-4 py-2 text-right space-x-2">
                                    <a href="{{ route('admin.projects.edit', $project) }}"
                                       class="inline-flex bg-accent-primary text-light px-3 py-1 rounded text-sm">
                                        Edit
                                    </a>
                                    <form method="POST"
                                          action="{{ route('admin.projects.destroy', $project) }}"
                                          class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button onclick="return confirm('Delete this project?')"
                                                class="bg-grey-light text-grey-dark px-3 py-1 rounded text-sm">
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
    </div>
</x-app-layout>
