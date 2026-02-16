<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ $ticket->title }}
            </h2>

            <div class="flex gap-2">
                <a href="{{ route('admin.tickets.index') }}"
                   class="bg-gray-300 px-4 py-2 rounded text-sm">
                    Back to tickets
                </a>

                <form method="POST"
                      action="{{ route('admin.tickets.mark-unread', $ticket) }}">
                    @csrf
                    <button type="submit"
                            class="bg-yellow-500 hover:bg-yellow-600 text-white px-4 py-2 rounded text-sm">
                        Mark as unread
                    </button>
                </form>

                <a href="{{ route('admin.tickets.edit', $ticket) }}"
                   class="bg-indigo-600 text-white px-4 py-2 rounded text-sm">
                    Admin Edit
                </a>
            </div>
        </div>
    </x-slot>

    <div class="py-6 max-w-5xl mx-auto space-y-6">

        {{-- TICKET META --}}
        <div class="bg-white p-6 rounded shadow space-y-2">
            <div>
                <strong>Ticket ID:</strong>
                <span class="font-mono text-sm">{{ $ticket->ticket_number ?? $ticket->uuid }}</span>
            </div>

            <div>
                <strong>User:</strong>
                {{ $ticket->owner?->name ?? '—' }}
            </div>

            <div>
                <strong>Category:</strong>
                {{ optional($ticket->category?->translation())->name ?? '—' }}
            </div>

            @if ($ticket->due_date)
                <div>
                    <strong>Due Date:</strong>
                    {{ $ticket->due_date->format('Y-m-d') }}
                </div>
            @endif

            <div>
                <strong>Status:</strong>
                {{ ucfirst($ticket->status) }}
            </div>

            <div>
                <strong>Opened:</strong>
                {{ $ticket->opened_at }}
            </div>

            @if ($ticket->closed_at)
                <div>
                    <strong>Closed:</strong>
                    {{ $ticket->closed_at }}
                </div>
            @endif
        </div>

        {{-- CLOSE / REOPEN --}}
        <div class="bg-white p-6 rounded shadow">
            @if ($ticket->status === 'open')
                <form method="POST" action="{{ route('tickets.close', $ticket) }}">
                    @csrf
                    <label class="block font-semibold mb-1">Close reason *</label>
                    <textarea name="reason"
                              class="w-full border rounded px-3 py-2 mb-3"
                              required></textarea>
                    <button class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded">
                        Close Ticket
                    </button>
                </form>
            @else
                <form method="POST" action="{{ route('tickets.reopen', $ticket) }}">
                    @csrf
                    <label class="block font-semibold mb-1">Reopen reason *</label>
                    <textarea name="reason"
                              class="w-full border rounded px-3 py-2 mb-3"
                              required></textarea>
                    <button class="bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
                        Reopen Ticket
                    </button>
                </form>
            @endif
        </div>

        {{-- MESSAGES --}}
        <div class="space-y-4">
            @foreach ($ticket->messages as $msg)
                <div class="bg-white p-4 rounded shadow">
                    <div class="text-sm text-gray-600 mb-1">
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
                                       class="text-blue-600 hover:underline">
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
                          class="w-full border rounded px-3 py-2"
                          required></textarea>

                <input type="file" name="files[]" multiple>

                <div class="flex justify-end">
                    <button class="bg-blue-600 text-white px-6 py-2 rounded">
                        Send
                    </button>
                </div>
            </form>
        @endif

    </div>
</x-app-layout>
