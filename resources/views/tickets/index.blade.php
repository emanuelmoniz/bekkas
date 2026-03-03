<x-app-layout>

    <div class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Actions --}}
        <div class="mb-4 flex justify-end">
            <a href="{{ route('tickets.create') }}"
               class="bg-primary hover:bg-primary/90 text-white px-8 py-3 rounded-full uppercase">
                {{ t('tickets.new') ?: 'New Ticket' }}
            </a>
        </div>

        {{-- Filters --}}
        <form method="GET" class="mb-6 lg:bg-white lg:p-4 lg:rounded lg:shadow" x-data="{ open: false }">

            {{-- Mobile toggle button --}}
            <button type="button" @click="open = !open"
                class="lg:hidden w-full flex items-center justify-between bg-white border border-grey-light rounded-full uppercase px-8 py-3 mb-2 font-semibold">
                <span>{{ t('tickets.filters') ?: 'Filters' }}</span>
                <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5 transition-transform" :class="open ? 'rotate-180' : ''" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                </svg>
            </button>

            {{-- Filter panel: collapsed on mobile, always visible on desktop --}}
            <div x-show="open" x-cloak
                 class="bg-white border border-grey-light rounded p-4 lg:bg-transparent lg:border-0 lg:rounded-none lg:p-0 lg:!flex lg:flex-wrap lg:items-center lg:gap-3 lg:mt-0">

                <div class="flex flex-col lg:flex-row lg:flex-wrap lg:items-center lg:flex-1 gap-2 lg:gap-3">
                    {{-- Ticket ID --}}
                    <input
                        type="text"
                        name="ticket_id"
                        value="{{ request('ticket_id') }}"
                        placeholder="{{ t('tickets.ticket_id') ?: 'Ticket ID' }}"
                        class="w-full lg:w-36 border rounded px-3 py-2"
                    >

                    {{-- Title --}}
                    <input
                        type="text"
                        name="title"
                        value="{{ request('title') }}"
                        placeholder="{{ t('tickets.search_title') ?: 'Search title' }}"
                        class="w-full lg:flex-1 lg:min-w-40 border rounded px-3 py-2"
                    >

                    {{-- Category --}}
                    <select name="category_id" class="w-full lg:w-auto border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">
                        <option value="">{{ t('tickets.category') ?: 'Category' }}</option>
                        @foreach($categories as $category)
                            @php $name = optional($category->translation())->name; @endphp
                            <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>

                {{-- Actions --}}
                <div class="flex flex-col lg:flex-row lg:items-center lg:shrink-0 gap-2 mt-2 lg:mt-0">
                    <a href="{{ route('tickets.index') }}"
                       class="w-full lg:w-auto text-center bg-grey-medium hover:bg-grey-dark text-white px-8 py-3 rounded-full uppercase">
                        {{ t('tickets.reset') ?: 'Reset' }}
                    </a>
                    <button
                        type="submit"
                        class="w-full lg:w-auto bg-primary hover:bg-primary/90 text-white px-8 py-3 rounded-full uppercase"
                    >
                        {{ t('tickets.filter') ?: 'Filter' }}
                    </button>
                </div>

            </div>
        </form>

        {{-- Tickets table --}}
        <div class="bg-white shadow rounded">

            {{-- Desktop table (md+) --}}
            <table class="hidden lg:table w-full border">
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
                            <td class="px-4 py-2 font-bold">
                                <a href="{{ route('tickets.show', $ticket) }}" class="text-accent-primary hover:underline">
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
                            <td colspan="5" class="px-4 py-6 text-center text-grey-medium">
                                {{ t('tickets.no_tickets') ?: 'No tickets found.' }}
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            {{-- Mobile cards (< md) --}}
            <div class="lg:hidden divide-y divide-grey-light">
                @forelse ($tickets as $ticket)
                    <a href="{{ route('tickets.show', $ticket) }}"
                       class="block px-4 py-3 hover:bg-grey-light/40 transition-colors">

                        {{-- Ticket ID + status badge --}}
                        <div class="flex items-center justify-between gap-2">
                            <span class="font-bold text-accent-primary">
                                {{ $ticket->ticket_number ?? $ticket->uuid }}
                            </span>
                            <span class="text-xs px-2 py-0.5 rounded-full bg-grey-light text-grey-dark shrink-0">
                                {{ ucfirst($ticket->status) }}
                            </span>
                        </div>

                        {{-- Title --}}
                        <p class="mt-1 font-semibold {{ $ticket->isUnreadFor(auth()->id()) ? 'text-status-error' : 'text-grey-dark' }}">
                            {{ $ticket->title }}
                        </p>

                        {{-- Category + date --}}
                        <div class="mt-1 flex flex-wrap gap-x-3 gap-y-0.5 text-xs text-grey-medium">
                            <span>{{ optional($ticket->category?->translation())->name ?? '—' }}</span>
                            <span>{{ $ticket->last_message_at?->format('Y-m-d H:i') ?? '—' }}</span>
                        </div>

                    </a>
                @empty
                    <p class="px-4 py-6 text-center text-grey-medium">
                        {{ t('tickets.no_tickets') ?: 'No tickets found.' }}
                    </p>
                @endforelse
            </div>

        </div>

    </div>
</x-app-layout>
