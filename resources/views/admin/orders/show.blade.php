<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            Order #{{ $order->id }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-4xl mx-auto sm:px-6 lg:px-8 space-y-6">

        <form method="POST"
              action="{{ route('admin.orders.update', $order) }}"
              class="bg-white shadow rounded p-4 space-y-3">
            @csrf
            @method('PATCH')

            <select name="status" class="border rounded px-2 py-1 w-full">
                @foreach (['PROCESSING','DISPATCHED','DELIVERED','CANCELED'] as $status)
                    <option value="{{ $status }}"
                        @selected($order->status === $status)>
                        {{ $status }}
                    </option>
                @endforeach
            </select>

            <input name="tracking_number"
                   value="{{ $order->tracking_number }}"
                   placeholder="Tracking number"
                   class="border rounded px-2 py-1 w-full">

            <label class="block">
                <input type="checkbox" name="is_paid" @checked($order->is_paid)>
                Paid
            </label>

            <label class="block">
                <input type="checkbox" name="is_canceled" @checked($order->is_canceled)>
                Canceled
            </label>

            <label class="block">
                <input type="checkbox" name="is_refunded" @checked($order->is_refunded)>
                Refunded
            </label>

            <button class="bg-blue-600 text-white px-4 py-2 rounded">
                Save
            </button>
        </form>

    </div>
</x-app-layout>
