<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-grey-dark leading-tight">
            Open Ticket
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">

        <form method="POST"
              action="{{ route('admin.tickets.store') }}"
              enctype="multipart/form-data">
            @csrf

            <div class="bg-white p-6 rounded shadow mb-6 space-y-4">

                <div>
                    <label class="block font-semibold mb-1">User *</label>
                    <select name="user_id" class="w-full border rounded px-3 py-2" required>
                        <option value="">Select a user</option>
                        @foreach ($users as $u)
                            <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-semibold mb-1">Category *</label>
                    <select name="ticket_category_id" class="w-full border rounded px-3 py-2" required>
                        <option value="">Select a category</option>
                        @foreach ($categories as $category)
                            <option value="{{ $category->id }}">
                                {{ optional($category->translation())->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-semibold mb-1">Title *</label>
                    <input type="text"
                           name="title"
                           class="w-full border rounded px-3 py-2"
                           required>
                </div>

                <div>
                    <label class="block font-semibold mb-1">Message *</label>
                    <textarea name="message"
                              rows="5"
                              class="w-full border rounded px-3 py-2"
                              required></textarea>
                </div>

                <div>
                    <label class="block font-semibold mb-1">Due Date</label>
                    <input type="date"
                           name="due_date"
                           class="border rounded px-3 py-2">
                </div>

                <div>
                    <label class="block font-semibold mb-1">Files</label>
                    <input type="file"
                           name="files[]"
                           multiple>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('admin.tickets.index') }}"
                   class="bg-grey-medium px-6 py-2 rounded">
                    Cancel
                </a>
                <button class="bg-accent-primary hover:bg-accent-primary/90 text-light px-6 py-2 rounded">
                    Open Ticket
                </button>
            </div>
        </form>

    </div>
</x-app-layout>
