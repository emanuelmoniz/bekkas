<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Easypay payload</h2>
    </x-slot>

    <div class="py-6 max-w-4xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-4">
            <nav class="flex gap-2 text-sm" aria-label="Admin orders subnav">
                <a href="{{ route('admin.orders.index') }}" class="px-3 py-2 rounded hover:bg-gray-50">Orders</a>
                <a href="{{ route('admin.orders.payloads.index') }}" class="px-3 py-2 rounded hover:bg-gray-50">Payloads</a>
            </nav>
        </div>

        <div class="bg-white shadow rounded p-4 space-y-4">
            <div class="flex justify-between items-start gap-4">
                <div>
                    <p><strong>Payload ID:</strong> {{ $payload->id }}</p>
                    <p><strong>Order:</strong>
                        @if($payload->order)
                            <a class="text-blue-600 hover:underline" href="{{ route('admin.orders.show', $payload->order) }}">{{ $payload->order->order_number }}</a>
                        @else
                            <span class="text-sm text-gray-500">No order</span>
                        @endif
                    </p>
                    <p><strong>Created:</strong> {{ $payload->created_at->format('d/m/Y H:i') }}</p>
                </div>

                <div class="text-right">
                    @if($payload->order)
                        <a href="{{ route('admin.orders.show', $payload->order) }}" class="inline-block bg-gray-100 border px-4 py-2 rounded text-sm">View order</a>
                    @endif
                    <a href="{{ route('admin.orders.payloads.index') }}" class="inline-block bg-white border px-4 py-2 rounded text-sm ms-2">Back</a>
                </div>
            </div>

            <div>
                <h3 class="font-semibold mb-2">Stored payload (JSON)</h3>
                <pre class="whitespace-pre-wrap bg-gray-50 border rounded p-4 text-sm overflow-auto" style="max-height:48vh">{{ json_encode($payload->payload, JSON_PRETTY_PRINT|JSON_UNESCAPED_SLASHES) }}</pre>
            </div>
        </div>
    </div>
</x-app-layout>
