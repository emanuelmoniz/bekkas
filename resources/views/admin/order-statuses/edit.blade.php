<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-grey-dark">Edit Order Status</h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded p-6">
            <form method="POST" action="{{ route('admin.order-statuses.update', $orderStatus) }}" class="space-y-6">
                @csrf
                @method('PATCH')

                <div>
                    <x-input-label for="code">Code</x-input-label>
                    <x-text-input id="code" name="code" type="text" class="mt-1 block w-full" :value="old('code', $orderStatus->code)" required />
                    <x-input-error :messages="$errors->get('code')" class="mt-2" />
                </div>

                <div>
                    <x-input-label for="sort_order">Sort Order</x-input-label>
                    <x-text-input id="sort_order" name="sort_order" type="number" class="mt-1 block w-full" :value="old('sort_order', $orderStatus->sort_order)" required />
                    <x-input-error :messages="$errors->get('sort_order')" class="mt-2" />
                </div>

                <div class="border-t pt-4">
                    <h3 class="font-semibold mb-4">Translations</h3>
                    @foreach ($locales as $locale => $label)
                        @php
                            $translation = $orderStatus->translations->firstWhere('locale', $locale);
                        @endphp
                        <div class="mb-4">
                            <x-input-label>{{ $label }} ({{ $locale }})</x-input-label>
                            <input type="hidden" name="translations[{{ $loop->index }}][locale]" value="{{ $locale }}">
                            <input name="translations[{{ $loop->index }}][name]"
                                   value="{{ old('translations.'.$loop->index.'.name', $translation?->name) }}"
                                   required
                                   class="mt-1 block w-full border-grey-medium focus:border-accent-primary focus:ring-accent-primary rounded-md shadow-sm">
                            <x-input-error :messages="$errors->get('translations.'.$loop->index.'.name')" class="mt-2" />
                        </div>
                    @endforeach
                </div>

                <div class="flex justify-between">
                    <a href="{{ route('admin.order-statuses.index') }}"
                       class="inline-flex items-center px-4 py-2 bg-white border border-grey-medium rounded-md font-semibold text-xs text-grey-dark uppercase tracking-widest shadow-sm hover:bg-grey-light transition ease-in-out duration-150">
                        Cancel
                    </a>
                    <x-primary-button>Update</x-primary-button>
                </div>
            </form>
        </div>
    </div>
</x-app-layout>
