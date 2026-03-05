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
                    <label class="block mb-1">User *</label>
                    <select name="user_id" class="w-full border rounded px-3 py-2" required>
                        <option value="">Select a user</option>
                        @foreach ($users as $u)
                            <option value="{{ $u->id }}">{{ $u->name }} ({{ $u->email }})</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block mb-1">Category *</label>
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
                    <label class="block mb-1">Title *</label>
                    <input type="text"
                           name="title"
                           class="w-full border rounded px-3 py-2"
                           required>
                </div>

                <div>
                    <label class="block mb-1">Message *</label>
                    <textarea name="message"
                              rows="5"
                              class="w-full border rounded px-3 py-2"
                              required></textarea>
                </div>

                <div>
                    <label class="block mb-1">Due Date</label>
                    <input type="date"
                           name="due_date"
                           class="border rounded px-3 py-2">
                </div>

                <div>
                    <label class="block mb-1">Files</label>
                    <input type="file"
                           name="files[]"
                           multiple>
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <x-default-button type="button" onclick="window.location.href='{{ route('admin.tickets.index') }}'">
                    Cancel
                </x-default-button>
                <x-default-button type="submit">
                    Open Ticket
                </x-default-button>
            </div>
        </form>

    </div>
</x-app-layout>
