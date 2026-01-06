@component('mail::message')
# {{ t('tickets.email.greeting', ['name' => $recipientName]) ?: ('Hello ' . $recipientName . ',') }}

{{ t('tickets.email.update_intro') ?: 'There is an update on a ticket you are involved in.' }}

**{{ t('tickets.email.ticket_label') ?: 'Ticket' }}:** {{ $ticket->title }}

**{{ t('tickets.email.status_label') ?: 'Status' }}:** {{ ucfirst($ticket->status) }}

**{{ t('tickets.email.update_type_label') ?: 'Update type' }}:** {{ $eventLabel }}

@if(!$ticketMessage->is_system)
**{{ t('tickets.email.message_label') ?: 'Message' }}:**

{{ $ticketMessage->message }}
@endif

@component('mail::button', ['url' => route('tickets.show', $ticket)])
{{ t('tickets.email.view_button') ?: 'View Ticket' }}
@endcomponent

{{ t('tickets.email.auto_sent') ?: 'This email was sent automatically.' }}

{{ t('tickets.email.thanks') ?: 'Thanks,' }}
{{ config('app.name', 'BEKKAS') }}
@endcomponent
