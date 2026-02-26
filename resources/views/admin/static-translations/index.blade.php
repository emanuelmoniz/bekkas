<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-grey-dark">Static Translations</h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">

        <div class="mb-4 flex justify-end">
            <a href="{{ route('admin.static-translations.create') }}"
               class="bg-primary hover:bg-primary/90 text-white px-8 py-3 rounded-full uppercase text-sm font-medium">
                + New key
            </a>
        </div>

        <div class="bg-white p-4 rounded shadow">

            {{-- Filter bar --}}
            <div class="mb-4">
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
                    <input
                        name="text"
                        placeholder="Search text…"
                        value="{{ request('text') }}"
                        class="border rounded px-2 py-1 text-sm w-48"
                    >
                    <button class="bg-primary hover:bg-primary/90 text-white px-8 py-3 rounded-full uppercase text-sm">Filter</button>
                    <a href="{{ route('admin.static-translations.index') }}"
                       class="bg-grey-medium hover:bg-grey-dark text-white px-8 py-3 rounded-full uppercase text-sm">Reset</a>
                </form>
            </div>

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="w-full text-left text-sm">
                    <thead class="bg-grey-light">
                        <tr>
                            <th class="px-3 py-2 font-semibold w-1/5">Key</th>
                            <th class="px-3 py-2 font-semibold w-auto">Context</th>
                            <th class="px-3 py-2 font-semibold w-1/3">Text (en-UK)</th>
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
                                <td class="px-3 py-2 font-mono break-all">
                                    <a href="{{ route('admin.static-translations.edit', $encodedKey) }}" class="text-accent-secondary hover:underline">{{ $keyRow->key }}</a>
                                </td>
                                <td class="px-3 py-2">
                                    @if ($keyRow->context)
                                        <span class="inline-block bg-grey-light text-grey-dark text-xs px-2 py-0.5 rounded-full">{{ $keyRow->context }}</span>
                                    @else
                                        <span class="text-grey-dark text-xs">—</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2">
                                    @if ($trans->has('en-UK'))
                                        <span class="text-grey-dark">{{ \Illuminate\Support\Str::limit($trans['en-UK']->value, 70) }}</span>
                                    @else
                                        <span class="text-red-400 text-xs italic">missing</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2 text-right whitespace-nowrap">
                                    <a href="{{ route('admin.static-translations.edit', $encodedKey) }}"
                                       class="inline-flex items-center px-3 py-1 rounded bg-primary text-white text-sm">Edit</a>
                                    <form method="POST"
                                          action="{{ route('admin.static-translations.destroy', $encodedKey) }}"
                                          class="inline-block ms-3"
                                          onsubmit="return confirm('Delete ALL locale rows for key &laquo;{{ addslashes($keyRow->key) }}&raquo;?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="inline-flex items-center px-8 py-3 rounded-full uppercase bg-status-error/10 text-status-error text-sm">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="4" class="px-3 py-6 text-center text-grey-dark">No translations found.</td>
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
