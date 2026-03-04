<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-grey-dark">Edit Locale</h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded p-6">
            <form method="POST" action="{{ route('admin.locales.update', $locale) }}" class="space-y-5">
                @csrf
                @method('PUT')

                <div>
                    <x-input-label for="code">Code <span class="text-status-error">*</span></x-input-label>
                    <x-text-input id="code" name="code" type="text" class="mt-1 block w-full font-mono" :value="old('code', $locale->code)" required />
                    <x-input-error :messages="$errors->get('code')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="name">Name <span class="text-status-error">*</span></x-input-label>
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $locale->name)" required />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="flag_emoji">Flag emoji</x-input-label>
                    <x-text-input id="flag_emoji" name="flag_emoji" type="text" class="mt-1 block w-full" :value="old('flag_emoji', $locale->flag_emoji)" />
                    <x-input-error :messages="$errors->get('flag_emoji')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="country_id">Country</x-input-label>
                    <select id="country_id" name="country_id" class="mt-1 block w-full border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">
                        <option value="">— none —</option>
                        @foreach ($countries as $country)
                            <option value="{{ $country->id }}"
                                {{ old('country_id', $locale->country_id) == $country->id ? 'selected' : '' }}>
                                {{ $country->name }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('country_id')" class="mt-2" />
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

                <div class="flex justify-between pt-2">
                    <button type="button"
                       onclick="window.location.href='{{ route('admin.locales.index') }}'"
                       class="inline-flex items-center px-2 py-2 bg-white border border-grey-medium rounded text-sm text-grey-dark uppercase shadow-sm hover:bg-grey-light">
                        Cancel
                    </button>
                    <x-primary-button>Save</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
