<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Create New User
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <form method="POST" action="{{ route('admin.users.store') }}">
                @csrf

                {{-- USER INFORMATION --}}
                <div class="bg-white p-6 rounded shadow mb-6 space-y-4">
                    <h3 class="text-lg font-semibold border-b pb-2">User Information</h3>

                    {{-- NAME --}}
                    <div>
                        <label class="block font-semibold mb-1">Name *</label>
                        <input type="text"
                               name="name"
                               value="{{ old('name') }}"
                               required
                               class="border rounded px-3 py-2 w-full @error('name') border-red-500 @enderror">
                        @error('name')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- EMAIL --}}
                    <div>
                        <label class="block font-semibold mb-1">Email *</label>
                        <input type="email"
                               name="email"
                               value="{{ old('email') }}"
                               required
                               class="border rounded px-3 py-2 w-full @error('email') border-red-500 @enderror">
                        @error('email')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- PHONE --}}
                    <div>
                        <label class="block font-semibold mb-1">Phone</label>
                        <input type="text"
                               name="phone"
                               value="{{ old('phone') }}"
                               class="border rounded px-3 py-2 w-full @error('phone') border-red-500 @enderror">
                        @error('phone')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- PASSWORD --}}
                    <div>
                        <label class="block font-semibold mb-1">Password *</label>
                        <input type="password"
                               name="password"
                               required
                               class="border rounded px-3 py-2 w-full @error('password') border-red-500 @enderror">
                        @error('password')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- PASSWORD CONFIRMATION --}}
                    <div>
                        <label class="block font-semibold mb-1">Confirm Password *</label>
                        <input type="password"
                               name="password_confirmation"
                               required
                               class="border rounded px-3 py-2 w-full">
                    </div>

                    {{-- IS_ACTIVE --}}
                    <div>
                        <label class="inline-flex items-center">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox"
                                   name="is_active"
                                   value="1"
                                   @checked(old('is_active', true))
                                   class="rounded border-gray-300">
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
                            <label class="block font-semibold mb-1">Title</label>
                            <input type="text"
                                   name="title"
                                   value="{{ old('title') }}"
                                   class="border rounded px-3 py-2 w-full @error('title') border-red-500 @enderror">
                            @error('title')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- NIF --}}
                        <div>
                            <label class="block font-semibold mb-1">NIF</label>
                            <input type="text"
                                   name="nif"
                                   value="{{ old('nif') }}"
                                   class="border rounded px-3 py-2 w-full @error('nif') border-red-500 @enderror">
                            @error('nif')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- ADDRESS LINE 1 --}}
                        <div class="md:col-span-2">
                            <label class="block font-semibold mb-1">Address Line 1 *</label>
                            <input type="text"
                                   name="address_line_1"
                                   value="{{ old('address_line_1') }}"
                                   required
                                   class="border rounded px-3 py-2 w-full @error('address_line_1') border-red-500 @enderror">
                            @error('address_line_1')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- ADDRESS LINE 2 --}}
                        <div class="md:col-span-2">
                            <label class="block font-semibold mb-1">Address Line 2</label>
                            <input type="text"
                                   name="address_line_2"
                                   value="{{ old('address_line_2') }}"
                                   class="border rounded px-3 py-2 w-full @error('address_line_2') border-red-500 @enderror">
                            @error('address_line_2')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- POSTAL CODE --}}
                        <div>
                            <label class="block font-semibold mb-1">Postal Code *</label>
                            <input type="text"
                                   name="postal_code"
                                   value="{{ old('postal_code') }}"
                                   required
                                   class="border rounded px-3 py-2 w-full @error('postal_code') border-red-500 @enderror">
                            @error('postal_code')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- CITY --}}
                        <div>
                            <label class="block font-semibold mb-1">City *</label>
                            <input type="text"
                                   name="city"
                                   value="{{ old('city') }}"
                                   required
                                   class="border rounded px-3 py-2 w-full @error('city') border-red-500 @enderror">
                            @error('city')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- COUNTRY --}}
                        <div class="md:col-span-2">
                            <label class="block font-semibold mb-1">Country *</label>
                            <select name="country_id" required class="border rounded px-3 py-2 w-full @error('country_id') border-red-500 @enderror">
                                @foreach ($countries as $country)
                                    <option value="{{ $country->id }}" @selected(old('country_id') == $country->id)>
                                        {{ app()->getLocale() === 'pt' ? $country->name_pt : $country->name_en }}
                                    </option>
                                @endforeach
                            </select>
                            @error('country_id')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        {{-- PHONE --}}
                        <div>
                            <label class="block font-semibold mb-1">Phone</label>
                            <input type="text"
                                   name="address_phone"
                                   value="{{ old('address_phone') }}"
                                   class="border rounded px-3 py-2 w-full @error('address_phone') border-red-500 @enderror">
                            @error('address_phone')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
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
                                       class="rounded border-gray-300 opacity-50">
                                <span class="ml-2 font-semibold text-gray-600">Default Address (automatic)</span>
                            </label>
                        </div>
                    </div>
                </div>

                {{-- ACTIONS --}}
                <div class="flex justify-between">
                    <a href="{{ route('admin.users.index') }}"
                       class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded">
                        Cancel
                    </a>

                    <button type="submit"
                            class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded">
                        Create User
                    </button>
                </div>

            </form>

        </div>
    </div>
</x-app-layout>
