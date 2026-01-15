<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            User Details
        </h2>
    </x-slot>

    <div class="py-6">
        <div class="max-w-3xl mx-auto sm:px-6 lg:px-8">

            {{-- USER INFO --}}
            <div class="bg-white p-6 rounded shadow mb-6 space-y-4">
                <h3 class="text-lg font-semibold border-b pb-2">User Information</h3>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <strong class="text-gray-700">Name:</strong>
                        <p class="text-gray-900">{{ $user->name }}</p>
                    </div>

                    <div>
                        <strong class="text-gray-700">Email:</strong>
                        <p class="text-gray-900">{{ $user->email }}</p>
                    </div>

                    <div>
                        <strong class="text-gray-700">Phone:</strong>
                        <p class="text-gray-900">{{ $user->phone ?? '-' }}</p>
                    </div>

                    <div>
                        <strong class="text-gray-700">Active:</strong>
                        <p class="text-gray-900">
                            <span class="px-2 py-1 rounded text-xs {{ $user->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                {{ $user->is_active ? 'Yes' : 'No' }}
                            </span>
                        </p>
                    </div>
                </div>
            </div>

            {{-- ADDRESSES --}}
            <div class="bg-white p-6 rounded shadow mb-6">
                <h3 class="text-lg font-semibold border-b pb-2 mb-4">Addresses</h3>

                @forelse ($user->addresses as $address)
                    <div class="border rounded p-4 mb-3">
                        @if($address->title)
                            <p class="font-semibold text-lg mb-2">{{ $address->title }}</p>
                        @endif
                        <p>{{ $address->address_line_1 }}</p>
                        @if($address->address_line_2)
                            <p>{{ $address->address_line_2 }}</p>
                        @endif
                        <p>{{ $address->postal_code }}, {{ $address->city }}</p>
                        <p>{{ $address->country->name_en ?? '-' }}</p>
                        @if($address->nif)
                            <p class="text-sm text-gray-600">NIF: {{ $address->nif }}</p>
                        @endif
                        @if($address->phone)
                            <p class="text-sm text-gray-600">Phone: {{ $address->phone }}</p>
                        @endif
                        @if($address->is_default)
                            <span class="inline-block mt-2 px-2 py-1 bg-blue-100 text-blue-800 text-xs rounded">Default</span>
                        @endif
                    </div>
                @empty
                    <p class="text-gray-500">No addresses registered.</p>
                @endforelse
            </div>

            {{-- ACTIONS --}}
            <div class="flex justify-between">
                <a href="{{ route('admin.users.index') }}"
                   class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded">
                    Back
                </a>

                <a href="{{ route('admin.users.edit', $user) }}"
                   class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded">
                    Edit User
                </a>
            </div>

        </div>
    </div>
</x-app-layout>
