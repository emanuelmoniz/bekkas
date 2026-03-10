@php
    // Server-provided flash support
    $serverMessage = session('success') ?? session('error') ?? session('warning') ?? session('info') ?? null;
    $serverType = session('success') ? 'success' : (session('error') ? 'error' : (session('warning') ? 'warning' : (session('info') ? 'info' : null)));

    // Legacy `session('status')` mappings (keep minimal and explicit)
    if (!$serverMessage && ($status = session('status'))) {
        $map = [
            'verification-link-sent' => ['msg' => t('profile.verification_sent') ?: t('auth.activation_sent') ?: 'A new verification link has been sent to your email address.', 'type' => 'success'],
            'profile-updated' => ['msg' => t('profile.updated_success') ?: 'Profile updated successfully!', 'type' => 'success'],
            'password-updated' => ['msg' => t('profile.password_updated_success') ?: 'Password updated successfully!', 'type' => 'success'],
        ];
        if (is_string($status) && isset($map[$status])) {
            $serverMessage = $map[$status]['msg'];
            $serverType = $map[$status]['type'];
        } elseif (is_string($status) && trim($status) !== '') {
            $serverMessage = $status;
            $serverType = 'success';
        }
    }

    // If there is no explicit server flash, surface the first validation error
    if (! $serverMessage && isset($errors) && $errors->any()) {
        $serverMessage = $errors->first();
        $serverType = 'error';
    }

    $hasServerMessage = (bool) $serverMessage;
    $wrapInLayout = empty($forceInline);
    $escapedServerMessage = $hasServerMessage ? $serverMessage : '';
@endphp

@if($hasServerMessage)
    <div data-server-flash style="display:none">{{ $escapedServerMessage }}</div>
    <script>
    (function(){
        var serverType = {!! json_encode($serverType) !!};
        var serverMessage = {!! json_encode($escapedServerMessage) !!};

        function applyServerFlash(){
            try {
                if (window.Alpine && Alpine.store && Alpine.store('flash')) {
                    var store = Alpine.store('flash');
                    store.type = serverType || store.type;
                    if (!store.message) store.message = serverMessage;
                    // Ensure the message is visible on first render
                    store.show = true;
                }
            } catch (e) {
                // ignore
            }
        }

        if (document.readyState === 'loading') {
            document.addEventListener('DOMContentLoaded', applyServerFlash, { once: true });
        } else {
            applyServerFlash();
        }
    })();
    </script>
@endif

<!-- Flash container (single canonical implementation) -->
<div
    data-flash-root
    x-data="{ localShow: {{ $hasServerMessage ? 'true' : 'false' }} }"
    x-show="Alpine.store('flash').show || localShow"
    x-cloak
    @unless($hasServerMessage) style="display:none" @endunless
    x-bind:aria-hidden="!(Alpine.store('flash').show || localShow)"
    @if($wrapInLayout) class="sticky top-16 z-40 w-full bg-white border-b border-grey-light" @endif
>
    @if($wrapInLayout)
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-2">
    @endif

    @php $flashBase = 'w-full px-4 py-3 rounded relative pr-12 border border-grey-light border-l-4'; @endphp

    <div
        class="{{ $flashBase }}"
        x-show="Alpine.store('flash').show || localShow"
        x-transition
        x-bind:class="{
            'bg-status-success/10 border-status-success text-status-success': Alpine.store('flash').type === 'success' || {!! json_encode($serverType === 'success') !!},
            'bg-status-error/10 border-status-error text-status-error': Alpine.store('flash').type === 'error' || {!! json_encode($serverType === 'error') !!},
            'bg-status-warning/10 border-status-warning text-status-warning': Alpine.store('flash').type === 'warning' || {!! json_encode($serverType === 'warning') !!},
            'bg-status-info/10 border-status-info text-status-info': Alpine.store('flash').type === 'info' || {!! json_encode($serverType === 'info') !!},
        }"
        role="alert"
        x-bind:aria-live="(Alpine.store('flash').type === 'error' || {!! json_encode($serverType === 'error') !!}) ? 'assertive' : 'polite'"
        x-bind:aria-hidden="!(Alpine.store('flash').show || localShow)"
    >

        <span class="block sm:inline" data-default-message="{{ $hasServerMessage ? e($escapedServerMessage) : '' }}" x-text="Alpine.store('flash').message || $el.getAttribute('data-default-message')">{{ $hasServerMessage ? e($escapedServerMessage) : '' }}</span>

        <button
            @click="Alpine.store('flash').hide(); localShow = false"
            x-bind:tabindex="(Alpine.store('flash').show || localShow) ? 0 : -1"
            x-bind:aria-hidden="!(Alpine.store('flash').show || localShow)"
            class="absolute inset-y-0 right-0 flex items-center px-8"
            aria-label="Close flash message"
        >
            <svg class="fill-current h-6 w-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" aria-hidden="true">
                <title>Close</title>
                <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
            </svg>
        </button>

    </div>

    @if($wrapInLayout)
        </div>
    @endif

</div>
