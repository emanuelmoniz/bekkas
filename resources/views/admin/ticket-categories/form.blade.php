<div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
    <form method="POST"
          action="{{ $action }}"
          class="bg-white p-6 rounded shadow space-y-4">
        @csrf
        @if ($method !== 'POST')
            @method($method)
        @endif

        {{-- Slug --}}
        <div>
            <x-input-label>Slug <span class="text-status-error">*</span></x-input-label>
            <input type="text"
                   name="slug"
                   class="mt-1 block w-full border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm font-mono text-sm"
                   value="{{ old('slug') ?? $category?->slug }}"
                   placeholder="e.g. rnd, preparation, print"
                   required>
            <p class="text-xs text-grey-dark mt-1">Lowercase, no spaces. Used to link the custom page service cards to this category.</p>
            <x-input-error :messages="$errors->get('slug')" class="mt-2" />
        </div>

        @foreach (\App\Models\Locale::activeList() as $locale => $label)
        <div class="border border-grey-light rounded-lg p-4 space-y-3">
            <p class="text-xs font-bold uppercase tracking-widest text-grey-dark">{{ $label }}</p>

            {{-- Name --}}
            <div>
                <x-input-label>Name @if($locale === $defaultLocale)<span class="text-status-error">*</span>@endif</x-input-label>
                <input type="text"
                       name="name[{{ $locale }}]"
                       class="mt-1 block w-full border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm"
                       value="{{ old('name.' . $locale) ?? $category?->translations->where('locale', $locale)->first()?->name }}"
                       @if($locale === $defaultLocale) required @endif>
                <x-input-error :messages="$errors->get('name.'.$locale)" class="mt-2" />
            </div>

            {{-- Description --}}
            <div>
                <x-input-label>Description <span class="text-xs font-normal text-grey-dark">(shown in ticket creation form)</span></x-input-label>
                <textarea name="description[{{ $locale }}]"
                          rows="6"
                          class="mt-1 block w-full border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm text-sm"
                          >{{ old('description.' . $locale) ?? $category?->translations->where('locale', $locale)->first()?->description }}</textarea>
                <x-input-error :messages="$errors->get('description.'.$locale)" class="mt-2" />
            </div>
        </div>
        @endforeach

        <div>
            <label class="inline-flex items-center">
                <input type="checkbox"
                       name="active"
                       value="1"
                       @checked($category?->active ?? true)>
                <span class="ml-2">Active</span>
            </label>
        </div>

        <div class="flex justify-between">
            <x-default-button type="button" onclick="window.location.href='{{ route('admin.ticket-categories.index') }}'">
                Cancel
            </x-default-button>
            <x-default-button>Save</x-default-button>
        </div>
    </form>
</div>
