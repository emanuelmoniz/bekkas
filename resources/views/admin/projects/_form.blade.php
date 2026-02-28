<form method="POST"
      action="{{ $mode === 'edit'
            ? route('admin.projects.update', $project)
            : route('admin.projects.store') }}"
      class="bg-white p-6 rounded shadow space-y-6">

    @csrf
    @if ($mode === 'edit')
        @method('PATCH')
    @endif

    {{-- TRANSLATIONS --}}
    @php $defaultLocale = \App\Models\Locale::defaultLocale()?->code ?? 'en-UK'; @endphp
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @foreach (\App\Models\Locale::activeCodes() as $locale)
            <div>
                <x-input-label>Name ({{ $locale }}) @if($locale === $defaultLocale)<span class="text-status-error">*</span>@endif</x-input-label>
                <input type="text"
                       name="name[{{ $locale }}]"
                       value="{{ old("name.$locale",
                            $mode === 'edit'
                                ? optional($project->translations->where('locale', $locale)->first())->name
                                : '') }}"
                       @if($locale === $defaultLocale) required @endif
                       class="mt-1 block w-full border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">
                <x-input-error :messages="$errors->get('name.'.$locale)" class="mt-2" />
            </div>
        @endforeach
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        @foreach (\App\Models\Locale::activeCodes() as $locale)
            <div>
                <x-input-label>Description ({{ $locale }})</x-input-label>
                <textarea name="description[{{ $locale }}]"
                          rows="3"
                          class="mt-1 block w-full border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">{{ old("description.$locale",
                            $mode === 'edit'
                                ? optional($project->translations->where('locale', $locale)->first())->description
                                : '') }}</textarea>
            </div>
        @endforeach
    </div>

    {{-- PROJECT FIELDS --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <div>
            <x-input-label for="production_date">Production Date</x-input-label>
            <input type="date"
                   id="production_date"
                   name="production_date"
                   value="{{ old('production_date', isset($project) ? $project->production_date?->format('Y-m-d') : '') }}"
                   class="mt-1 block w-full border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">
        </div>

        <div>
            <x-input-label for="execution_time">Execution Time</x-input-label>
            <input type="number"
                   id="execution_time"
                   step="0.01"
                   name="execution_time"
                   value="{{ old('execution_time', $project->execution_time ?? '') }}"
                   class="mt-1 block w-full border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">
        </div>

        <div>
            <x-input-label for="width">Width (mm)</x-input-label>
            <input type="number"
                   id="width"
                   min="0"
                   name="width"
                   value="{{ old('width', $project->width ?? '') }}"
                   class="mt-1 block w-full border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">
        </div>

        <div>
            <x-input-label for="length">Length (mm)</x-input-label>
            <input type="number"
                   id="length"
                   min="0"
                   name="length"
                   value="{{ old('length', $project->length ?? '') }}"
                   class="mt-1 block w-full border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">
        </div>

        <div>
            <x-input-label for="height">Height (mm)</x-input-label>
            <input type="number"
                   id="height"
                   min="0"
                   name="height"
                   value="{{ old('height', $project->height ?? '') }}"
                   class="mt-1 block w-full border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div>
            <x-input-label for="weight">Weight</x-input-label>
            <input type="number"
                   id="weight"
                   step="0.01"
                   name="weight"
                   value="{{ old('weight', $project->weight ?? '') }}"
                   class="mt-1 block w-full border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">
        </div>

        <div>
            <x-input-label>Materials</x-input-label>
            <select name="materials[]"
                    multiple
                    class="mt-1 block w-full border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">
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

    {{-- CLIENT --}}
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
        <div>
            <x-input-label for="client">Client</x-input-label>
            <input type="text"
                   id="client"
                   name="client"
                   value="{{ old('client', $project->client ?? '') }}"
                   class="mt-1 block w-full border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">
        </div>

        <div>
            <x-input-label for="client_url">Client URL</x-input-label>
            <input type="url"
                   id="client_url"
                   name="client_url"
                   value="{{ old('client_url', $project->client_url ?? '') }}"
                   class="mt-1 block w-full border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">
        </div>
    </div>

    {{-- FLAGS --}}
    <div class="flex gap-6">
        <label class="flex items-center gap-2">
            <input type="checkbox"
                   name="is_active"
                   @checked(old('is_active', $project->is_active ?? true))>
            Active
        </label>

        <label class="flex items-center gap-2">
            <input type="checkbox"
                   name="is_featured"
                   @checked(old('is_featured', $project->is_featured ?? false))>
            Featured
        </label>
    </div>

    {{-- SUBMIT --}}
    <div class="pt-4 flex justify-between">
        <button type="button"
           onclick="window.location.href='{{ route('admin.projects.index') }}'"
           class="inline-flex items-center px-2 py-2 bg-white border border-grey-medium rounded text-sm text-grey-dark uppercase shadow-sm hover:bg-grey-light transition ease-in-out duration-150">
            Cancel
        </button>
        <x-primary-button>{{ $mode === 'edit' ? 'Update Project' : 'Create Project' }}</x-primary-button>
    </div>
</form>
