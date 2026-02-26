<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-grey-dark leading-tight">
            Create New User
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">

        <form method="POST" action="{{ route('admin.users.store') }}">
            @csrf

            {{-- USER INFORMATION --}}
            <div class="bg-white p-6 rounded shadow mb-6 space-y-4">
                <h3 class="text-lg font-semibold border-b pb-2">User Information</h3>

                {{-- NAME --}}
                <div>
                    <x-input-label for="name">Name <span class="text-status-error">*</span></x-input-label>
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name')" required />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                {{-- EMAIL --}}
                <div>
                    <x-input-label for="email">Email <span class="text-status-error">*</span></x-input-label>
                    <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email')" required />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                {{-- PHONE --}}
                <div>
                    <x-input-label for="phone">Phone</x-input-label>
                    <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone')" />
                    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                </div>

                {{-- PASSWORD --}}
                <div>
                    <x-input-label for="password">Password <span class="text-status-error">*</span></x-input-label>
                    <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" required />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                {{-- PASSWORD CONFIRMATION --}}
                <div>
                    <x-input-label for="password_confirmation">Confirm Password <span class="text-status-error">*</span></x-input-label>
                    <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" required />
                </div>

                {{-- IS_ACTIVE --}}
                <div>
                    <label class="inline-flex items-center">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox"
                               name="is_active"
                               value="1"
                               @checked(old('is_active', true))
                               class="rounded border-grey-medium">
                        <span class="ml-2 font-semibold">Active</span>
                    </label>
                </div>
            </div>

            {{-- DEFAULT ADDRESS --}}
            <div class="bg-white p-6 rounded shadow mb-6 space-y-4">
                <h3 class="text-lg font-semibold border-b pb-2">Default Address</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- TITLE --}}
                    <div>
                        <x-input-label for="title">Title</x-input-label>
                        <x-text-input id="title" name="title" type="text" class="mt-1 block w-full" :value="old('title')" />
                        <x-input-error :messages="$errors->get('title')" class="mt-2" />
                    </div>

                    {{-- NIF --}}
                    <div>
                        <x-input-label for="nif">NIF</x-input-label>
                        <x-text-input id="nif" name="nif" type="text" class="mt-1 block w-full" :value="old('nif')" />
                        <x-input-error :messages="$errors->get('nif')" class="mt-2" />
                    </div>

                    {{-- ADDRESS LINE 1 --}}
                    <div class="md:col-span-2">
                        <x-input-label for="address_line_1">Address Line 1 <span class="text-status-error">*</span></x-input-label>
                        <x-text-input id="address_line_1" name="address_line_1" type="text" class="mt-1 block w-full" :value="old('address_line_1')" required />
                        <x-input-error :messages="$errors->get('address_line_1')" class="mt-2" />
                    </div>

                    {{-- ADDRESS LINE 2 --}}
                    <div class="md:col-span-2">
                        <x-input-label for="address_line_2">Address Line 2</x-input-label>
                        <x-text-input id="address_line_2" name="address_line_2" type="text" class="mt-1 block w-full" :value="old('address_line_2')" />
                        <x-input-error :messages="$errors->get('address_line_2')" class="mt-2" />
                    </div>

                    {{-- POSTAL CODE --}}
                    <div>
                        <x-input-label for="postal_code">Postal Code <span class="text-status-error">*</span></x-input-label>
                        <x-text-input id="postal_code" name="postal_code" type="text" class="mt-1 block w-full" :value="old('postal_code')" required />
                        <x-input-error :messages="$errors->get('postal_code')" class="mt-2" />
                    </div>

                    {{-- CITY --}}
                    <div>
                        <x-input-label for="city">City <span class="text-status-error">*</span></x-input-label>
                        <x-text-input id="city" name="city" type="text" class="mt-1 block w-full" :value="old('city')" required />
                        <x-input-error :messages="$errors->get('city')" class="mt-2" />
                    </div>

                    {{-- COUNTRY --}}
                    <div class="md:col-span-2">
                        <x-input-label for="country_id">Country <span class="text-status-error">*</span></x-input-label>
                        <select id="country_id" name="country_id" required class="mt-1 block w-full border-grey-medium focus:border-accent-primary focus:ring-accent-primary rounded-md shadow-sm">
                            @foreach ($countries as $country)
                                <option value="{{ $country->id }}" @selected(old('country_id') == $country->id)>
                                    {{ $country->name }}
                                </option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('country_id')" class="mt-2" />
                    </div>

                    {{-- PHONE --}}
                    <div>
                        <x-input-label for="address_phone">Phone</x-input-label>
                        <x-text-input id="address_phone" name="address_phone" type="text" class="mt-1 block w-full" :value="old('address_phone')" />
                        <x-input-error :messages="$errors->get('address_phone')" class="mt-2" />
                    </div>

                    {{-- IS DEFAULT --}}
                    <div class="flex items-center">
                        <label class="inline-flex items-center">
                            <input type="hidden" name="is_default" value="1">
                            <input type="checkbox"
                                   name="is_default"
                                   value="1"
                                   checked
                                   disabled
                                   class="rounded border-grey-medium opacity-50">
                            <span class="ml-2 font-semibold text-grey-dark">Default Address (automatic)</span>
                        </label>
                    </div>
                </div>
            </div>

            {{-- ACTIONS --}}
            <div class="flex justify-between">
                <a href="{{ route('admin.users.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-white border border-grey-medium rounded-md font-semibold text-xs text-grey-dark uppercase tracking-widest shadow-sm hover:bg-grey-light transition ease-in-out duration-150">
                    Cancel
                </a>
                <x-primary-button>Create User</x-primary-button>
            </div>

        </form>

    </div>
</x-app-layout>
