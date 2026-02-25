<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-grey-dark">Edit Locale</h2>
    </x-slot>

    <div class="py-6 max-w-xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded p-6">
            <form method="POST" action="{{ route('admin.locales.update', $locale) }}" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <label class="block font-medium mb-1">Code <span class="text-status-error">*</span></label>
                    <input name="code" value="{{ old('code', $locale->code) }}" required
                           class="border rounded px-3 py-2 w-full font-mono">
                    @error('code')
                        <p class="text-status-error text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block font-medium mb-1">Name <span class="text-status-error">*</span></label>
                    <input name="name" value="{{ old('name', $locale->name) }}" required
                           class="border rounded px-3 py-2 w-full">
                    @error('name')
                        <p class="text-status-error text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block font-medium mb-1">Flag emoji</label>
                    <input name="flag_emoji" value="{{ old('flag_emoji', $locale->flag_emoji) }}"
                           class="border rounded px-3 py-2 w-full">
                    @error('flag_emoji')
                        <p class="text-status-error text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block font-medium mb-1">Country</label>
                    <select name="country_id" class="border rounded px-3 py-2 w-full">
                        <option value="">— none —</option>
                        @foreach ($countries as $country)
                            <option value="{{ $country->id }}"
                                {{ old('country_id', $locale->country_id) == $country->id ? 'selected' : '' }}>
                                {{ $country->name }}
                            </option>
                        @endforeach
                    </select>
                    @error('country_id')
                        <p class="text-status-error text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="flex items-center gap-6">
                    <label class="inline-flex items-center gap-2">
                        <input type="checkbox" name="is_active" value="1"
                               {{ old('is_active', $locale->is_active) ? 'checked' : '' }}>
                        Active
                    </label>
                    <label class="inline-flex items-center gap-2">
                        <input type="checkbox" name="is_default" value="1"
                               {{ old('is_default', $locale->is_default) ? 'checked' : '' }}>
                        Default locale
                    </label>
                </div>

                <div class="flex gap-3 pt-2">
                    <button type="submit"
                            class="bg-accent-primary hover:bg-accent-primary/90 text-light px-6 py-2 rounded">
                        Save
                    </button>
                    <a href="{{ route('admin.locales.index') }}"
                       class="bg-grey-medium hover:bg-grey-dark text-light px-6 py-2 rounded">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
