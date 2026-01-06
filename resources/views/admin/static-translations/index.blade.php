<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">Static Translations</h2>
    </x-slot>

    <div class="py-6 max-w-6xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white p-4 rounded shadow">
            <div class="flex justify-between items-center mb-4">
                <form method="GET" class="flex gap-2">
                    <input name="key" placeholder="Key" value="{{ request('key') }}" class="border rounded px-2 py-1">
                    <select name="locale" class="border rounded px-2 py-1">
                        <option value="">All locales</option>
                        @foreach (config('app.locales') as $locale => $label)
                            <option value="{{ $locale }}" @selected(request('locale') === $locale)>{{ $label }} ({{ $locale }})</option>
                        @endforeach
                    </select>
                    <button class="bg-gray-100 px-3 py-1 rounded">Filter</button>
                </form>

                <a href="{{ route('admin.static-translations.create') }}" class="bg-indigo-600 text-white px-4 py-2 rounded">Create</a>
            </div>

            <table class="w-full text-left">
                <thead>
                    <tr>
                        <th class="p-2">Key</th>
                        <th class="p-2">Locale</th>
                        <th class="p-2">Value</th>
                        <th class="p-2">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($items as $item)
                        <tr class="border-t">
                            <td class="p-2">{{ $item->key }}</td>
                            <td class="p-2">{{ $item->locale }}</td>
                            <td class="p-2">{{ Str::limit($item->value, 100) }}</td>
                            <td class="p-2">
                                <a href="{{ route('admin.static-translations.edit', $item) }}" class="text-sm text-blue-600">Edit</a>
                                <form method="POST" action="{{ route('admin.static-translations.destroy', $item) }}" class="inline-block ms-2">
                                    @csrf
                                    @method('DELETE')
                                    <button class="text-sm text-red-600" onclick="return confirm('Delete?')">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>

            <div class="mt-4">
                {{ $items->withQueryString()->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
