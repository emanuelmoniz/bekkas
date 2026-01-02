<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">New Ticket Category</h2>
    </x-slot>

    @include('admin.ticket-categories.form', [
        'action' => route('admin.ticket-categories.store'),
        'method' => 'POST',
        'category' => null,
    ])
</x-app-layout>
