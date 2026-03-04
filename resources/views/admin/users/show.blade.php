<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-grey-dark leading-tight">
            User Details
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">

        {{-- USER INFO --}}
        <div class="bg-white shadow rounded p-6 mb-6">
            <h3 class="text-xs text-grey-dark uppercase mb-4">User Information</h3>

            <dl class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div>
                    <p class="text-xs text-grey-dark uppercase tracking-widest">Name</p>
                    <p class="text-sm text-grey-dark mt-1">{{ $user->name }}</p>
                </div>

                <div>
                    <p class="text-xs text-grey-dark uppercase tracking-widest">Email</p>
                    <p class="text-sm text-grey-dark mt-1">{{ $user->email }}</p>
                </div>

                <div>
                    <p class="text-xs text-grey-dark uppercase tracking-widest">Phone</p>
                    <p class="text-sm text-grey-dark mt-1">{{ $user->phone ?? '-' }}</p>
                </div>

                <div>
                    <p class="text-xs text-grey-dark uppercase tracking-widest">Active</p>
                    <p class="text-sm text-grey-dark mt-1">
                        @if($user->is_active)
                            <span class="text-status-success font-bold">&#10003;</span>
                        @else
                            <span class="text-status-error font-bold">&#10007;</span>
                        @endif
                    </p>
                </div>
            </dl>
        </div>

        {{-- ADDRESSES --}}
        <div class="bg-white shadow rounded p-6 mb-6">
            <h3 class="text-xs text-grey-dark uppercase mb-4">Addresses</h3>

            @forelse ($user->addresses as $address)
                <div class="border border-grey-medium rounded p-4 mb-3">
                    @if($address->title)
                        <p class="font-semibold text-sm mb-2">{{ $address->title }}</p>
                    @endif
                    <p class="text-sm text-grey-dark">{{ $address->address_line_1 }}</p>
                    @if($address->address_line_2)
                        <p class="text-sm text-grey-dark">{{ $address->address_line_2 }}</p>
                    @endif
                    <p class="text-sm text-grey-dark">{{ $address->postal_code }}, {{ $address->city }}</p>
                    <p class="text-sm text-grey-dark">{{ $address->country?->name ?? '-' }}</p>
                    @if($address->nif)
                        <p class="text-sm text-grey-dark">NIF: {{ $address->nif }}</p>
                    @endif
                    @if($address->phone)
                        <p class="text-sm text-grey-dark">Phone: {{ $address->phone }}</p>
                    @endif
                    @if($address->is_default)
                        <span class="inline-block mt-2 px-2 py-1 bg-grey-light text-grey-dark text-sm rounded">Default</span>
                    @endif
                </div>
            @empty
                <p class="text-sm text-grey-medium">No addresses registered.</p>
            @endforelse
        </div>

        {{-- ACTIONS --}}
        <div class="flex justify-between mt-6">
            <button type="button"
               onclick="window.location.href='{{ route('admin.users.index') }}'"
               class="inline-flex items-center px-2 py-2 bg-white border border-grey-medium rounded text-sm text-grey-dark uppercase shadow-sm hover:bg-grey-light">
                Back
            </button>

            <button type="button"
               onclick="window.location.href='{{ route('admin.users.edit', $user) }}'"
               class="inline-flex items-center px-2 py-2 bg-primary border border-transparent rounded text-sm text-white uppercase hover:bg-primary/90 transition ease-in-out duration-150">
                Edit User
            </button>
        </div>

    </div>
</x-app-layout>
