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
                <h3 class="text-lg border-b pb-2">User Information</h3>

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

            {{-- ROLES --}}
            <div class="bg-white p-6 rounded shadow mb-6 space-y-4">
                <h3 class="text-lg border-b pb-2">Roles</h3>

                <div class="grid grid-cols-2 gap-2">
                    @foreach($roles as $role)
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="roles[]" value="{{ $role->id }}" @checked(in_array($role->id, old('roles', []))) class="rounded border-grey-medium">
                            <span class="ml-2">{{ $role->name }}</span>
                        </label>
                    @endforeach
                </div>

                <x-input-error :messages="$errors->get('roles')" class="mt-2" />
            </div>

            {{-- Addresses are managed on the user edit page --}}

            {{-- ACTIONS --}}
            <div class="flex justify-between">
                <x-default-button type="button" onclick="window.location.href='{{ route('admin.users.index') }}'">
                    Cancel
                </x-default-button>
                <x-default-button>Create User</x-default-button>
            </div>

        </form>

    </div>
</x-app-layout>
