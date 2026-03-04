<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-grey-dark leading-tight">
            Edit User
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">

        {{-- USER INFORMATION --}}
        <form method="POST" action="{{ route('admin.users.update', $user) }}" class="mb-6">
            @csrf
            @method('PATCH')

            <div class="bg-white p-6 rounded shadow space-y-4">
                <h3 class="text-lg border-b pb-2">User Information</h3>

                {{-- NAME --}}
                <div>
                    <x-input-label for="name">Name <span class="text-status-error">*</span></x-input-label>
                    <x-text-input id="name" name="name" type="text" class="mt-1 block w-full" :value="old('name', $user->name)" required />
                    <x-input-error :messages="$errors->get('name')" class="mt-2" />
                </div>

                {{-- EMAIL --}}
                <div>
                    <x-input-label for="email">Email <span class="text-status-error">*</span></x-input-label>
                    <x-text-input id="email" name="email" type="email" class="mt-1 block w-full" :value="old('email', $user->email)" required />
                    <x-input-error :messages="$errors->get('email')" class="mt-2" />
                </div>

                {{-- PHONE --}}
                <div>
                    <x-input-label for="phone">Phone</x-input-label>
                    <x-text-input id="phone" name="phone" type="text" class="mt-1 block w-full" :value="old('phone', $user->phone)" />
                    <x-input-error :messages="$errors->get('phone')" class="mt-2" />
                </div>

                {{-- PASSWORD --}}
                <div>
                    <x-input-label for="password">New Password <span class="font-normal text-grey-dark">(leave blank to keep current)</span></x-input-label>
                    <x-text-input id="password" name="password" type="password" class="mt-1 block w-full" />
                    <x-input-error :messages="$errors->get('password')" class="mt-2" />
                </div>

                {{-- PASSWORD CONFIRMATION --}}
                <div>
                    <x-input-label for="password_confirmation">Confirm Password</x-input-label>
                    <x-text-input id="password_confirmation" name="password_confirmation" type="password" class="mt-1 block w-full" />
                </div>

                {{-- IS_ACTIVE --}}
                <div>
                    <label class="inline-flex items-center">
                        <input type="hidden" name="is_active" value="0">
                        <input type="checkbox"
                               name="is_active"
                               value="1"
                               @checked(old('is_active', $user->is_active))
                               class="rounded border-grey-medium">
                        <span class="ml-2 font-semibold">Active</span>
                    </label>
                </div>

                <div class="flex justify-between">
                    <x-default-button type="button" onclick="window.location.href='{{ route('admin.users.index') }}'">
                        Cancel
                    </x-default-button>
                    <x-default-button>Update User</x-default-button>
                </div>
            </div>
        </form>

        {{-- ADDRESSES --}}
        <div class="bg-white p-6 rounded shadow mb-6">
            <h3 class="text-lg border-b pb-2 mb-4">Addresses</h3>

            @foreach ($user->addresses as $address)
                <form method="POST" action="{{ route('admin.users.addresses.update', [$user, $address]) }}" class="border rounded p-4 mb-4">
                    @csrf
                    @method('PATCH')

                    <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
                        {{-- TITLE --}}
                        <div>
                            <x-input-label for="title_{{ $address->id }}">Title</x-input-label>
                            <x-text-input id="title_{{ $address->id }}" name="title" type="text" class="mt-1 block w-full" :value="old('title', $address->title)" />
                        </div>

                        {{-- NIF --}}
                        <div>
                            <x-input-label for="nif_{{ $address->id }}">NIF</x-input-label>
                            <x-text-input id="nif_{{ $address->id }}" name="nif" type="text" class="mt-1 block w-full" :value="old('nif', $address->nif)" />
                        </div>

                        {{-- ADDRESS LINE 1 --}}
                        <div class="lg:col-span-2">
                            <x-input-label>Address Line 1 <span class="text-status-error">*</span></x-input-label>
                            <x-text-input name="address_line_1" type="text" class="mt-1 block w-full" :value="old('address_line_1', $address->address_line_1)" required />
                        </div>

                        {{-- ADDRESS LINE 2 --}}
                        <div class="lg:col-span-2">
                            <x-input-label>Address Line 2</x-input-label>
                            <x-text-input name="address_line_2" type="text" class="mt-1 block w-full" :value="old('address_line_2', $address->address_line_2)" />
                        </div>

                        {{-- POSTAL CODE --}}
                        <div>
                            <x-input-label>Postal Code <span class="text-status-error">*</span></x-input-label>
                            <x-text-input name="postal_code" type="text" class="mt-1 block w-full" :value="old('postal_code', $address->postal_code)" required />
                        </div>

                        {{-- CITY --}}
                        <div>
                            <x-input-label>City <span class="text-status-error">*</span></x-input-label>
                            <x-text-input name="city" type="text" class="mt-1 block w-full" :value="old('city', $address->city)" required />
                        </div>

                        {{-- COUNTRY --}}
                        <div class="lg:col-span-2">
                            <x-input-label>Country <span class="text-status-error">*</span></x-input-label>
                            <select name="country_id" required class="mt-1 block w-full border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">
                                @foreach ($countries as $country)
                                    <option value="{{ $country->id }}" @selected(old('country_id', $address->country_id) == $country->id)>
                                        {{ $country->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        {{-- PHONE --}}
                        <div>
                            <x-input-label>Phone</x-input-label>
                            <x-text-input name="phone" type="text" class="mt-1 block w-full" :value="old('phone', $address->phone)" />
                        </div>

                        {{-- IS DEFAULT --}}
                        <div class="flex items-center">
                            <label class="inline-flex items-center">
                                <input type="hidden" name="is_default" value="0">
                                <input type="checkbox"
                                       name="is_default"
                                       value="1"
                                       @checked(old('is_default', $address->is_default))
                                       class="rounded border-grey-medium">
                                <span class="ml-2 font-semibold">Default Address</span>
                            </label>
                        </div>
                    </div>

                    <div class="flex justify-end">
                        <x-default-button>Update Address</x-default-button>
                    </div>
                </form>
            @endforeach

            {{-- ADD NEW ADDRESS --}}
            <form method="POST" action="{{ route('admin.users.addresses.store', $user) }}" class="border-2 border-dashed border-grey-medium rounded p-4 bg-white">
                @csrf

                <h4 class="font-semibold mb-3">Add New Address</h4>

                <div class="grid grid-cols-1 lg:grid-cols-2 gap-4 mb-4">
                    {{-- TITLE --}}
                    <div>
                        <x-input-label for="new_title">Title</x-input-label>
                        <x-text-input id="new_title" name="title" type="text" class="mt-1 block w-full" />
                    </div>

                    {{-- NIF --}}
                    <div>
                        <x-input-label for="new_nif">NIF</x-input-label>
                        <x-text-input id="new_nif" name="nif" type="text" class="mt-1 block w-full" />
                    </div>

                    {{-- ADDRESS LINE 1 --}}
                    <div class="lg:col-span-2">
                        <x-input-label>Address Line 1 <span class="text-status-error">*</span></x-input-label>
                        <x-text-input name="address_line_1" type="text" class="mt-1 block w-full" required />
                    </div>

                    {{-- ADDRESS LINE 2 --}}
                    <div class="lg:col-span-2">
                        <x-input-label>Address Line 2</x-input-label>
                        <x-text-input name="address_line_2" type="text" class="mt-1 block w-full" />
                    </div>

                    {{-- POSTAL CODE --}}
                    <div>
                        <x-input-label>Postal Code <span class="text-status-error">*</span></x-input-label>
                        <x-text-input name="postal_code" type="text" class="mt-1 block w-full" required />
                    </div>

                    {{-- CITY --}}
                    <div>
                        <x-input-label>City <span class="text-status-error">*</span></x-input-label>
                        <x-text-input name="city" type="text" class="mt-1 block w-full" required />
                    </div>

                    {{-- COUNTRY --}}
                    <div class="lg:col-span-2">
                        <x-input-label>Country <span class="text-status-error">*</span></x-input-label>
                        <select name="country_id" required class="mt-1 block w-full border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">
                            @foreach ($countries as $country)
                                <option value="{{ $country->id }}">
                                    {{ $country->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    {{-- PHONE --}}
                    <div>
                        <x-input-label>Phone</x-input-label>
                        <x-text-input name="phone" type="text" class="mt-1 block w-full" />
                    </div>

                    {{-- IS DEFAULT --}}
                    <div class="flex items-center">
                        <label class="inline-flex items-center">
                            <input type="hidden" name="is_default" value="0">
                            <input type="checkbox"
                                   name="is_default"
                                   value="1"
                                   class="rounded border-grey-medium">
                            <span class="ml-2 font-semibold">Default Address</span>
                        </label>
                    </div>
                </div>

                <div class="flex justify-end">
                    <x-default-button>Add Address</x-default-button>
                </div>
            </form>
        </div>

        {{-- BACK BUTTON --}}
        <div class="flex justify-start">
            <x-default-button type="button" onclick="window.location.href='{{ route('admin.users.index') }}'">
                Back to Users
            </x-default-button>
        </div>

    </div>
</x-app-layout>
