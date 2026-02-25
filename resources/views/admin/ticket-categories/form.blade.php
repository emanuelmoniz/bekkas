<div class="py-6 max-w-xl mx-auto">
    <form method="POST"
          action="{{ $action }}"
          class="bg-white p-6 rounded shadow space-y-4">
        @csrf
        @if ($method !== 'POST')
            @method($method)
        @endif

        @foreach (\App\Models\Locale::activeList() as $locale => $label)
        <div>
            <label class="block font-semibold mb-1">Name ({{ $label }}) *</label>
            <input type="text"
                   name="name[{{ $locale }}]"
                   class="w-full border rounded px-3 py-2{{ $errors->has('name.' . $locale) ? ' border-red-500' : '' }}"
                   value="{{ old('name.' . $locale) ?? $category?->translations->where('locale', $locale)->first()?->name }}">
            @if ($errors->has('name.' . $locale))
                <p class="text-red-600 text-sm mt-1">{{ $errors->first('name.' . $locale) }}</p>
            @endif
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
               class="bg-grey-medium px-4 py-2 rounded">
                Cancel
            </a>

            <button class="bg-accent-primary text-light px-6 py-2 rounded">
                Save
            </button>
        </div>
    </form>
</div>
