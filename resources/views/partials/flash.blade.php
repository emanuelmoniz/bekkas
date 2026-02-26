@php
    // Determine server-supplied message + semantic type. Accept both modern
    // flashes (success/error/warning/info) and legacy session('status') tokens.
    $serverMessage = session('success') ?? session('error') ?? session('warning') ?? session('info') ?? null;
    $serverType = session('success') ? 'success' : (session('error') ? 'error' : (session('warning') ? 'warning' : (session('info') ? 'info' : null)));

    // Support legacy `session('status')` tokens (convert to a user-facing message).
    if (!$serverMessage && ($status = session('status'))) {
        $statusMap = [
            'verification-link-sent' => ['msg' => t('profile.verification_sent') ?: t('auth.activation_sent') ?: 'A new verification link has been sent to your email address.', 'type' => 'success'],
            'profile-updated'        => ['msg' => t('profile.updated_success') ?: 'Profile updated successfully!', 'type' => 'success'],
            'password-updated'       => ['msg' => t('profile.password_updated_success') ?: 'Password updated successfully!', 'type' => 'success'],
            'deletion-link-sent'     => ['msg' => t('profile.deletion_link_sent') ?: 'A deletion link has been sent to your email address.', 'type' => 'success'],
            'social-linked'          => ['msg' => t('profile.social_linked') ?: 'Social account linked.', 'type' => 'success'],
            'social-unlinked'        => ['msg' => t('profile.social_unlinked') ?: 'Social account unlinked.', 'type' => 'success'],
        ];

        if (is_string($status) && isset($statusMap[$status])) {
            $serverMessage = $statusMap[$status]['msg'];
            $serverType = $statusMap[$status]['type'];
        } elseif (is_string($status) && strlen(trim($status)) > 0) {
            // If controllers set a free-text status, treat it as a success message.
            $serverMessage = $status;
            $serverType = 'success';
        }
    }

    $hasServerMessage = (bool) $serverMessage;
@endphp

@if($hasServerMessage)
    {{-- ensure the raw HTML always contains the server message (defensive: helps tests and non-JS clients) --}}
    <div data-server-flash style="display:none">{{ $serverMessage }}</div>

    {{-- Ensure the client-side Alpine flash store reflects the server message type so
         the visual tone (success/error) matches the server-provided semantic. --}}
    <script>
    (function(){
        var serverType = {!! json_encode($serverType) !!};
        var serverMessage = {!! json_encode($serverMessage) !!};

        function applyServerFlash() {
            if (window.Alpine && Alpine.store && Alpine.store('flash')) {
                try {
                    // Prefer the server-provided type for styling; do not overwrite an
                    // already-user-set message if present.
                    Alpine.store('flash').type = serverType || Alpine.store('flash').type;
                    if (!Alpine.store('flash').message) Alpine.store('flash').message = serverMessage;
                } catch (e) {
                    // ignore
                }
            } else {
                document.addEventListener('DOMContentLoaded', applyServerFlash, {once:true});
            }
        }

        applyServerFlash();
    })();
    </script>
@endif

<!-- Canonical flash partial: server-rendered fallback + Alpine-driven runtime -->
<div data-flash-root x-data="{ localShow: {{ $hasServerMessage ? 'true' : 'false' }} }" x-show="Alpine.store('flash').show || localShow" x-cloak class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 mt-4" @unless($hasServerMessage) style="display:none" @endunless x-bind:aria-hidden="!(Alpine.store('flash').show || localShow)">
    <div class="px-4 py-3 rounded relative pr-12 border border-grey-light border-l-4"
         x-show="Alpine.store('flash').show || localShow"
         x-init="localShow && setTimeout(() => localShow = false, 6000)"
         x-transition
         x-bind:class="{
             'bg-status-success/10 border-status-success text-status-success': Alpine.store('flash').type === 'success' || {{ $serverType === 'success' ? 'true' : 'false' }},
             'bg-status-error/10 border-status-error text-status-error': Alpine.store('flash').type === 'error' || {{ $serverType === 'error' ? 'true' : 'false' }},
             'bg-status-warning/10 border-status-warning text-status-warning': Alpine.store('flash').type === 'warning' || {{ $serverType === 'warning' ? 'true' : 'false' }},
             'bg-status-info/10 border-status-info text-status-info': Alpine.store('flash').type === 'info' || {{ $serverType === 'info' ? 'true' : 'false' }},
         }"
         role="alert"
         x-bind:aria-live="(Alpine.store('flash').type === 'error' || {{ $serverType === 'error' ? 'true' : 'false' }}) ? 'assertive' : 'polite'"
         x-bind:aria-hidden="!(Alpine.store('flash').show || localShow)">

        <span class="block sm:inline" x-text="Alpine.store('flash').message || {{ $hasServerMessage ? json_encode($serverMessage) : json_encode('') }}">{{ $hasServerMessage ? $serverMessage : '' }}</span>

        <button @click="Alpine.store('flash').hide(); localShow = false"
                x-bind:tabindex="(Alpine.store('flash').show || localShow) ? 0 : -1"
                x-bind:aria-hidden="!(Alpine.store('flash').show || localShow)"
                class="absolute inset-y-0 right-0 flex items-center px-8"
                aria-label="Close flash message">
            <svg class="fill-current h-6 w-6" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" aria-hidden="true">
                <title>Close</title>
                <path d="M14.348 14.849a1.2 1.2 0 0 1-1.697 0L10 11.819l-2.651 3.029a1.2 1.2 0 1 1-1.697-1.697l2.758-3.15-2.759-3.152a1.2 1.2 0 1 1 1.697-1.697L10 8.183l2.651-3.031a1.2 1.2 0 1 1 1.697 1.697l-2.758 3.152 2.758 3.15a1.2 1.2 0 0 1 0 1.698z"/>
            </svg>
        </button>
    </div>
</div>
