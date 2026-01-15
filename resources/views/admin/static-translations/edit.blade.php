<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Edit Translation</h2>
    </x-slot>

    <div class="py-6 max-w-4xl mx-auto sm:px-6 lg:px-8">
        <form method="POST" action="{{ route('admin.static-translations.update', $item) }}" class="bg-white p-6 rounded shadow">
            @csrf
            @method('PUT')

            <div class="mb-4">
                <label class="block mb-1">Key</label>
                <input disabled class="w-full border rounded px-3 py-2" value="{{ $item->key }}">
            </div>

            <div class="mb-4">
                <label class="block mb-1">Locale</label>
                <input disabled class="w-full border rounded px-3 py-2" value="{{ $item->locale }}">
            </div>

            <div class="mb-4">
                <label class="block mb-1">Value</label>
                <textarea name="value" class="w-full border rounded px-3 py-2" rows="6">{{ old('value', $item->value) }}</textarea>
            </div>

            <div class="flex justify-end gap-2">
                <a href="{{ route('admin.static-translations.index') }}" class="px-4 py-2 border rounded">Cancel</a>
                <button class="px-4 py-2 bg-indigo-600 text-white rounded">Save</button>
            </div>
        </form>
    </div>
</x-app-layout>
