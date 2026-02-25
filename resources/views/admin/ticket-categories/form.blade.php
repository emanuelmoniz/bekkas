<div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
    <form method="POST"
          action="{{ $action }}"
          class="bg-white p-6 rounded shadow space-y-4">
        @csrf
        @if ($method !== 'POST')
            @method($method)
        @endif

        @foreach (\App\Models\Locale::activeList() as $locale => $label)
        <div>
            <x-input-label>Name ({{ $label }}) <span class="text-status-error">*</span></x-input-label>
            <input type="text"
                   name="name[{{ $locale }}]"
                   class="mt-1 block w-full border-grey-medium focus:border-accent-primary focus:ring-accent-primary rounded-md shadow-sm"
                   value="{{ old('name.' . $locale) ?? $category?->translations->where('locale', $locale)->first()?->name }}">
            <x-input-error :messages="$errors->get('name.'.$locale)" class="mt-2" />
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
            <a href="{{ route('admin.ticket-categories.index') }}"
               class="inline-flex items-center px-4 py-2 bg-white border border-grey-medium rounded-md font-semibold text-xs text-grey-dark uppercase tracking-widest shadow-sm hover:bg-grey-light transition ease-in-out duration-150">
                Cancel
            </a>
            <x-primary-button>Save</x-primary-button>
        </div>
    </form>
</div>
