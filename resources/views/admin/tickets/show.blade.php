<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-grey-dark leading-tight">
                {{ $ticket->title }}
            </h2>

            <div class="flex gap-2">
                <x-default-button type="button" onclick="window.location.href='{{ route('admin.tickets.index') }}'">
                    Back to tickets
                </x-default-button>

                <form method="POST"
                      action="{{ route('admin.tickets.mark-unread', $ticket) }}">
                    @csrf
                    <x-default-button type="submit">
                        Mark as unread
                    </x-default-button>
                </form>

                <x-default-button type="button" onclick="window.location.href='{{ route('admin.tickets.edit', $ticket) }}'">
                    Admin Edit
                </x-default-button>
            </div>
        </div>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        {{-- TICKET META --}}
        <div class="bg-white shadow rounded p-6">
            <dl class="grid grid-cols-1 lg:grid-cols-2 gap-6">
                <div>
                    <p class="text-xs text-grey-dark uppercase tracking-widest">Ticket ID</p>
                    <p class="text-sm text-grey-dark mt-1 font-mono">{{ $ticket->ticket_number ?? $ticket->uuid }}</p>
                </div>

                <div>
                    <p class="text-xs text-grey-dark uppercase tracking-widest">User</p>
                    <p class="text-sm text-grey-dark mt-1">{{ $ticket->owner?->name ?? '—' }}</p>
                </div>

                <div>
                    <p class="text-xs text-grey-dark uppercase tracking-widest">Category</p>
                    <p class="text-sm text-grey-dark mt-1">{{ optional($ticket->category?->translation())->name ?? '—' }}</p>
                </div>

                <div>
                    <p class="text-xs text-grey-dark uppercase tracking-widest">Status</p>
                    <p class="text-sm text-grey-dark mt-1">{{ ucfirst($ticket->status) }}</p>
                </div>

                <div>
                    <p class="text-xs text-grey-dark uppercase tracking-widest">Opened</p>
                    <p class="text-sm text-grey-dark mt-1">{{ $ticket->opened_at }}</p>
                </div>

                @if ($ticket->due_date)
                    <div>
                        <p class="text-xs text-grey-dark uppercase tracking-widest">Due Date</p>
                        <p class="text-sm text-grey-dark mt-1">{{ $ticket->due_date->format('Y-m-d') }}</p>
                    </div>
                @endif

                @if ($ticket->closed_at)
                    <div>
                        <p class="text-xs text-grey-dark uppercase tracking-widest">Closed</p>
                        <p class="text-sm text-grey-dark mt-1">{{ $ticket->closed_at }}</p>
                    </div>
                @endif
            </dl>
        </div>

        {{-- CLOSE / REOPEN --}}
        <div class="bg-white p-6 rounded shadow">
            @if ($ticket->status === 'open')
                <form method="POST" action="{{ route('tickets.close', $ticket) }}">
                    @csrf
                    <label class="block mb-1">Close reason *</label>
                    <textarea name="reason"
                              class="w-full border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm mb-3"
                              required></textarea>
                    <x-default-button type="submit">
                        Close Ticket
                    </x-default-button>
                </form>
            @else
                <form method="POST" action="{{ route('tickets.reopen', $ticket) }}">
                    @csrf
                    <label class="block mb-1">Reopen reason *</label>
                    <textarea name="reason"
                              class="w-full border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm mb-3"
                              required></textarea>
                    <x-default-button type="submit">
                        Reopen Ticket
                    </x-default-button>
                </form>
            @endif
        </div>

        {{-- MESSAGES --}}
        <div class="space-y-4">
            @foreach ($ticket->messages as $msg)
                <div class="bg-white p-4 rounded shadow">
                    <div class="text-sm text-grey-dark mb-1">
                        {{ $msg->is_system ? 'System' : ($msg->user?->name ?? '—') }}
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
                                       class="text-accent-secondary hover:underline text-accent-primary hover:text-accent-primary/90 no-underline">
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

                <textarea name="message"
                          rows="4"
                          class="w-full border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm"
                          required></textarea>

                <input type="file" name="files[]" multiple>

                <div class="flex justify-end">
                    <x-default-button type="submit">
                        Send
                    </x-default-button>
                </div>
            </form>
        @endif

    </div>
</x-app-layout>
