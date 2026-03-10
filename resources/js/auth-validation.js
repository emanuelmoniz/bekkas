// Client-side auth form validation using flash messages (instead of browser native prompts)
(function () {
    document.addEventListener('DOMContentLoaded', function () {
        var forms = document.querySelectorAll('form[data-auth-validation="true"]');
        if (!forms.length) return;

        var emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

        function showFlash(message) {
            if (window.Alpine && Alpine.store && Alpine.store('flash')) {
                Alpine.store('flash').showMessage(message, 'error');
            } else {
                alert(message);
            }
        }

        forms.forEach(function (form) {
            var messages = {
                emailInvalid: form.dataset.msgEmailInvalid || 'Please enter a valid email address.',
                validationFailed: form.dataset.msgValidationFailed || 'Please correct the errors below and try again.'
            };

            if (form.dataset.hasServerErrors === '1') {
                showFlash(messages.validationFailed);
            }

            form.addEventListener('submit', function (ev) {
                var firstInvalid = null;
                var hasInvalid = false;
                var flashMessage = messages.validationFailed;

                var fields = form.querySelectorAll('input, textarea, select');
                fields.forEach(function (field) {
                    field.removeAttribute('aria-invalid');
                    field.classList.remove('border-status-error', 'focus:border-status-error');
                });

                var requiredFields = form.querySelectorAll('input[required], textarea[required], select[required]');
                requiredFields.forEach(function (field) {
                    var emptyValue = !String(field.value || '').trim();
                    var unchecked = (field.type === 'checkbox' || field.type === 'radio') && !field.checked;

                    if (emptyValue || unchecked) {
                        hasInvalid = true;
                        if (!firstInvalid) firstInvalid = field;
                        field.setAttribute('aria-invalid', 'true');
                        field.classList.add('border-status-error', 'focus:border-status-error');
                    }
                });

                var emailFields = form.querySelectorAll('input[type="email"]');
                emailFields.forEach(function (field) {
                    var value = String(field.value || '').trim();
                    if (!value) return;

                    if (!emailPattern.test(value)) {
                        hasInvalid = true;
                        flashMessage = messages.emailInvalid;
                        if (!firstInvalid) firstInvalid = field;
                        field.setAttribute('aria-invalid', 'true');
                        field.classList.add('border-status-error', 'focus:border-status-error');
                    }
                });

                if (hasInvalid) {
                    ev.preventDefault();
                    showFlash(flashMessage);
                    if (firstInvalid && typeof firstInvalid.focus === 'function') {
                        firstInvalid.focus();
                    }
                }
            });
        });
    });
})();
