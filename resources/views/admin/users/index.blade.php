<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Users
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">

            {{-- ACTION BAR --}}
            <div class="mb-4 flex justify-end">
                <a href="{{ route('admin.users.create') }}"
                   class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white font-semibold px-4 py-2 rounded">
                    New User
                </a>
            </div>

            {{-- FILTERS --}}
            <form method="GET" class="mb-6 bg-white p-4 rounded shadow">
                <div class="grid grid-cols-1 md:grid-cols-5 gap-4">

                    {{-- NAME --}}
                    <input type="text"
                           name="name"
                           value="{{ request('name') }}"
                           placeholder="Name"
                           class="border rounded px-3 py-2">

                    {{-- EMAIL --}}
                    <input type="text"
                           name="email"
                           value="{{ request('email') }}"
                           placeholder="Email"
                           class="border rounded px-3 py-2">

                    {{-- PHONE --}}
                    <input type="text"
                           name="phone"
                           value="{{ request('phone') }}"
                           placeholder="Phone"
                           class="border rounded px-3 py-2">

                    {{-- IS_ACTIVE --}}
                    <select name="is_active" class="border rounded px-3 py-2">
                        <option value="">Active</option>
                        <option value="1" @selected(request('is_active')==='1')>Yes</option>
                        <option value="0" @selected(request('is_active')==='0')>No</option>
                    </select>

                    {{-- ACTIONS --}}
                    <div class="flex gap-2">
                        <a href="{{ route('admin.users.index') }}"
                           class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                            Reset
                        </a>
                        <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                            Filter
                        </button>
                    </div>
                </div>
            </form>

            {{-- TABLE --}}
            <div class="bg-white shadow rounded">
                <table class="min-w-full border">
                    <thead class="bg-gray-100">
                        <tr>
                            <th class="px-4 py-2 text-left">Name</th>
                            <th class="px-4 py-2 text-left">Email</th>
                            <th class="px-4 py-2 text-left">Phone</th>
                            <th class="px-4 py-2 text-left">Active</th>
                            <th class="px-4 py-2 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($users as $user)
                            <tr class="border-t">
                                <td class="px-4 py-2">
                                    {{ $user->name }}
                                </td>
                                <td class="px-4 py-2">
                                    {{ $user->email }}
                                </td>
                                <td class="px-4 py-2">
                                    {{ $user->phone ?? '-' }}
                                </td>
                                <td class="px-4 py-2">
                                    <span class="px-2 py-1 rounded text-xs {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        {{ $user->is_active ? 'Yes' : 'No' }}
                                    </span>
                                </td>
                                <td class="px-4 py-2 text-right space-x-2">
                                    <a href="{{ route('admin.users.show', $user) }}"
                                       class="inline-flex bg-gray-600 text-white px-3 py-1 rounded text-sm">
                                        View
                                    </a>
                                    <a href="{{ route('admin.users.edit', $user) }}"
                                       class="inline-flex bg-blue-600 text-white px-3 py-1 rounded text-sm">
                                        Edit
                                    </a>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="px-4 py-6 text-center text-gray-500">
                                    No users found.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="mt-6">
                {{ $users->links() }}
            </div>

        </div>
    </div>
</x-app-layout>
