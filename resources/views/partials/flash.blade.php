@php
    $serverMessage = session('success') ?? session('error') ?? session('warning') ?? session('info');
    $hasServerMessage = (bool) $serverMessage;
@endphp

@if($hasServerMessage)
    {{-- ensure the raw HTML always contains the server message (defensive: helps tests and non-JS clients) --}}
    <div data-server-flash style="display:none">{{ $serverMessage }}</div>
@endif

<!-- Canonical flash partial: server-rendered fallback + Alpine-driven runtime -->
<div data-flash-root x-data="{ localShow: {{ $hasServerMessage ? 'true' : 'false' }} }" x-show="Alpine.store('flash').show || localShow" x-cloak class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4" @unless($hasServerMessage) style="display:none" @endunless x-bind:aria-hidden="!(Alpine.store('flash').show || localShow)">
    <div class="px-4 py-3 rounded relative pr-12"
         x-show="Alpine.store('flash').show || localShow"
         x-init="localShow && setTimeout(() => localShow = false, 6000)"
         x-transition
         x-bind:class="{
             'bg-green-100 border border-green-400 text-green-700': Alpine.store('flash').type === 'success' || {{ session('success') ? 'true' : 'false' }},
             'bg-red-100 border border-red-400 text-red-700': Alpine.store('flash').type === 'error' || {{ session('error') ? 'true' : 'false' }},
             'bg-amber-50 border border-amber-200 text-amber-700': Alpine.store('flash').type === 'warning' || {{ session('warning') ? 'true' : 'false' }},
             'bg-blue-100 border border-blue-400 text-blue-700': Alpine.store('flash').type === 'info' || {{ session('info') ? 'true' : 'false' }},
         }"
         role="alert"
         x-bind:aria-live="(Alpine.store('flash').type === 'error' || {{ session('error') ? 'true' : 'false' }}) ? 'assertive' : 'polite'"
         x-bind:aria-hidden="!(Alpine.store('flash').show || localShow)">

        <span class="block sm:inline" x-text="Alpine.store('flash').message || {{ $hasServerMessage ? json_encode($serverMessage) : json_encode('') }}">{{ $hasServerMessage ? $serverMessage : '' }}</span>

        <button @click="Alpine.store('flash').hide(); localShow = false"
                x-bind:tabindex="(Alpine.store('flash').show || localShow) ? 0 : -1"
                x-bind:aria-hidden="!(Alpine.store('flash').show || localShow)"
                class="absolute inset-y-0 right-0 flex items-center px-4"
                aria-label="Close flash message">
            <svg class="fill-current h-6 w-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" aria-hidden="true">
                <title>Close</title>
                <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
            </svg>
        </button>
    </div>
</div>
