<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Tickets</h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="mb-4 flex justify-end">
            <button type="submit" type="button" onclick="window.location.href='{{ route('admin.tickets.create') }}'"
        class="bg-primary hover:bg-primary/90 text-white px-2 py-2 rounded uppercase text-sm">
                New Ticket
            </button>
        </div>

        <form method="GET" class="mb-6 bg-white p-4 rounded shadow">
            <div class="grid grid-cols-1 lg:grid-cols-6 gap-4">
                <input name="ticket_id" placeholder="Ticket ID" value="{{ request('ticket_id') }}"
                       class="border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">
                <input name="title" placeholder="Title" value="{{ request('title') }}"
                       class="border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">
                <input name="user" placeholder="User" value="{{ request('user') }}"
                       class="border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">
                <input name="email" placeholder="Email" value="{{ request('email') }}"
                       class="border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">

                <select name="category_id"
                        class="border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">
                    <option value="">All categories</option>
                    @foreach($categories as $category)
                        <option value="{{ $category->id }}" @selected(request('category_id') == $category->id)>
                            {{ optional($category->translation())->name ?? '—' }}
                        </option>
                    @endforeach
                </select>
                <div class="flex justify-end gap-2">
                    <button type="button"
                    onclick="window.location.href='{{ route('admin.tickets.index') }}'"
                    class="bg-grey-light hover:bg-grey-medium text-grey-dark px-2 py-2 rounded uppercase text-sm">
                        Reset
                    </button>
                    <button type="submit" class="bg-primary hover:bg-primary/90 text-white px-2 py-2 rounded uppercase text-sm">
                        Filter
                    </button>
                </div>
            </div>
        </form>

        <div class="bg-white shadow rounded">
            <table class="min-w-full border">
                <thead class="bg-grey-light">
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
                            <td class="px-4 py-2 font-mono text-sm">
                                <a href="{{ route('admin.tickets.show', $ticket) }}" class="text-accent-secondary hover:underline font-medium">{{ $ticket->ticket_number ?? $ticket->uuid }}</a>
                            </td>

                            <td class="px-4 py-2 font-semibold">
                                <a href="{{ route('admin.tickets.show', $ticket) }}"
                                   class="{{ $ticket->isUnreadFor(auth()->id())
                                        ? 'text-status-error'
                                        : 'text-grey-dark'
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
                            <td class="px-4 py-2 text-sm text-grey-dark">
                                {{ $ticket->last_message_at?->format('Y-m-d H:i') ?? '—' }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6"
                                class="px-4 py-6 text-center text-grey-medium">
                                No tickets found.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>
</x-app-layout>
