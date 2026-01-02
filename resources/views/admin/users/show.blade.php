<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            User Details
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            <form method="POST" action="{{ route('admin.users.update', $user) }}">
                @csrf
                @method('PUT')

                <div class="bg-white p-6 rounded shadow space-y-4">
                    <div>
                        <strong>Name:</strong> {{ $user->name }}
                    </div>

                    <div>
                        <strong>Email:</strong> {{ $user->email }}
                    </div>

                    <div>
                        <label class="block font-semibold mb-1">Role</label>
Attach
                        <select name="role" class="border rounded px-3 py-2 w-full">
                            <option value="client" @selected($user->hasRole('client'))>
                                Client
                            </option>
                            <option value="admin" @selected($user->hasRole('admin'))>
                                Admin
                            </option>
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold mb-1">Status</label>
                        <label class="inline-flex items-center">
                            <input type="checkbox" name="active" value="1" @checked($user->active)>
                            <span class="ml-2">Active</span>
                        </label>
                    </div>

                    <div class="flex justify-between">
                        <a href="{{ route('admin.users.index') }}"
                           class="bg-gray-300 hover:bg-gray-400 px-6 py-3 rounded">
                            Back
                        </a>

                        <button type="submit"
                                class="bg-green-600 hover:bg-green-700 text-white px-6 py-3 rounded">
                            Save Changes
                        </button>
                    </div>
                </div>

            </form>

        </div>
    </div>
</x-app-layout>
