<section>
    <header class="mb-4">
        <h2 class="text-lg font-medium text-gray-900">Addresses</h2>
    </header>

    <div class="space-y-4 mb-6">
        @php $addressCount = $addresses->count(); @endphp

        @forelse ($addresses as $address)
            <div class="border p-4 rounded space-y-3">
                {{-- UPDATE FORM --}}
                <form method="POST" action="{{ route('addresses.update', $address) }}" class="space-y-2">
                    @csrf
                    @method('PATCH')

                    <div class="grid grid-cols-2 gap-2">
                        <input name="title" value="{{ $address->title }}" class="border rounded px-2 py-1" required>
                        <input name="nif" value="{{ $address->nif }}" class="border rounded px-2 py-1" required>
                        <input name="address_line_1" value="{{ $address->address_line_1 }}" class="border rounded px-2 py-1" required>
                        <input name="address_line_2" value="{{ $address->address_line_2 }}" class="border rounded px-2 py-1" required>
                        <input name="postal_code" value="{{ $address->postal_code }}" class="border rounded px-2 py-1" required>
                        <input name="city" value="{{ $address->city }}" class="border rounded px-2 py-1" required>
                        <input name="country" value="{{ $address->country }}" class="border rounded px-2 py-1" required>
                    </div>

                    <label class="flex items-center gap-2">
                        <input type="checkbox"
                               name="is_default"
                               value="1"
                               @checked($address->is_default)
                               @disabled($address->is_default)>
                        Default address
                    </label>

                    <button class="bg-indigo-600 text-white px-3 py-1 rounded">
                        Save
                    </button>
                </form>

                {{-- DELETE FORM (SEPARATE) --}}
                <form method="POST"
                      action="{{ route('addresses.destroy', $address) }}"
                      onsubmit="return confirm('Delete this address?')">
                    @csrf
                    @method('DELETE')

                    <button class="text-red-600 text-sm">
                        Delete
                    </button>
                </form>
            </div>
        @empty
            <p class="text-sm text-gray-600">No addresses yet.</p>
        @endforelse
    </div>

    {{-- ADD NEW ADDRESS --}}
    <form method="POST" action="{{ route('addresses.store') }}" class="border p-4 rounded space-y-2">
        @csrf
        <h3 class="font-medium">Add new address</h3>

        <div class="grid grid-cols-2 gap-2">
            <input name="title" placeholder="Title" class="border rounded px-2 py-1" required>
            <input name="nif" placeholder="NIF" class="border rounded px-2 py-1" required>
            <input name="address_line_1" placeholder="Address line 1" class="border rounded px-2 py-1" required>
            <input name="address_line_2" placeholder="Address line 2" class="border rounded px-2 py-1" required>
            <input name="postal_code" placeholder="Postal code" class="border rounded px-2 py-1" required>
            <input name="city" placeholder="City" class="border rounded px-2 py-1" required>
            <input name="country" placeholder="Country" class="border rounded px-2 py-1" required>
        </div>

        <label class="flex items-center gap-2 mt-2">
            <input type="checkbox" name="is_default" value="1">
            Default address
        </label>

        <button class="bg-green-600 text-white px-4 py-2 rounded mt-2">
            Add Address
        </button>
    </form>
</section>
