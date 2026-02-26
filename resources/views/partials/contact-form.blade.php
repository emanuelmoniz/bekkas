@include('partials.flash')

<form id="contact-form" method="POST" action="{{ route('contact.store') }}"
      data-msg-name-required="{{ t('validation.name_required') ?: 'Please enter your name.' }}"
      data-msg-email-invalid="{{ t('validation.email_invalid') ?: 'Please enter a valid email address.' }}"
      data-msg-message-required="{{ t('validation.message_required') ?: 'Please enter your message.' }}"
      data-msg-validation-failed="{{ t('contact.validation_failed') ?: 'Please correct the errors below and try again.' }}">
    @csrf

    <div class="mb-6">
        <label for="name" class="block text-sm font-medium text-grey-dark mb-2">
            {{ t('contact.name') ?: 'Name' }}
        </label>
        <input type="text" id="name" name="name" required
               value="{{ old('name') }}"
               class="w-full px-4 py-2 rounded-lg border border-grey-medium focus:outline-none focus:border-accent-primary focus:ring-1 focus:ring-primary">
        @error('name')
            <p class="text-primary text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div class="mb-6">
        <label for="email" class="block text-sm font-medium text-grey-dark mb-2">
            {{ t('contact.email') ?: 'Email' }}
        </label>
        <input type="email" id="email" name="email" required
               value="{{ old('email') }}"
               class="w-full px-4 py-2 rounded-lg border border-grey-medium focus:outline-none focus:border-accent-primary focus:ring-1 focus:ring-primary">
        @error('email')
            <p class="text-primary text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div class="mb-6">
        <label for="message" class="block text-sm font-medium text-grey-dark mb-2">
            {{ t('contact.message') ?: 'Message' }}
        </label>
        <textarea id="message" name="message" rows="5" required
                  class="w-full px-4 py-2 rounded-lg border border-grey-medium focus:outline-none focus:border-accent-primary focus:ring-1 focus:ring-primary">{{ old('message') }}</textarea>
        @error('message')
            <p class="text-primary text-sm mt-1">{{ $message }}</p>
        @enderror
    </div>

    <div class="mb-6 g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
    @error('g-recaptcha-response')
        <p class="text-primary text-sm mt-1">{{ $message }}</p>
    @enderror

    <button type="submit" 
            class="bg-primary hover:bg-primary/90 text-white px-8 py-3 rounded-full uppercase font-semibold transition-colors">
        {{ t('contact.send') ?: 'Send Message' }}
    </button>
</form>
