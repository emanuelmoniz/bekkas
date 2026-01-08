<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            Edit Region
        </h2>
    </x-slot>

    <div class="py-6 max-w-xl mx-auto sm:px-6 lg:px-8">
        <form method="POST"
              action="{{ route('admin.regions.update', $region) }}"
              class="bg-white shadow rounded p-6 space-y-4">
            @csrf
            @method('PATCH')

            <div>
                <label class="block text-sm font-medium">Country *</label>
                <select name="country_id" required class="w-full border rounded px-3 py-2">
                    @foreach ($countries as $country)
                        <option value="{{ $country->id }}" @selected(old('country_id', $region->country_id) == $country->id)>
                            {{ app()->getLocale() === 'pt' ? $country->name_pt : $country->name_en }}
                        </option>
                    @endforeach
                </select>
                @error('country_id')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium">Name *</label>
                <input type="text"
                       name="name"
                       value="{{ old('name', $region->name) }}"
                       required
                       class="w-full border rounded px-3 py-2 @error('name') border-red-500 @enderror">
                @error('name')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium">Postal Code From *</label>
                <input type="text"
                       name="postal_code_from"
                       value="{{ old('postal_code_from', $region->postal_code_from) }}"
                       required
                       class="w-full border rounded px-3 py-2 @error('postal_code_from') border-red-500 @enderror">
                @error('postal_code_from')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <div>
                <label class="block text-sm font-medium">Postal Code To *</label>
                <input type="text"
                       name="postal_code_to"
                       value="{{ old('postal_code_to', $region->postal_code_to) }}"
                       required
                       class="w-full border rounded px-3 py-2 @error('postal_code_to') border-red-500 @enderror">
                @error('postal_code_to')
                    <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                @enderror
            </div>

            <label class="flex items-center gap-2">
                <input type="checkbox" name="is_active" value="1" @checked(old('is_active', $region->is_active))>
                Active
            </label>

            <div class="flex justify-between">
                <a href="{{ route('admin.regions.index') }}"
                   class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                    Cancel
                </a>
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                    Update
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
