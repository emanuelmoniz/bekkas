<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-grey-dark leading-tight">
            {{ t('tickets.index_title') ?: 'Tickets' }}
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Actions --}}
        <div class="mb-4 flex justify-end">
            <a href="{{ route('tickets.create') }}"
               class="bg-primary hover:bg-primary/90 text-white px-8 py-3 rounded-full uppercase">
                {{ t('tickets.new') ?: 'New Ticket' }}
            </a>
        </div>

        {{-- Filters --}}
        <form method="GET" class="mb-6 bg-white p-4 rounded shadow">
            <div class="flex flex-wrap items-center gap-3">

                {{-- Ticket ID --}}
                <input
                    type="text"
                    name="ticket_id"
                    value="{{ request('ticket_id') }}"
                    placeholder="{{ t('tickets.ticket_id') ?: 'Ticket ID' }}"
                    class="border rounded px-3 py-2 w-36"
                >

                {{-- Title --}}
                <input
                    type="text"
                    name="title"
                    value="{{ request('title') }}"
                    placeholder="{{ t('tickets.search_title') ?: 'Search title' }}"
                    class="border rounded px-3 py-2 flex-1 min-w-40"
                >

                {{-- Category --}}
                <select name="category_id" class="border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">
                    <option value="">{{ t('tickets.category') ?: 'Category' }}</option>
                    @foreach($categories as $category)
                        @php $name = optional($category->translation())->name; @endphp
                        <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>{{ $name }}</option>
                    @endforeach
                </select>

                {{-- Actions --}}
                <div class="ml-auto flex items-center gap-2">
                    <a href="{{ route('tickets.index') }}"
                       class="bg-grey-medium hover:bg-grey-dark text-white px-8 py-3 rounded-full uppercase">
                        {{ t('tickets.reset') ?: 'Reset' }}
                    </a>
                    <button
                        type="submit"
                        class="bg-primary hover:bg-primary/90 text-white px-8 py-3 rounded-full uppercase"
                    >
                        {{ t('tickets.filter') ?: 'Filter' }}
                    </button>
                </div>

            </div>
        </form>

        {{-- Tickets table (UNCHANGED) --}}
        <div class="bg-white shadow rounded">
            <table class="min-w-full border">
                <thead class="bg-grey-light">
                    <tr>
                        <th class="px-4 py-2 text-left">{{ t('tickets.ticket_id') ?: 'Ticket ID' }}</th>
                        <th class="px-4 py-2 text-left">{{ t('tickets.title') ?: 'Title' }}</th>
                        <th class="px-4 py-2 text-left">{{ t('tickets.category') ?: 'Category' }}</th>
                        <th class="px-4 py-2 text-left">{{ t('tickets.status') ?: 'Status' }}</th>
                        <th class="px-4 py-2 text-left">{{ t('tickets.last_update') ?: 'Last Update' }}</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($tickets as $ticket)
                        <tr class="border-t">
                            <td class="px-4 py-2 font-mono text-sm">
                                <a href="{{ route('tickets.show', $ticket) }}" class="text-accent-secondary hover:underline">
                                    {{ $ticket->ticket_number ?? $ticket->uuid }}
                                </a>
                            </td>

                            <td class="px-4 py-2 font-semibold">
                                <a href="{{ route('tickets.show', $ticket) }}"
                                   class="{{ $ticket->isUnreadFor(auth()->id())
                                        ? 'text-status-error'
                                        : 'text-grey-dark'
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

                            <td class="px-4 py-2 text-sm text-grey-dark">
                                {{ $ticket->last_message_at?->format('Y-m-d H:i') ?? '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="5"
                                class="px-4 py-6 text-center text-grey-medium">
                                {{ t('tickets.no_tickets') ?: 'No tickets found.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    </div>
</x-app-layout>
