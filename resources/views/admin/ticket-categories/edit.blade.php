<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl">Edit Ticket Category</h2>
    </x-slot>

    @include('admin.ticket-categories.form', [
        'action' => route('admin.ticket-categories.update', $category),
        'method' => 'PUT',
        'category' => $category,
    ])
</x-app-layout>
