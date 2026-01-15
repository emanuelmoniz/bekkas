<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ t('tickets.open_ticket') ?: 'Open Ticket' }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-4xl mx-auto">

        <form method="POST"
              action="{{ route('tickets.store') }}"
              enctype="multipart/form-data">
            @csrf

            <div class="bg-white p-6 rounded shadow mb-6 space-y-4">

                <div>
                    <label class="block font-semibold mb-1">{{ t('tickets.category') ?: 'Category' }} *</label>
                    <select name="ticket_category_id" class="w-full border rounded px-3 py-2" required>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">
                                {{ optional($category->translation())->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-semibold mb-1">{{ t('tickets.title') ?: 'Title' }} *</label>
                    <input type="text"
                           name="title"
                           class="w-full border rounded px-3 py-2"
                           required>
                </div>

                <div>
                    <label class="block font-semibold mb-1">{{ t('tickets.message') ?: 'Message' }} *</label>
                    <textarea name="message"
                              rows="5"
                              class="w-full border rounded px-3 py-2"
                              required></textarea>
                </div>

                <div>
                    <label class="block font-semibold mb-1">{{ t('tickets.due_date') ?: 'Due Date' }}</label>
                    <input type="date"
                           name="due_date"
                           class="border rounded px-3 py-2">
                </div>

                <div>
                    <label class="block font-semibold mb-1">{{ t('tickets.files') ?: 'Files' }}</label>
                    <input type="file"
                           name="files[]"
                           multiple>
                </div>

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
                    if (!container) { console.debug('[recaptcha] container not found (ticket create)'); return; }

                    function loadRecaptcha(){
                        if (window.__recaptchaLazyLoaded) return;
                        window.__recaptchaLazyLoaded = true;
                        console.debug('[recaptcha] loading script (ticket create)');
                        var s = document.createElement('script');
                        s.src = 'https://www.google.com/recaptcha/api.js';
                        s.async = true; s.defer = true;
                        s.onload = function(){
                            console.debug('[recaptcha] script loaded (ticket create)');
                            try{
                                var key = container.getAttribute('data-sitekey');
                                if (window.grecaptcha && typeof window.grecaptcha.render === 'function' && !container.querySelector('iframe')) {
                                    window.grecaptcha.render(container, { 'sitekey': key });
                                    console.debug('[recaptcha] rendered (ticket create)');
                                }
                            } catch(e) { console.error('[recaptcha] render error (ticket create)', e); }
                        };
                        s.onerror = function(e){ console.error('[recaptcha] failed to load (ticket create)', e); };
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
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('tickets.index') }}"
                   class="bg-gray-300 px-6 py-2 rounded">
                    {{ t('tickets.cancel') ?: 'Cancel' }}
                </a>
                <button class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded">
                    {{ t('tickets.submit') ?: 'Open Ticket' }}
                </button>
            </div>
        </form>

    </div>


</x-app-layout>
