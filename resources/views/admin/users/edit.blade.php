<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Edit User
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            {{-- USER INFORMATION --}}
            <form method="POST" action="{{ route('admin.users.update', $user) }}" class="mb-6">
                @csrf
                @method('PATCH')

                <div class="bg-white p-6 rounded shadow space-y-4">
                    <h3 class="text-lg font-semibold border-b pb-2">User Information</h3>

                    {{-- NAME --}}
                    <div>
                        <label class="block font-semibold mb-1">Name *</label>
                        <input type="text"
                               name="name"
                               value="{{ old('name', $user->name) }}"
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
                               value="{{ old('email', $user->email) }}"
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
                               value="{{ old('phone', $user->phone) }}"
                               class="border rounded px-3 py-2 w-full @error('phone') border-red-500 @enderror">
                        @error('phone')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- PASSWORD --}}
                    <div>
                        <label class="block font-semibold mb-1">New Password (leave blank to keep current)</label>
                        <input type="password"
                               name="password"
                               class="border rounded px-3 py-2 w-full @error('password') border-red-500 @enderror">
                        @error('password')
                            <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    {{-- PASSWORD CONFIRMATION --}}
                    <div>
                        <label class="block font-semibold mb-1">Confirm Password</label>
                        <input type="password"
                               name="password_confirmation"
                               class="border rounded px-3 py-2 w-full">
                    </div>

                    {{-- IS_ACTIVE --}}
                    <div>
                        <label class="inline-flex items-center">
                            <input type="hidden" name="is_active" value="0">
                            <input type="checkbox"
                                   name="is_active"
                                   value="1"
                                   @checked(old('is_active', $user->is_active))
                                   class="rounded border-gray-300">
                            <span class="ml-2 font-semibold">Active</span>
                        </label>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded">
                            Update User
                        </button>
                    </div>
                </div>
            </form>

            {{-- ADDRESSES --}}
            <div class="bg-white p-6 rounded shadow mb-6">
                <h3 class="text-lg font-semibold border-b pb-2 mb-4">Addresses</h3>

                @foreach ($user->addresses as $address)
                    <form method="POST" action="{{ route('admin.users.addresses.update', [$user, $address]) }}" class="border rounded p-4 mb-4">
                        @csrf
                        @method('PATCH')

                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                            {{-- TITLE --}}
                            <div>
                                <label class="block font-semibold mb-1">Title</label>
                                <input type="text"
                                       name="title"
                                       value="{{ old('title', $address->title) }}"
                                       class="border rounded px-3 py-2 w-full">
                            </div>

                            {{-- NIF --}}
                            <div>
                                <label class="block font-semibold mb-1">NIF</label>
                                <input type="text"
                                       name="nif"
                                       value="{{ old('nif', $address->nif) }}"
                                       class="border rounded px-3 py-2 w-full">
                            </div>

                            {{-- ADDRESS LINE 1 --}}
                            <div class="md:col-span-2">
                                <label class="block font-semibold mb-1">Address Line 1 *</label>
                                <input type="text"
                                       name="address_line_1"
                                       value="{{ old('address_line_1', $address->address_line_1) }}"
                                       required
                                       class="border rounded px-3 py-2 w-full">
                            </div>

                            {{-- ADDRESS LINE 2 --}}
                            <div class="md:col-span-2">
                                <label class="block font-semibold mb-1">Address Line 2</label>
                                <input type="text"
                                       name="address_line_2"
                                       value="{{ old('address_line_2', $address->address_line_2) }}"
                                       class="border rounded px-3 py-2 w-full">
                            </div>

                            {{-- POSTAL CODE --}}
                            <div>
                                <label class="block font-semibold mb-1">Postal Code *</label>
                                <input type="text"
                                       name="postal_code"
                                       value="{{ old('postal_code', $address->postal_code) }}"
                                       required
                                       class="border rounded px-3 py-2 w-full">
                            </div>

                            {{-- CITY --}}
                            <div>
                                <label class="block font-semibold mb-1">City *</label>
                                <input type="text"
                                       name="city"
                                       value="{{ old('city', $address->city) }}"
                                       required
                                       class="border rounded px-3 py-2 w-full">
                            </div>

                            {{-- COUNTRY --}}
                            <div class="md:col-span-2">
                                <label class="block font-semibold mb-1">Country *</label>
                                <select name="country_id" required class="border rounded px-3 py-2 w-full">
                                    @foreach ($countries as $country)
                                        <option value="{{ $country->id }}" @selected(old('country_id', $address->country_id) == $country->id)>
                                            {{ app()->getLocale() === 'pt' ? $country->name_pt : $country->name_en }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                            {{-- PHONE --}}
                            <div>
                                <label class="block font-semibold mb-1">Phone</label>
                                <input type="text"
                                       name="phone"
                                       value="{{ old('phone', $address->phone) }}"
                                       class="border rounded px-3 py-2 w-full">
                            </div>

                            {{-- IS DEFAULT --}}
                            <div class="flex items-center">
                                <label class="inline-flex items-center">
                                    <input type="hidden" name="is_default" value="0">
                                    <input type="checkbox"
                                           name="is_default"
                                           value="1"
                                           @checked(old('is_default', $address->is_default))
                                           class="rounded border-gray-300">
                                    <span class="ml-2 font-semibold">Default Address</span>
                                </label>
                            </div>
                        </div>

                        <div class="flex justify-end">
                            <button type="submit"
                                    class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded text-sm">
                                Update Address
                            </button>
                        </div>
                    </form>
                @endforeach

                {{-- ADD NEW ADDRESS --}}
                <form method="POST" action="{{ route('admin.users.addresses.store', $user) }}" class="border-2 border-dashed border-gray-300 rounded p-4 bg-gray-50">
                    @csrf

                    <h4 class="font-semibold mb-3">Add New Address</h4>

                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-4">
                        {{-- TITLE --}}
                        <div>
                            <label class="block font-semibold mb-1">Title</label>
                            <input type="text"
                                   name="title"
                                   class="border rounded px-3 py-2 w-full">
                        </div>

                        {{-- NIF --}}
                        <div>
                            <label class="block font-semibold mb-1">NIF</label>
                            <input type="text"
                                   name="nif"
                                   class="border rounded px-3 py-2 w-full">
                        </div>

                        {{-- ADDRESS LINE 1 --}}
                        <div class="md:col-span-2">
                            <label class="block font-semibold mb-1">Address Line 1 *</label>
                            <input type="text"
                                   name="address_line_1"
                                   required
                                   class="border rounded px-3 py-2 w-full">
                        </div>

                        {{-- ADDRESS LINE 2 --}}
                        <div class="md:col-span-2">
                            <label class="block font-semibold mb-1">Address Line 2</label>
                            <input type="text"
                                   name="address_line_2"
                                   class="border rounded px-3 py-2 w-full">
                        </div>

                        {{-- POSTAL CODE --}}
                        <div>
                            <label class="block font-semibold mb-1">Postal Code *</label>
                            <input type="text"
                                   name="postal_code"
                                   required
                                   class="border rounded px-3 py-2 w-full">
                        </div>

                        {{-- CITY --}}
                        <div>
                            <label class="block font-semibold mb-1">City *</label>
                            <input type="text"
                                   name="city"
                                   required
                                   class="border rounded px-3 py-2 w-full">
                        </div>

                        {{-- COUNTRY --}}
                        <div class="md:col-span-2">
                            <label class="block font-semibold mb-1">Country *</label>
                            <select name="country_id" required class="border rounded px-3 py-2 w-full">
                                @foreach ($countries as $country)
                                    <option value="{{ $country->id }}">
                                        {{ app()->getLocale() === 'pt' ? $country->name_pt : $country->name_en }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- PHONE --}}
                        <div>
                            <label class="block font-semibold mb-1">Phone</label>
                            <input type="text"
                                   name="phone"
                                   class="border rounded px-3 py-2 w-full">
                        </div>

                        {{-- IS DEFAULT --}}
                        <div class="flex items-center">
                            <label class="inline-flex items-center">
                                <input type="hidden" name="is_default" value="0">
                                <input type="checkbox"
                                       name="is_default"
                                       value="1"
                                       class="rounded border-gray-300">
                                <span class="ml-2 font-semibold">Default Address</span>
                            </label>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded text-sm">
                            Add Address
                        </button>
                    </div>
                </form>
            </div>

            {{-- BACK BUTTON --}}
            <div class="flex justify-start">
                <a href="{{ route('admin.users.index') }}"
                   class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded">
                    Back to Users
                </a>
            </div>

        </div>
    </div>
</x-app-layout>
