<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-grey-dark">Create Order Status</h2>
    </x-slot>

    <div class="py-6 max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-light shadow rounded p-6">
            <form method="POST" action="{{ route('admin.order-statuses.store') }}" class="space-y-6">
                @csrf

                <div>
                    <label class="block font-medium mb-1">Code</label>
                    <input name="code" value="{{ old('code') }}" required
                           class="border rounded px-3 py-2 w-full">
                    @error('code')
                        <p class="text-status-error text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div>
                    <label class="block font-medium mb-1">Sort Order</label>
                    <input type="number" name="sort_order" value="{{ old('sort_order', 0) }}" required
                           class="border rounded px-3 py-2 w-full">
                    @error('sort_order')
                        <p class="text-status-error text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                <div class="border-t pt-4">
                    <h3 class="font-semibold mb-4">Translations</h3>
                    @foreach ($locales as $locale => $label)
                        <div class="mb-4">
                            <label class="block font-medium mb-1">{{ $label }} ({{ $locale }})</label>
                            <input type="hidden" name="translations[{{ $loop->index }}][locale]" value="{{ $locale }}">
                            <input name="translations[{{ $loop->index }}][name]"
                                   value="{{ old('translations.'.$loop->index.'.name') }}"
                                   required
                                   class="border rounded px-3 py-2 w-full">
                            @error('translations.'.$loop->index.'.name')
                                <p class="text-status-error text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>
                    @endforeach
                </div>

                <div class="flex gap-2">
                    <button type="submit" class="bg-accent-primary hover:bg-accent-primary/90 text-light px-6 py-2 rounded">
                        Create
                    </button>
                    <a href="{{ route('admin.order-statuses.index') }}"
                       class="bg-grey-medium hover:bg-grey-dark text-light px-6 py-2 rounded">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
