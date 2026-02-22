// client-side contact form validation logic
// the blade template adds translated message strings to data-* attributes on the form

(function(){
    document.addEventListener('DOMContentLoaded', function(){
        var form = document.getElementById('contact-form');
        if (!form) return;

        var emailInput = form.querySelector('input[name="email"]');
        var nameInput = form.querySelector('input[name="name"]');
        var messageInput = form.querySelector('textarea[name="message"]');

        var messages = {
            nameRequired: form.dataset.msgNameRequired || 'Please enter your name.',
            emailInvalid: form.dataset.msgEmailInvalid || 'Please enter a valid email address.',
            messageRequired: form.dataset.msgMessageRequired || 'Please enter your message.',
            validationFailed: form.dataset.msgValidationFailed || 'Please correct the errors below and try again.'
        };

        var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/; // simple client-side TLD check

        function makeClientError(field, msg) {
            field.setAttribute('aria-invalid', 'true');
            field.classList.add('border-status-error', 'focus:border-status-error');

            var existing = field.parentElement.querySelector('.js-client-error');
            if (existing) { existing.textContent = msg; return; }

            var p = document.createElement('p');
            p.className = 'text-primary text-sm mt-1 js-client-error';
            p.setAttribute('role','alert');
            p.textContent = msg;
            field.parentElement.appendChild(p);
        }

        function clearClientError(field) {
            field.removeAttribute('aria-invalid');
            field.classList.remove('border-status-error', 'focus:border-status-error');
            var e = field.parentElement.querySelector('.js-client-error');
            if (e) e.remove();
        }

        function validateClient() {
            var ok = true;

            if (!nameInput.value.trim()) { makeClientError(nameInput, messages.nameRequired); ok = false; } else clearClientError(nameInput);

            var emailVal = (emailInput.value || '').trim();
            if (!emailVal || !emailPattern.test(emailVal)) { makeClientError(emailInput, messages.emailInvalid); ok = false; } else clearClientError(emailInput);

            if (!messageInput.value.trim()) { makeClientError(messageInput, messages.messageRequired); ok = false; } else clearClientError(messageInput);

            return ok;
        }

        form.addEventListener('submit', function(ev){
            if (!validateClient()) {
                ev.preventDefault();
                if (window.Alpine && Alpine.store && Alpine.store('flash')) {
                    Alpine.store('flash').showMessage(messages.validationFailed, 'error');
                } else {
                    alert(messages.validationFailed);
                }

                var firstInvalid = form.querySelector('[aria-invalid="true"]');
                if (firstInvalid) firstInvalid.focus();
            }
        });

        [nameInput, emailInput, messageInput].forEach(function(f){
            f.addEventListener('input', function(){
                if (f === emailInput) {
                    if (emailPattern.test(f.value.trim())) clearClientError(f);
                } else {
                    if (f.value.trim()) clearClientError(f);
                }
            });
        });
    });
})();
