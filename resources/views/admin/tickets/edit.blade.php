<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">
            Admin – Edit Ticket
        </h2>
    </x-slot>

    <div class="py-6 max-w-xl mx-auto">
        <form method="POST"
              action="{{ route('admin.tickets.update', $ticket) }}"
              class="bg-white p-6 rounded shadow space-y-4">
            @csrf
            @method('PATCH')

            {{-- OWNER --}}
            @if ($canChangeOwner)
                <div>
                    <label class="block font-semibold mb-1">User *</label>
                    <select name="user_id"
                            class="w-full border rounded px-3 py-2">
                        @foreach ($users as $user)
                            <option value="{{ $user->id }}"
                                @selected($ticket->user_id === $user->id)>
                                {{ $user->name }} ({{ $user->email }})
                            </option>
                        @endforeach
                    </select>
                </div>
            @else
                <div>
                    <label class="block font-semibold mb-1">User</label>
                    <input type="text"
                           class="w-full border rounded px-3 py-2 bg-grey-light"
                           value="{{ $ticket->owner?->name }}"
                           disabled>
                </div>
            @endif

            {{-- CATEGORY --}}
            <div>
                <label class="block font-semibold mb-1">Category *</label>
                <select name="ticket_category_id"
                        class="w-full border rounded px-3 py-2">
                    @foreach ($categories as $cat)
                        <option value="{{ $cat->id }}"
                            @selected($ticket->ticket_category_id === $cat->id)>
                            {{ optional($cat->translation())->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- DUE DATE --}}
            <div>
                <label class="block font-semibold mb-1">Due Date</label>
                <input type="date"
                       name="due_date"
                       value="{{ optional($ticket->due_date)->format('Y-m-d') }}"
                       class="w-full border rounded px-3 py-2">
            </div>

            <div class="flex justify-between">
                <a href="{{ route('admin.tickets.index') }}"
                   class="bg-grey-medium px-4 py-2 rounded">
                    Cancel
                </a>

                <button class="bg-accent-primary text-light px-6 py-2 rounded">
                    Save Changes
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
