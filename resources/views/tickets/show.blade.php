<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $ticket->title }}
            </h2>

            <div class="flex gap-2">
                <a href="{{ route('tickets.index') }}"
                   class="bg-gray-300 px-4 py-2 rounded text-sm">
                    {{ t('tickets.back_to_tickets') ?: 'Back to tickets' }}
                </a>

                <form method="POST"
                      action="{{ route('tickets.mark-unread', $ticket) }}">
                    @csrf
                    <button type="submit"
                            class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded text-sm">
                        {{ t('tickets.mark_as_unread') ?: 'Mark as unread' }}
                    </button>
                </form>
            </div>
        </div>
    </x-slot>

    <div class="py-6 max-w-5xl mx-auto space-y-6">

        {{-- TICKET META --}}
        <div class="bg-white p-6 rounded shadow space-y-2">
            <div>
                <strong>{{ t('tickets.ticket_id') ?: 'Ticket ID' }}:</strong>
                <span class="font-mono text-sm">{{ $ticket->uuid }}</span>
            </div>

            <div>
                <strong>{{ t('tickets.user') ?: 'User' }}:</strong>
                {{ $ticket->owner?->name ?? '—' }}
            </div>

            <div>
                <strong>{{ t('tickets.category') ?: 'Category' }}:</strong>
                {{ optional($ticket->category?->translation())->name ?? '—' }}
            </div>

            @if ($ticket->due_date)
                <div>
                    <strong>{{ t('tickets.due_date') ?: 'Due Date' }}:</strong>
                    {{ $ticket->due_date->format('Y-m-d') }}
                </div>
            @endif

            <div>
                <strong>{{ t('tickets.status') ?: 'Status' }}:</strong>
                {{ ucfirst($ticket->status) }}
            </div>

            <div>
                <strong>{{ t('tickets.opened') ?: 'Opened' }}:</strong>
                {{ $ticket->opened_at }}
            </div>

            @if ($ticket->closed_at)
                <div>
                    <strong>{{ t('tickets.closed') ?: 'Closed' }}:</strong>
                    {{ $ticket->closed_at }}
                </div>
            @endif
        </div>

        {{-- CLOSE / REOPEN --}}
        <div class="bg-white p-6 rounded shadow">
            @if ($ticket->status === 'open')
                <form method="POST" action="{{ route('tickets.close', $ticket) }}">
                    @csrf
                    <label class="block font-semibold mb-1">{{ t('tickets.close_reason') ?: 'Close reason' }} *</label>
                    <textarea name="reason"
                              class="w-full border rounded px-3 py-2 mb-3"
                              required></textarea>
                    <button class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">
                        {{ t('tickets.close_ticket') ?: 'Close Ticket' }}
                    </button>
                </form>
            @else
                <form method="POST" action="{{ route('tickets.reopen', $ticket) }}">
                    @csrf
                    <label class="block font-semibold mb-1">{{ t('tickets.reopen_reason') ?: 'Reopen reason' }} *</label>
                    <textarea name="reason"
                              class="w-full border rounded px-3 py-2 mb-3"
                              required></textarea>
                    <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
                        {{ t('tickets.reopen_ticket') ?: 'Reopen Ticket' }}
                    </button>
                </form>
            @endif
        </div>

        {{-- MESSAGES --}}
        <div class="space-y-4">
            @foreach ($ticket->messages as $msg)
                <div class="bg-white p-4 rounded shadow">
                    <div class="text-sm text-gray-600 mb-1">
                        {{ $msg->is_system ? t('tickets.system', 'System') : ($msg->user?->name ?? '—') }}
                        · {{ $msg->created_at }}
                    </div>

                    <div class="whitespace-pre-line">
                        {{ $msg->message }}
                    </div>

                    @if ($msg->attachments->count())
                        <ul class="list-disc ml-5 text-sm mt-2">
                            @foreach ($msg->attachments as $file)
                                <li>
                                    <a href="{{ route('tickets.attachments.download', $file) }}"
                                       class="text-blue-600 hover:underline">
                                        {{ $file->original_name }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif
                </div>
            @endforeach
        </div>

        {{-- REPLY --}}
        @if ($ticket->status === 'open')
            <form method="POST"
                  action="{{ route('tickets.messages.store', $ticket) }}"
                  enctype="multipart/form-data"
                  class="bg-white p-6 rounded shadow space-y-4">
                @csrf

                <div>
                    <label class="block font-semibold mb-1">{{ t('tickets.new_message') ?: 'New message' }} *</label>
                    <textarea name="message"
                              rows="4"
                              class="w-full border rounded px-3 py-2"
                              required></textarea>
                </div>

                <input type="file" name="files[]" multiple>

                <!-- Google reCAPTCHA -->
                <div>
                    <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
                    @error('g-recaptcha-response')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <script>
                (function(){
                    var script = document.currentScript;
                    var container = (script && script.previousElementSibling && script.previousElementSibling.classList && script.previousElementSibling.classList.contains('g-recaptcha')) ? script.previousElementSibling : (script && script.parentElement && script.parentElement.querySelector('.g-recaptcha')) || document.querySelector('.g-recaptcha[data-sitekey]');
                    if (!container) { console.debug('[recaptcha] container not found (ticket show)'); return; }

                    function loadRecaptcha(){
                        if (window.__recaptchaLazyLoaded) return;
                        window.__recaptchaLazyLoaded = true;
                        console.debug('[recaptcha] loading script (ticket show)');
                        var s = document.createElement('script');
                        s.src = 'https://www.google.com/recaptcha/api.js';
                        s.async = true; s.defer = true;
                        s.onload = function(){
                            console.debug('[recaptcha] script loaded (ticket show)');
                            try{
                                var key = container.getAttribute('data-sitekey');
                                if (window.grecaptcha && typeof window.grecaptcha.render === 'function' && !container.querySelector('iframe')) {
                                    window.grecaptcha.render(container, { 'sitekey': key });
                                    console.debug('[recaptcha] rendered (ticket show)');
                                }
                            } catch(e) { console.error('[recaptcha] render error (ticket show)', e); }
                        };
                        s.onerror = function(e){ console.error('[recaptcha] failed to load (ticket show)', e); };
                        document.head.appendChild(s);
                    }

                    container.addEventListener('click', loadRecaptcha, {once:true});
                    container.addEventListener('mouseenter', loadRecaptcha, {once:true});
                    var f = container.closest('form'); if (f){
                        f.addEventListener('submit', loadRecaptcha, {once:true});
                        f.addEventListener('focusin', loadRecaptcha, {once:true});
                        f.querySelectorAll('input, textarea, button, select').forEach(function(el){ el.addEventListener('focus', loadRecaptcha, {once:true}); });
                    }

                    if ('IntersectionObserver' in window) {
                        var io = new IntersectionObserver(function(entries){
                            entries.forEach(function(entry){ if (entry.isIntersecting) { loadRecaptcha(); io.disconnect(); } });
                        }, {rootMargin: '200px'});
                        io.observe(container);
                    }
                })();
                </script>

                <div class="flex justify-end">
                    <button class="bg-blue-600 text-white px-6 py-2 rounded">
                        {{ t('tickets.send') ?: 'Send' }}
                    </button>
                </div>
            </form>
        @endif

    </div>


</x-app-layout>
