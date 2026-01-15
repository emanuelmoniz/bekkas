<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ t('tickets.index_title') ?: 'Tickets' }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">

        {{-- Actions --}}
        <div class="mb-4 flex justify-end">
            <a href="{{ route('tickets.create') }}"
               class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                {{ t('tickets.new') ?: 'New Ticket' }}
            </a>
        </div>

        {{-- Filters --}}
        <form method="GET" class="mb-6 bg-white p-4 rounded shadow">
            <div class="grid grid-cols-1 md:grid-cols-6 gap-4">

                {{-- Ticket ID --}}
                <div>
                    <label class="block text-sm font-medium mb-1">{{ t('tickets.ticket_id') ?: 'Ticket ID' }}</label>
                    <input
                        type="text"
                        name="ticket_id"
                        value="{{ request('ticket_id') }}"
                        class="w-full border rounded px-3 py-2"
                    >
                </div>

                {{-- Ticket Title (NEW) --}}
                <div>
                    <label class="block text-sm font-medium mb-1">{{ t('tickets.title') ?: 'Title' }}</label>
                    <input
                        type="text"
                        name="title"
                        value="{{ request('title') }}"
                        class="w-full border rounded px-3 py-2"
                        placeholder="{{ t('tickets.search_title') ?: 'Search title' }}"
                    >
                </div>

                {{-- Category (dropdown + searchable) --}}
                <div
                    x-data="{ open: false, search: '', selected: '{{ request('category_id') }}' }"
                    class="relative"
                >
                    <label class="block text-sm font-medium mb-1">{{ t('tickets.category') ?: 'Category' }}</label>
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
                            {{ t('tickets.select_category') ?: 'Select category' }}
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
                            placeholder="{{ t('tickets.search') ?: 'Search...' }}"
                            class="w-full px-3 py-2 border-b"
                        >

                        <ul class="max-h-48 overflow-y-auto">
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

                {{-- Actions --}}
                <div class="flex items-end gap-2">
                    <a href="{{ route('tickets.index') }}"
                       class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded">
                        {{ t('tickets.reset') ?: 'Reset' }}
                    </a>
                    <button
                        type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded"
                    >
                        {{ t('tickets.filter') ?: 'Filter' }}
                    </button>
                </div>

            </div>
        </form>

        {{-- Tickets table (UNCHANGED) --}}
        <div class="bg-white shadow rounded">
            <table class="min-w-full border">
                <thead class="bg-gray-100">
                    <tr>
                        <th class="px-4 py-2 text-left">{{ t('tickets.title') ?: 'Title' }}</th>
                        <th class="px-4 py-2 text-left">{{ t('tickets.category') ?: 'Category' }}</th>
                        <th class="px-4 py-2 text-left">{{ t('tickets.status') ?: 'Status' }}</th>
                        <th class="px-4 py-2 text-left">{{ t('tickets.last_update') ?: 'Last Update' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tickets as $ticket)
                        <tr class="border-t">
                            <td class="px-4 py-2 font-semibold">
                                <a href="{{ route('tickets.show', $ticket) }}"
                                   class="{{ $ticket->isUnreadFor(auth()->id())
                                        ? 'text-red-600'
                                        : 'text-gray-800'
                                   }} hover:underline">
                                    {{ $ticket->title }}
                                </a>
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
                            <td colspan="4"
                                class="px-4 py-6 text-center text-gray-500">
                                {{ t('tickets.no_tickets') ?: 'No tickets found.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</x-app-layout>
