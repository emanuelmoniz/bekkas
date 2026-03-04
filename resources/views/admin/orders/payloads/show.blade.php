<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-grey-dark">Easypay payload</h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded p-6 space-y-6">
            <div class="flex justify-between items-start gap-4">
                <dl class="grid grid-cols-1 gap-4">
                    <div>
                        <p class="text-xs text-grey-dark uppercase tracking-widest">Payload ID</p>
                        <p class="text-sm text-grey-dark mt-1">{{ $payload->id }}</p>
                    </div>
                    <div>
                        <p class="text-xs text-grey-dark uppercase tracking-widest">Order</p>
                        <p class="text-sm text-grey-dark mt-1">
                            @if($payload->order)
                                <a class="text-accent-primary hover:text-accent-primary/90 no-underline" href="{{ route('admin.orders.show', $payload->order) }}">{{ $payload->order->order_number }}</a>
                            @else
                                <span class="text-grey-medium">No order</span>
                            @endif
                        </p>
                    </div>
                    <div>
                        <p class="text-xs text-grey-dark uppercase tracking-widest">Created</p>
                        <p class="text-sm text-grey-dark mt-1">{{ $payload->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </dl>

                <div class="text-right flex flex-wrap justify-end items-center gap-2">
                    @if($payload->order)
                        <button type="button" onclick="window.location.href='{{ route('admin.orders.show', $payload->order) }}'" class="inline-block bg-grey-light border px-2 py-2 rounded uppercase text-sm">Order</button>

                        <button type="button" onclick="window.location.href='{{ route('admin.orders.checkouts.index', ['order_number' => $payload->order->order_number]) }}'" class="inline-block bg-white border px-2 py-2 rounded uppercase text-sm ms-2">Checkouts</button>

                        <button type="button" onclick="window.location.href='{{ route('admin.orders.payments.index', ['order_number' => $payload->order->order_number]) }}'" class="inline-block bg-white border px-2 py-2 rounded uppercase text-sm ms-2">Payments</button>

                        <form method="POST" action="{{ route('admin.orders.checkouts.store', $payload->order) }}" class="inline-block ms-2">
                            @csrf
                            <x-default-button type="submit">Create checkout</x-default-button>
                        </form>

                    @endif

                    <form method="POST" action="{{ route('admin.orders.payloads.destroy', $payload) }}" class="inline-block ms-2" onsubmit="return confirm('Delete this payload? This cannot be undone.');">
                        @csrf
                        @method('DELETE')
                        <x-default-button type="submit">Delete</x-default-button>
                    </form>

                    <x-default-button type="button" onclick="window.location.href='{{ route('admin.orders.payloads.index') }}'">Back</x-default-button>
                </div>
            </div>

            <div>
                <h3 class="text-xs text-grey-dark uppercase mb-2">Stored payload (JSON)</h3>
                <pre class="whitespace-pre-wrap bg-white border rounded p-4 text-sm overflow-auto" style="max-height:48vh">{{ json_encode($payload->payload, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES) }}</pre>
            </div>
        </div>
    </div>
</x-app-layout>
