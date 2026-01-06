@component('mail::message')
# {{ t('contact.email.admin_heading') ?: 'New Contact Message' }}

**{{ t('contact.email.name_label') ?: 'Name' }}:** {{ $name }}

**{{ t('contact.email.email_label') ?: 'Email' }}:** {{ $email }}

**{{ t('contact.email.message_label') ?: 'Message' }}:**

{{ $body }}

{{ t('contact.email.thanks') ?: 'Thanks,' }}
{{ config('app.name', 'BEKKAS') }}
@endcomponent
