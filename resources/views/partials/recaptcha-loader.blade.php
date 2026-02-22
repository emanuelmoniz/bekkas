{{--
  Shared lazy-loader for Google reCAPTCHA v2 (checkbox widget).
  Include this once per page, inside a @push('recaptcha') ... @endpush block.
  The script discovers every .g-recaptcha[data-sitekey] container on the page
  and defers loading the Google API until the user first interacts with a form
  or the widget scrolls into view.
  The global flag window.__recaptchaLazyLoaded prevents the API from being
  appended to <head> more than once (safe across multiple forms on the same page).
--}}
<script>
(function () {
    // Nothing to do if the API was already loaded (e.g. this partial is included twice).
    if (window.__recaptchaLazyLoaded) return;

    function init() {
        if (window.__recaptchaLazyLoaded) return;

        var containers = document.querySelectorAll('.g-recaptcha[data-sitekey]');
        console.debug('[recaptcha-loader] init, found %d containers', containers.length);
        if (!containers.length) return;

        function loadRecaptcha() {
            console.debug('[recaptcha-loader] loadRecaptcha called');
            if (window.__recaptchaLazyLoaded) return;
            window.__recaptchaLazyLoaded = true;

            var s = document.createElement('script');
            s.src = 'https://www.google.com/recaptcha/api.js';
            s.async = true;
            s.defer = true;
            s.onload = function () {
                containers.forEach(function (c) {
                    var key = c.getAttribute('data-sitekey');
                    if (!key) return;
                    try {
                        if (window.grecaptcha && typeof window.grecaptcha.render === 'function' && !c.querySelector('iframe')) {
                            window.grecaptcha.render(c, { sitekey: key });
                        }
                    } catch (e) {
                        console.error('[recaptcha] render error', e);
                    }
                });
            };
            s.onerror = function (e) { console.error('[recaptcha] failed to load', e); };
            document.head.appendChild(s);
        }

        // Trigger on any interaction with a container or its parent form.
        var forms = new Set();
        containers.forEach(function (c) {
            c.addEventListener('click', loadRecaptcha, { once: true });
            c.addEventListener('mouseenter', loadRecaptcha, { once: true });
            var f = c.closest('form');
            if (f) forms.add(f);
        });

        forms.forEach(function (f) {
            f.addEventListener('submit', loadRecaptcha, { once: true });
            f.addEventListener('focusin', loadRecaptcha, { once: true });
            f.querySelectorAll('input, textarea, button, select').forEach(function (el) {
                el.addEventListener('focus', loadRecaptcha, { once: true });
            });
        });

        // Also load when any container enters the viewport (200 px margin).
        if ('IntersectionObserver' in window) {
            var io = new IntersectionObserver(function (entries) {
                entries.forEach(function (entry) {
                    if (entry.isIntersecting) { loadRecaptcha(); io.disconnect(); }
                });
            }, { rootMargin: '200px' });
            containers.forEach(function (c) { io.observe(c); });
        }
    }

    if (document.readyState === 'loading') {
        document.addEventListener('DOMContentLoaded', init);
    } else {
        init();
    }
})();
</script>
