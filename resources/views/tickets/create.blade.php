<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            Open Ticket
        </h2>
    </x-slot>

    <div class="py-6 max-w-4xl mx-auto">

        <form method="POST"
              action="{{ route('tickets.store') }}"
              enctype="multipart/form-data">
            @csrf

            <div class="bg-white p-6 rounded shadow mb-6 space-y-4">

                <div>
                    <label class="block font-semibold mb-1">Category *</label>
                    <select name="ticket_category_id" class="w-full border rounded px-3 py-2" required>
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

                <!-- Google reCAPTCHA -->
                <div>
                    <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
                    @error('g-recaptcha-response')
                        <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>
            </div>

            <div class="flex justify-end gap-3">
                <a href="{{ route('tickets.index') }}"
                   class="bg-gray-300 px-6 py-2 rounded">
                    Cancel
                </a>
                <button class="bg-green-600 hover:bg-green-700 text-white px-6 py-2 rounded">
                    Open Ticket
                </button>
            </div>
        </form>

    </div>
</x-app-layout>
