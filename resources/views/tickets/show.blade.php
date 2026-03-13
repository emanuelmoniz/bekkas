<x-app-layout>

    <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 space-y-4">

        {{-- Actions --}}
        <div class="mb-4 flex gap-2 justify-end">
            <x-optional-cta as="a" :href="route('tickets.index')">
                {{ t('tickets.back_to_tickets') ?: 'Back to tickets' }}
            </x-optional-cta>

            <form method="POST"
                    action="{{ route('tickets.mark-unread', $ticket) }}">
                @csrf
                <x-primary-cta type="submit">
                    {{ t('tickets.mark_as_unread') ?: 'Mark as unread' }}
                </x-primary-cta>
            </form>
        </div>


        {{-- TICKET META --}}
        <div class="bg-white p-6 rounded shadow space-y-2">

            <div>
                <strong>{{ t('tickets.title') ?: 'Title:' }}:</strong>
                <span class="text-sm">{{ $ticket->title }}</span>
            </div>

            <div>
                <strong>{{ t('tickets.ticket_id') ?: 'Ticket ID' }}:</strong>
                <span class="text-sm">{{ $ticket->ticket_number ?? $ticket->uuid }}</span>
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

         {{-- MESSAGES --}}
        <div class="bg-white p-6 rounded shadow">
            <div class="space-y-4">
                @foreach ($ticket->messages as $msg)
                    @php
                        $isClientMessage = ! $msg->is_system && $msg->user_id === $ticket->user_id;
                    @endphp

                    <div class="flex {{ $isClientMessage ? 'justify-end' : 'justify-start' }}">
                        <div class="max-w-[66%] w-full bg-light {{ $isClientMessage ? 'text-right' : 'text-left' }}">
                            <div class="rounded-lg p-4 shadow text-dark-grey {{ $isClientMessage ? 'bg-accent-primary/10 border border-accent-primary' : 'bg-accent-secondary/10 border border-accent-secondary' }}">
                                <div class="text-sm mb-1 text-dark-grey">
                                    {{ $msg->is_system ? (t('tickets.system') ?: 'System') : ($msg->user?->name ?? '—') }}
                                    · {{ $msg->created_at }}
                                </div>

                                @if ($msg->is_system && $msg->system_event)
                                    <div class="whitespace-pre-line">
                                        @if ($msg->system_event === 'closed')
                                            <span class="font-semibold">{{ t('tickets.closed_by') ?: 'Closed by' }}:</span> {{ $msg->user?->name ?? '—' }}<br>
                                        @elseif ($msg->system_event === 'reopened')
                                            <span class="font-semibold">{{ t('tickets.reopened_by') ?: 'Reopened by' }}:</span> {{ $msg->user?->name ?? '—' }}<br>
                                        @endif
                                        <span class="font-semibold">{{ t('tickets.reason') ?: 'Reason' }}:</span> {{ $msg->message }}
                                    </div>
                                @else
                                    <div class="whitespace-pre-line">
                                        {{ $msg->message }}
                                    </div>
                                @endif

                                @if ($msg->attachments->count())
                                    <ul class="list-disc ml-5 text-sm mt-2">
                                        @foreach ($msg->attachments as $file)
                                                <a href="{{ route('tickets.attachments.download', $file) }}"
                                                class="text-accent-primary hover:text-accent-primary/90 no-underline">
                                                    {{ $file->original_name }}
                                                </a>
                                        @endforeach
                                    </ul>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            </div>
        </div>            

        {{-- REPLY --}}
        @if ($ticket->status === 'open')
            <form method="POST"
                  action="{{ route('tickets.messages.store', $ticket) }}"
                  enctype="multipart/form-data"
                  class="bg-white p-6 rounded shadow"
                  novalidate>
                @csrf

                <h3 class="text-lg font-semibold mb-3">{{ t('tickets.reply_to_ticket') ?: 'Reply to ticket' }}</h3>

                <div >
                    <label class="block mb-1">{{ t('tickets.new_message') ?: 'New message' }} *</label>
                    <textarea name="message"
                                rows="4"
                                class="w-full border rounded px-3 py-2 mb-3"></textarea>
                </div>

                <input class="mb-3" type="file" name="files[]" multiple>

                <!-- Google reCAPTCHA (render only when both site and secret keys configured) -->
                @if (! empty(config('services.recaptcha.site_key')) && ! empty(config('services.recaptcha.secret_key')))
                    <div class="mb-3">
                        <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
                        @error('g-recaptcha-response')
                            <p class="text-status-error text-sm mt-1">{{ $message }}</p>
                        @enderror
                    </div>

                    @include('partials.recaptcha-loader')
                @endif

                <x-primary-cta>
                    {{ t('tickets.send') ?: 'Send' }}
                </x-primary-cta>
            </form>
        @endif

        {{-- CLOSE / REOPEN --}}
        <div class="bg-white p-6 rounded shadow">
            @if ($ticket->status === 'open')
                <form method="POST" action="{{ route('tickets.close', $ticket) }}" novalidate>
                    <h3 class="text-lg font-semibold mb-3">{{ t('tickets.close_ticket') ?: 'Close ticket' }}</h3>
                    @csrf
                    <label class="block mb-1">{{ t('tickets.close_reason') ?: 'Close reason' }} *</label>
                    <textarea name="reason"
                              class="w-full border rounded px-3 py-2 mb-3"></textarea>
                    <x-optional-cta>
                        {{ t('tickets.close_ticket') ?: 'Close Ticket' }}
                    </x-optional-cta>
                </form>
            @else
                <form method="POST" action="{{ route('tickets.reopen', $ticket) }}" novalidate>
                    <h3 class="text-lg font-semibold mb-3">{{ t('tickets.reopen_ticket') ?: 'Reopen ticket' }}</h3>
                     @csrf
                    @csrf
                    <label class="block mb-1">{{ t('tickets.reopen_reason') ?: 'Reopen reason' }} *</label>
                    <textarea name="reason"
                              class="w-full border rounded px-3 py-2 mb-3"></textarea>
                    <x-primary-cta>
                        {{ t('tickets.reopen_ticket') ?: 'Reopen Ticket' }}
                    </x-primary-cta>
                </form>
            @endif
        </div>

    </div>


</x-app-layout>
