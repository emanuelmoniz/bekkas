<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            New Tax
        </h2>
    </x-slot>

    <div class="py-6 max-w-xl mx-auto sm:px-6 lg:px-8">
        <form method="POST"
              action="{{ route('admin.taxes.store') }}"
              class="bg-white shadow rounded p-6 space-y-4">
            @csrf

            <div>
                <label class="block text-sm font-medium text-gray-700">
                    Name
                </label>
                <input type="text"
                       name="name"
                       required
                       class="mt-1 block w-full border rounded px-3 py-2">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700">
                    Tax Percentage
                </label>
                <input type="number"
                       step="0.01"
                       name="percentage"
                       required
                       class="mt-1 block w-full border rounded px-3 py-2">
            </div>

            <label class="flex items-center gap-2">
                <input type="checkbox" name="is_active" checked>
                Active
            </label>

            <div class="flex justify-end">
                <button type="submit"
                        class="bg-blue-600 hover:bg-blue-700 text-white px-4 py-2 rounded">
                    Save
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
