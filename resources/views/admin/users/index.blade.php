<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-grey-dark leading-tight">
            Users
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">

        {{-- ACTION BAR --}}
        <div class="mb-4 flex justify-end">
            <button type="button" onclick="window.location.href='{{ route('admin.users.create') }}'"
        class="inline-flex items-center bg-primary hover:bg-primary/90 text-white px-2 py-2 rounded uppercase text-sm">
                New User
            </button>
        </div>

        {{-- FILTERS --}}
        <form method="GET" class="mb-6 bg-white p-4 rounded shadow">
            <div class="grid grid-cols-1 lg:grid-cols-5 gap-4">

                {{-- NAME --}}
                <input type="text"
                       name="name"
                       value="{{ request('name') }}"
                       placeholder="Name"
                       class="border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">

                {{-- EMAIL --}}
                <input type="text"
                       name="email"
                       value="{{ request('email') }}"
                       placeholder="Email"
                       class="border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">

                {{-- PHONE --}}
                <input type="text"
                       name="phone"
                       value="{{ request('phone') }}"
                       placeholder="Phone"
                       class="border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">

                {{-- IS_ACTIVE --}}
                <select name="is_active" class="border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">
                    <option value="">Active</option>
                    <option value="1" @selected(request('is_active')==='1')>Yes</option>
                    <option value="0" @selected(request('is_active')==='0')>No</option>
                </select>

                {{-- ACTIONS --}}
                <div class="flex justify-end gap-2">
                    <button type="button" onclick="window.location.href='{{ route('admin.users.index') }}'"
        class="bg-grey-light hover:bg-grey-medium text-grey-dark px-2 py-2 rounded uppercase text-sm">
                        Reset
                    </button>
                    <button type="submit" class="bg-primary hover:bg-primary/90 text-white px-2 py-2 rounded uppercase text-sm">
                        Filter
                    </button>
                </div>
            </div>
        </form>

        {{-- TABLE --}}
        <div class="bg-white shadow rounded">
            <table class="min-w-full border">
                <thead class="bg-grey-light">
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
                                <a href="{{ route('admin.users.show', $user) }}" class="text-accent-secondary hover:underline font-medium">{{ $user->name }}</a>
                            </td>
                            <td class="px-4 py-2">
                                {{ $user->email }}
                            </td>
                            <td class="px-4 py-2">
                                {{ $user->phone ?? '-' }}
                            </td>
                            <td class="px-4 py-2">
                                @if($user->is_active)
                                    <span class="text-status-success font-bold">&#10003;</span>
                                @else
                                    <span class="text-status-error font-bold">&#10007;</span>
                                @endif
                            </td>
                            <td class="px-4 py-2 text-right space-x-2">
                                <button type="button" onclick="window.location.href='{{ route('admin.users.edit', $user) }}'"
        class="inline-flex bg-primary text-white px-2 py-2 rounded uppercase text-sm">
                                    Edit
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5" class="px-4 py-6 text-center text-grey-medium">
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
</x-app-layout>
