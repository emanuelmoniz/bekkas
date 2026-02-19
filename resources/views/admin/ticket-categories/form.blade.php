<div class="py-6 max-w-xl mx-auto">
    <form method="POST"
          action="{{ $action }}"
          class="bg-light p-6 rounded shadow space-y-4">
        @csrf
        @if ($method !== 'POST')
            @method($method)
        @endif

        <div>
            <label class="block font-semibold mb-1">Name (PT-PT) *</label>
            <input type="text"
                   name="name[pt-PT]"
                   class="w-full border rounded px-3 py-2"
                   value="{{ $category?->translations->where('locale','pt-PT')->first()?->name }}"
                   required>
        </div>

        <div>
            <label class="block font-semibold mb-1">Name (EN-UK) *</label>
            <input type="text"
                   name="name[en-UK]"
                   class="w-full border rounded px-3 py-2"
                   value="{{ $category?->translations->where('locale','en-UK')->first()?->name }}"
                   required>
        </div>

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
