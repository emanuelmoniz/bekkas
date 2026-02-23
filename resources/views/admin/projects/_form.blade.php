<form method="POST"
      action="{{ $mode === 'edit'
            ? route('admin.projects.update', $project)
            : route('admin.projects.store') }}"
      class="bg-light p-6 rounded shadow space-y-6">

    @csrf
    @if ($mode === 'edit')
        @method('PATCH')
    @endif

    {{-- TRANSLATIONS --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach (['pt-PT', 'en-UK'] as $locale)
            <div>
                <label class="block font-medium mb-1">
                    Name ({{ $locale }})
                </label>
                <input type="text"
                       name="name[{{ $locale }}]"
                       value="{{ old("name.$locale",
                            $mode === 'edit'
                                ? optional($project->translations->where('locale', $locale)->first())->name
                                : '') }}"
                       required
                       class="w-full border rounded px-3 py-2">
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        @foreach (['pt-PT', 'en-UK'] as $locale)
            <div>
                <label class="block font-medium mb-1">
                    Description ({{ $locale }})
                </label>
                <textarea name="description[{{ $locale }}]"
                          rows="3"
                          class="w-full border rounded px-3 py-2">{{ old("description.$locale",
                            $mode === 'edit'
                                ? optional($project->translations->where('locale', $locale)->first())->description
                                : '') }}</textarea>
            </div>
        @endforeach
    </div>

    {{-- PROJECT FIELDS --}}
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
        <div>
            <label class="block font-medium mb-1">Production Date</label>
            <input type="date"
                   name="production_date"
                   value="{{ old('production_date', isset($project) ? $project->production_date?->format('Y-m-d') : '') }}"
                   class="w-full border rounded px-3 py-2">
        </div>

        <div>
            <label class="block font-medium mb-1">Execution Time</label>
            <input type="number"
                   step="0.01"
                   name="execution_time"
                   value="{{ old('execution_time', $project->execution_time ?? '') }}"
                   class="w-full border rounded px-3 py-2">
        </div>

        <div>
            <label class="block font-medium mb-1">Dimensions</label>
            <input type="text"
                   name="dimensions"
                   value="{{ old('dimensions', $project->dimensions ?? '') }}"
                   class="w-full border rounded px-3 py-2">
        </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div>
            <label class="block font-medium mb-1">Weight</label>
            <input type="number"
                   step="0.01"
                   name="weight"
                   value="{{ old('weight', $project->weight ?? '') }}"
                   class="w-full border rounded px-3 py-2">
        </div>

        <div>
            <label class="block font-medium mb-1">Materials</label>
            <select name="materials[]"
                    multiple
                    class="w-full border rounded px-3 py-2">
                @foreach($materials as $material)
                    <option value="{{ $material->id }}"
                        @selected(
                            isset($project) &&
                            $project->materials->contains($material->id)
                        )>
                        {{ optional($material->translation())->name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    {{-- FLAGS --}}
    <label class="flex items-center gap-2">
        <input type="checkbox"
               name="is_active"
               @checked(old('is_active', $project->is_active ?? true))>
        Active
    </label>

    {{-- SUBMIT --}}
    <div class="pt-4">
        <button type="submit"
                class="bg-accent-primary hover:bg-accent-primary/90 text-light font-semibold px-6 py-3 rounded">
            {{ $mode === 'edit' ? 'Update Project' : 'Create Project' }}
        </button>
    </div>
</form>
