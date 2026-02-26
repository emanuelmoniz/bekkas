<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-grey-dark leading-tight">
                {{ $ticket->title }}
            </h2>

            <div class="flex gap-2">
                <a href="{{ route('admin.tickets.index') }}"
                   class="inline-flex items-center px-4 py-2 bg-white border border-grey-medium rounded-full font-semibold text-xs text-grey-dark uppercase tracking-widest shadow-sm hover:bg-grey-light transition ease-in-out duration-150">
                    Back to tickets
                </a>

                <form method="POST"
                      action="{{ route('admin.tickets.mark-unread', $ticket) }}">
                    @csrf
                    <button type="submit"
                            class="bg-primary hover:bg-primary/90 text-white px-8 py-3 rounded-full uppercase text-sm">
                        Mark as unread
                    </button>
                </form>

                <a href="{{ route('admin.tickets.edit', $ticket) }}"
                   class="inline-flex items-center px-4 py-2 bg-primary border border-transparent rounded-full font-semibold text-xs text-white uppercase tracking-widest hover:bg-primary/90 transition ease-in-out duration-150">
                    Admin Edit
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">

        {{-- TICKET META --}}
        <div class="bg-white shadow rounded p-6">
            <dl class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <p class="text-xs text-grey-dark font-medium uppercase tracking-widest">Ticket ID</p>
                    <p class="text-sm text-grey-dark mt-1 font-mono">{{ $ticket->ticket_number ?? $ticket->uuid }}</p>
                </div>

                <div>
                    <p class="text-xs text-grey-dark font-medium uppercase tracking-widest">User</p>
                    <p class="text-sm text-grey-dark mt-1">{{ $ticket->owner?->name ?? '—' }}</p>
                </div>

                <div>
                    <p class="text-xs text-grey-dark font-medium uppercase tracking-widest">Category</p>
                    <p class="text-sm text-grey-dark mt-1">{{ optional($ticket->category?->translation())->name ?? '—' }}</p>
                </div>

                <div>
                    <p class="text-xs text-grey-dark font-medium uppercase tracking-widest">Status</p>
                    <p class="text-sm text-grey-dark mt-1">{{ ucfirst($ticket->status) }}</p>
                </div>

                <div>
                    <p class="text-xs text-grey-dark font-medium uppercase tracking-widest">Opened</p>
                    <p class="text-sm text-grey-dark mt-1">{{ $ticket->opened_at }}</p>
                </div>

                @if ($ticket->due_date)
                    <div>
                        <p class="text-xs text-grey-dark font-medium uppercase tracking-widest">Due Date</p>
                        <p class="text-sm text-grey-dark mt-1">{{ $ticket->due_date->format('Y-m-d') }}</p>
                    </div>
                @endif

                @if ($ticket->closed_at)
                    <div>
                        <p class="text-xs text-grey-dark font-medium uppercase tracking-widest">Closed</p>
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
                    <label class="block font-semibold mb-1">Close reason *</label>
                    <textarea name="reason"
                              class="w-full border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm mb-3"
                              required></textarea>
                    <button class="bg-grey-light hover:bg-grey-light/90 text-grey-dark px-8 py-3 rounded-full uppercase">
                        Close Ticket
                    </button>
                </form>
            @else
                <form method="POST" action="{{ route('tickets.reopen', $ticket) }}">
                    @csrf
                    <label class="block font-semibold mb-1">Reopen reason *</label>
                    <textarea name="reason"
                              class="w-full border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm mb-3"
                              required></textarea>
                    <button class="bg-primary hover:bg-primary/90 text-white px-8 py-3 rounded-full uppercase">
                        Reopen Ticket
                    </button>
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

                <textarea name="message"
                          rows="4"
                          class="w-full border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm"
                          required></textarea>

                <input type="file" name="files[]" multiple>

                <div class="flex justify-end">
                    <button class="bg-primary text-white px-8 py-3 rounded-full uppercase">
                        Send
                    </button>
                </div>
            </form>
        @endif

    </div>
</x-app-layout>
