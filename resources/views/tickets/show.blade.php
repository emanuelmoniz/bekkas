<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-grey-dark leading-tight">
                {{ $ticket->title }}
            </h2>

            <div class="flex gap-2">
                <a href="{{ route('tickets.index') }}"
                   class="bg-grey-medium px-4 py-2 rounded text-sm">
                    {{ t('tickets.back_to_tickets') ?: 'Back to tickets' }}
                </a>

                <form method="POST"
                      action="{{ route('tickets.mark-unread', $ticket) }}">
                    @csrf
                    <button type="submit"
                            class="bg-accent-secondary hover:bg-accent-secondary/90 text-light px-4 py-2 rounded text-sm">
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
                <span class="font-mono text-sm">{{ $ticket->ticket_number ?? $ticket->uuid }}</span>
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
                    <button class="bg-grey-light hover:bg-grey-light/90 text-grey-dark px-4 py-2 rounded">
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
                    <button class="bg-accent-primary hover:bg-accent-primary/90 text-light px-4 py-2 rounded">
                        {{ t('tickets.reopen_ticket') ?: 'Reopen Ticket' }}
                    </button>
                </form>
            @endif
        </div>

        {{-- MESSAGES --}}
        <div class="space-y-4">
            @foreach ($ticket->messages as $msg)
                <div class="bg-white p-4 rounded shadow">
                    <div class="text-sm text-grey-dark mb-1">
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
                                       class="text-accent-secondary hover:underline">
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
                        <p class="text-status-error text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                @include('partials.recaptcha-loader')

                <div class="flex justify-end">
                    <button class="bg-accent-primary text-light px-6 py-2 rounded">
                        {{ t('tickets.send') ?: 'Send' }}
                    </button>
                </div>
            </form>
        @endif

    </div>


</x-app-layout>
