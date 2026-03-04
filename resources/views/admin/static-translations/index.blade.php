<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-grey-dark">Static Translations</h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">

        <div class="mb-4 flex justify-end">
            <x-default-button type="button" onclick="window.location.href='{{ route('admin.static-translations.create') }}'">
                + New key
            </x-default-button>
        </div>

        {{-- FILTERS --}}
        <form method="GET" class="mb-6 bg-white p-4 rounded shadow">
            <div class="grid grid-cols-1 lg:grid-cols-4 gap-4">
                <input name="search" placeholder="Search key…" value="{{ request('search') }}"
                       class="border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">
                <input name="ctx" placeholder="Context…" value="{{ request('ctx') }}"
                       class="border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">
                <input name="text" placeholder="Search text…" value="{{ request('text') }}"
                       class="border-grey-medium focus:border-accent-primary focus:ring-primary rounded-md shadow-sm">
                <div class="flex justify-end gap-2">
                    <x-default-button type="button" onclick="window.location.href='{{ route('admin.static-translations.index') }}'">Reset</x-default-button>
                    <x-default-button type="submit">Filter</x-default-button>
                </div>
            </div>
        </form>

        <div class="bg-white shadow rounded">

            {{-- Table --}}
            <div class="overflow-x-auto">
                <table class="min-w-full border text-left text-sm">
                    <thead class="bg-grey-light">
                        <tr>
                            <th class="px-3 py-2 w-1/5">Key</th>
                            <th class="px-3 py-2 w-auto">Context</th>
                            <th class="px-3 py-2 w-1/3">Text (en-UK)</th>
                            <th class="px-3 py-2 text-right">Actions</th>
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
                                    <a href="{{ route('admin.static-translations.edit', $encodedKey) }}" class="text-accent-primary hover:text-accent-primary/90 no-underline">{{ $keyRow->key }}</a>
                                </td>
                                <td class="px-3 py-2">
                                    @if ($keyRow->context)
                                        <span class="inline-block bg-grey-light text-grey-dark text-sm px-2 py-0.5 rounded-full">{{ $keyRow->context }}</span>
                                    @else
                                        <span class="text-grey-dark text-xs">—</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2">
                                    @if ($trans->has('en-UK'))
                                        <span class="text-grey-dark">{{ \Illuminate\Support\Str::limit($trans['en-UK']->value, 70) }}</span>
                                    @else
                                        <span class="text-red-400 text-sm italic">missing</span>
                                    @endif
                                </td>
                                <td class="px-3 py-2 text-right whitespace-nowrap">
                                    <x-default-button type="button" onclick="window.location.href='{{ route('admin.static-translations.edit', $encodedKey) }}'">
Edit</x-default-button>
                                    <form method="POST"
                                          action="{{ route('admin.static-translations.destroy', $encodedKey) }}"
                                          class="inline-block ms-3"
                                          onsubmit="return confirm('Delete ALL locale rows for key &laquo;{{ addslashes($keyRow->key) }}&raquo;?')">
                                        @csrf
                                        @method('DELETE')
                                        <x-default-button type="submit">Delete</x-default-button>
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

        </div>

        {{-- Pagination --}}
        <div class="mt-6">
            {{ $keyRows->links() }}
        </div>
    </div>
</x-app-layout>
