<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-grey-dark leading-tight">
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
                        <strong class="text-grey-dark">Name:</strong>
                        <p class="text-dark">{{ $user->name }}</p>
                    </div>

                    <div>
                        <strong class="text-grey-dark">Email:</strong>
                        <p class="text-dark">{{ $user->email }}</p>
                    </div>

                    <div>
                        <strong class="text-grey-dark">Phone:</strong>
                        <p class="text-dark">{{ $user->phone ?? '-' }}</p>
                    </div>

                    <div>
                        <strong class="text-grey-dark">Active:</strong>
                        <p class="text-dark">
                            <span class="px-2 py-1 rounded text-xs {{ $user->is_active ? 'bg-status-success text-status-success' : 'bg-status-error/10 text-status-error' }}">
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
                        <p>{{ $address->country?->name ?? '-' }}</p>
                        @if($address->nif)
                            <p class="text-sm text-grey-dark">NIF: {{ $address->nif }}</p>
                        @endif
                        @if($address->phone)
                            <p class="text-sm text-grey-dark">Phone: {{ $address->phone }}</p>
                        @endif
                        @if($address->is_default)
                            <span class="inline-block mt-2 px-2 py-1 bg-blue-100 text-accent-primary text-xs rounded">Default</span>
                        @endif
                    </div>
                @empty
                    <p class="text-grey-medium">No addresses registered.</p>
                @endforelse
            </div>

            {{-- ACTIONS --}}
            <div class="flex justify-between">
                <a href="{{ route('admin.users.index') }}"
                   class="bg-grey-medium hover:bg-grey-dark text-light px-6 py-3 rounded">
                    Back
                </a>

                <a href="{{ route('admin.users.edit', $user) }}"
                   class="bg-accent-primary hover:bg-accent-primary/90 text-light px-6 py-3 rounded">
                    Edit User
                </a>
            </div>

        </div>
    </div>
</x-app-layout>
