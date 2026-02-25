<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-grey-dark">Static Translations</h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">

        @if (session('success'))
            <div class="mb-4 px-4 py-2 bg-green-100 text-green-800 rounded">{{ session('success') }}</div>
        @endif

        <div class="bg-light p-4 rounded shadow">

            {{-- Toolbar --}}
            <div class="flex flex-wrap justify-between items-center gap-3 mb-4">
                <form method="GET" class="flex flex-wrap gap-2">
                    <input
                        name="search"
                        placeholder="Search key…"
                        value="{{ request('search') }}"
                        class="border rounded px-2 py-1 text-sm w-56"
                    >
                    <input
                        name="ctx"
                        placeholder="Context…"
                        value="{{ request('ctx') }}"
                        class="border rounded px-2 py-1 text-sm w-40"
                    >
                    <button class="bg-accent-primary hover:bg-accent-primary/90 text-light px-4 py-1 rounded text-sm">Filter</button>
                    <a href="{{ route('admin.static-translations.index') }}"
                       class="bg-grey-medium hover:bg-grey-dark text-light px-4 py-1 rounded text-sm">Reset</a>
                </form>

                <a href="{{ route('admin.static-translations.create') }}"
                   class="bg-accent-primary hover:bg-accent-primary/90 text-light px-4 py-2 rounded text-sm font-medium">
                    + New key
                </a>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-grey-light">
                        <tr>
                            <th class="px-3 py-2 font-semibold w-1/4">Key</th>
                            <th class="px-3 py-2 font-semibold w-24">Context</th>
                            @foreach ($locales as $locale => $label)
                                <th class="px-3 py-2 font-semibold">{{ $label }}<span class="text-grey-dark font-normal text-xs ml-1">({{ $locale }})</span></th>
                            @endforeach
                            <th class="px-3 py-2 font-semibold text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse ($keyRows as $keyRow)
                            @php
                                $encodedKey = \App\Http\Controllers\Admin\StaticTranslationController::encodeKey($keyRow->key);
                                $trans      = $allTranslations[$keyRow->key] ?? collect();
                            @endphp
                            <tr class="border-t hover:bg-grey-light/30">
                                <td class="px-3 py-2 font-mono break-all">{{ $keyRow->key }}</td>
                                <td class="px-3 py-2">
                                    @if ($keyRow->context)
                                        <span class="inline-block bg-grey-light text-grey-dark text-xs px-2 py-0.5 rounded-full">{{ $keyRow->context }}</span>
                                    @else
                                        <span class="text-grey-dark text-xs">—</span>
                                    @endif
                                </td>
                                @foreach ($locales as $locale => $label)
                                    <td class="px-3 py-2 text-grey-dark">
                                        @if ($trans->has($locale))
                                            {{ \Illuminate\Support\Str::limit($trans[$locale]->value, 60) }}
                                        @else
                                            <span class="text-red-400 text-xs">missing</span>
                                        @endif
                                    </td>
                                @endforeach
                                <td class="px-3 py-2 text-right whitespace-nowrap">
                                    <a href="{{ route('admin.static-translations.edit', $encodedKey) }}"
                                       class="text-accent-secondary text-sm hover:underline">Edit</a>
                                    <form method="POST"
                                          action="{{ route('admin.static-translations.destroy', $encodedKey) }}"
                                          class="inline-block ms-3"
                                          onsubmit="return confirm('Delete ALL locale rows for key &laquo;{{ addslashes($keyRow->key) }}&raquo;?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="text-red-500 text-sm hover:underline">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="{{ 3 + count($locales) }}" class="px-3 py-6 text-center text-grey-dark">No translations found.</td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            {{-- Pagination --}}
            <div class="mt-4">
                {{ $keyRows->links() }}
            </div>
        </div>
    </div>
</x-app-layout>
