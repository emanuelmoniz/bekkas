@props(['status'])

@if ($status)
    {{-- delegate inline status display to the global flash UI (server-side handled by partials/flash) --}}
    <script>
        (function () {
            try {
                if (window.Alpine && Alpine.store && Alpine.store('flash')) {
                    Alpine.store('flash').showMessage({!! json_encode($status) !!}, 'success');
                }
            } catch (e) { /* ignore */ }
        })();
    </script>
@endif
