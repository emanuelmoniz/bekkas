<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-grey-dark">
            New Country
        </h2>
    </x-slot>

    <div class="py-6 max-w-xl mx-auto sm:px-6 lg:px-8">
        <form method="POST"
              action="{{ route('admin.countries.store') }}"
              class="bg-light shadow rounded p-6 space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium">Name PT</label>
                <input type="text"
                       name="name_pt"
                       value="{{ old('name_pt') }}"
                       required
                       class="w-full border rounded px-3 py-2">
            </div>

            <div>
                <label class="block text-sm font-medium">Name EN</label>
                <input type="text"
                       name="name_en"
                       value="{{ old('name_en') }}"
                       required
                       class="w-full border rounded px-3 py-2">
            </div>

            <div>
                <label class="block text-sm font-medium">ISO 3166 Alpha-2</label>
                <input type="text"
                       name="iso_alpha2"
                       value="{{ old('iso_alpha2') }}"
                       maxlength="2"
                       required
                       class="w-full border rounded px-3 py-2">
            </div>

            <div>
                <label class="block text-sm font-medium">Country Code</label>
                <input type="text"
                       name="country_code"
                       value="{{ old('country_code') }}"
                       placeholder="+351"
                       required
                       class="w-full border rounded px-3 py-2">
            </div>

            <label class="flex items-center gap-2">
                <input type="checkbox" name="is_active" checked>
                Active
            </label>

            <div class="flex justify-end">
                <button type="submit"
                        class="bg-accent-primary hover:bg-accent-primary/90 text-light px-4 py-2 rounded">
                    Save
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
