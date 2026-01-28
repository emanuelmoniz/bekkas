<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Order {{ $order->order_number }} — Payment</h2>
    </x-slot>

    <div class="py-6 max-w-5xl mx-auto sm:px-6 lg:px-8 space-y-6">
        <div class="bg-white shadow rounded p-4">
            <h3 class="font-semibold mb-2">Easypay payload</h3>

            @if($payload)
                <pre class="bg-gray-100 p-4 rounded overflow-auto text-sm" style="max-height:420px">{{ json_encode($payload->payload, JSON_PRETTY_PRINT|JSON_UNESCAPED_UNICODE) }}</pre>
            @else
                <p class="text-gray-600">No payload found for this order.</p>
            @endif
        </div>

        <div class="bg-white shadow rounded p-4">
            <div class="flex items-center justify-between">
                <h3 class="font-semibold mb-2">Checkout sessions</h3>
                <div>
                    <button id="create-session-btn" data-url="{{ route('orders.pay.session', $order->uuid) }}" class="inline-flex items-center bg-blue-600 hover:bg-blue-700 text-white px-3 py-2 rounded">
                        <svg id="create-session-spinner" class="animate-spin -ml-1 mr-2 h-4 w-4 hidden" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"></path></svg>
                        <span id="create-session-label">Create checkout session</span>
                    </button>
                </div>
            </div>

            <div id="easypay-sessions" class="mt-4 space-y-4">
                @if($sessions->isEmpty())
                    <p class="text-gray-600">No checkout sessions created yet.</p>
                @else
                    @foreach($sessions as $s)
                        @include('orders._session', ['s' => $s])
                    @endforeach
                @endif
            </div>
        </div>

        <script>
            (function(){
                const btn = document.getElementById('create-session-btn');
                if (!btn) return;
                const spinner = document.getElementById('create-session-spinner');
                const label = document.getElementById('create-session-label');
                const container = document.getElementById('easypay-sessions');

                btn.addEventListener('click', async function () {
                    const url = btn.dataset.url;
                    btn.disabled = true;
                    spinner.classList.remove('hidden');
                    label.textContent = 'Creating...';

                    try {
                        const resp = await fetch(url, {
                            method: 'POST',
                            headers: {
                                'Accept': 'application/json',
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
                            },
                            body: JSON.stringify({})
                        });

                        const json = await resp.json();
                        if (resp.status === 201 && json.ok) {
                            // Insert returned HTML at the top
                            const wrapper = document.createElement('div');
                            wrapper.innerHTML = json.html;
                            container.prepend(wrapper.firstElementChild);
                        } else {
                            alert(json.message || 'Failed to create checkout session');
                        }
                    } catch (err) {
                        console.error(err);
                        alert('Request failed — check console');
                    } finally {
                        btn.disabled = false;
                        spinner.classList.add('hidden');
                        label.textContent = 'Create checkout session';
                    }
                });
            })();
        </script>

        <div class="text-right">
            <a href="{{ route('orders.show', $order->uuid) }}" class="text-sm text-gray-600 hover:underline">Back to order</a>
        </div>
    </div>
</x-app-layout>
