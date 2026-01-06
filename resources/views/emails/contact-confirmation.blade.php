@component('mail::message')
# {{ t('contact.email.user_heading', ['name' => $name]) ?: ('Thank you, ' . $name) }}

{{ t('contact.email.user_body') ?: 'We received your message and will get back to you shortly.' }}

**{{ t('contact.email.no_reply') ?: 'Please do not reply to this email.' }}**

{{ t('contact.email.user_followup') ?: 'Your request will be answered soon.' }}

{{ t('contact.email.thanks') ?: 'Thanks,' }}
{{ config('app.name', 'BEKKAS') }}
@endcomponent
