<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Admin – Tickets</h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto">
        <div class="mb-4 flex justify-end">
            <a href="{{ route('admin.tickets.create') }}"
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                New Ticket
            </a>
        </div>

        <div class="bg-white p-6 rounded shadow mb-4">
            <form method="GET" class="grid grid-cols-1 md:grid-cols-5 gap-3">
                <input name="ticket_id" placeholder="Ticket ID" class="border rounded px-3 py-2" value="{{ request('ticket_id') }}">
                <input name="title" placeholder="Title" class="border rounded px-3 py-2" value="{{ request('title') }}">
                <input name="user" placeholder="User" class="border rounded px-3 py-2" value="{{ request('user') }}">
                <input name="email" placeholder="Email" class="border rounded px-3 py-2" value="{{ request('email') }}">

                {{-- Category (dropdown + searchable) --}}
                <div
                    x-data="{ open: false, search: '', selected: '{{ request('category_id') }}' }"
                    class="relative"
                >
                    <input type="hidden" name="category_id" :value="selected">

                    <button
                        type="button"
                        @click="open = !open"
                        class="w-full border rounded px-3 py-2 text-left bg-white"
                    >
                        @if(request('category_id'))
                            {{
                                optional(
                                    $categories->firstWhere('id', request('category_id'))
                                        ?->translation()
                                )->name ?? '—'
                            }}
                        @else
                            All categories
                        @endif
                    </button>

                    <div
                        x-show="open"
                        @click.outside="open = false"
                        class="absolute z-10 mt-1 w-full bg-white border rounded shadow"
                    >
                        <input
                            type="text"
                            x-model="search"
                            placeholder="Search..."
                            class="w-full px-3 py-2 border-b"
                        >

                        <ul class="max-h-48 overflow-y-auto">
                            <li
                                @click="selected=''; open=false"
                                class="px-3 py-2 hover:bg-gray-100 cursor-pointer"
                            >
                                All categories
                            </li>
                            @foreach($categories as $category)
                                @php
                                    $name = optional($category->translation())->name;
                                @endphp
                                <li
                                    x-show="'{{ strtolower($name) }}'.includes(search.toLowerCase())"
                                    @click="selected='{{ $category->id }}'; open=false"
                                    class="px-3 py-2 hover:bg-gray-100 cursor-pointer"
                                >
                                    {{ $name }}
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>

                <div class="md:col-span-5 text-right flex justify-end gap-2">
                    <a href="{{ route('admin.tickets.index') }}" 
                       class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                        Reset
                    </a>
                    <button class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">Filter</button>
                </div>
            </form>
        </div>

        <div class="bg-white shadow rounded">
            <table class="min-w-full border">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left">Ticket ID</th>
                        <th class="px-4 py-2 text-left">Title</th>
                        <th class="px-4 py-2 text-left">User</th>
                        <th class="px-4 py-2 text-left">Category</th>
                        <th class="px-4 py-2 text-left">Status</th>
                        <th class="px-4 py-2 text-left">Updated</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tickets as $ticket)
                        <tr class="border-t">
                            <td class="px-4 py-2 font-mono text-sm">{{ $ticket->ticket_number ?? $ticket->uuid }}</td>

                            <td class="px-4 py-2 font-semibold">
                                <a href="{{ route('admin.tickets.show', $ticket) }}"
                                   class="{{ $ticket->isUnreadFor(auth()->id())
                                        ? 'text-red-600'
                                        : 'text-gray-800'
                                   }} hover:underline">
                                    {{ $ticket->title }}
                                </a>
                            </td>
                            <td class="px-4 py-2">
                                {{ $ticket->owner?->name ?? '—' }}
                            </td>
                            <td class="px-4 py-2">
                                {{ optional($ticket->category?->translation())->name ?? '—' }}
                            </td>
                            <td class="px-4 py-2">
                                {{ ucfirst($ticket->status) }}
                            </td>
                            <td class="px-4 py-2 text-sm text-gray-600">
                                {{ $ticket->last_message_at?->format('Y-m-d H:i') ?? '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6"
                                class="px-4 py-6 text-center text-gray-500">
                                No tickets found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
