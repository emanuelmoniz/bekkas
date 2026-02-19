<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-grey-dark">
            Edit Tax
        </h2>
    </x-slot>

    <div class="py-6 max-w-xl mx-auto sm:px-6 lg:px-8">
        <form method="POST"
              action="{{ route('admin.taxes.update', $tax) }}"
              class="bg-light shadow rounded p-6 space-y-4">
            @csrf
            @method('PATCH')

            <div>
                <label class="block text-sm font-medium text-grey-dark">
                    Name
                </label>
                <input type="text"
                       name="name"
                       value="{{ $tax->name }}"
                       required
                       class="mt-1 block w-full border rounded px-3 py-2">
            </div>

            <div>
                <label class="block text-sm font-medium text-grey-dark">
                    Tax Percentage
                </label>
                <input type="number"
                       step="0.01"
                       name="percentage"
                       value="{{ $tax->percentage }}"
                       required
                       class="mt-1 block w-full border rounded px-3 py-2">
            </div>

            <label class="flex items-center gap-2">
                <input type="checkbox" name="is_active"
                       @checked($tax->is_active)>
                Active
            </label>

            <div class="flex justify-end">
                <button type="submit"
                        class="bg-accent-primary hover:bg-accent-primary/90 text-light px-4 py-2 rounded">
                    Update
                </button>
            </div>
        </form>
    </div>
</x-app-layout>
