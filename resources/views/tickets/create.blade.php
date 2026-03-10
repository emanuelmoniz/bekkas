<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-grey-dark leading-tight">
            {{ t('tickets.open_ticket') ?: 'Open Ticket' }}
        </h2>
    </x-slot>

    @php
        $descriptions = $categories->mapWithKeys(fn ($cat) => [
            $cat->id => optional($cat->translation())->description,
        ])->toJson();
        $preselectedId = optional($preselectedCategory)->id;
    @endphp

    <div
        class="py-6 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8"
        x-data="{
            selectedCategory: {{ $preselectedId ?? 'null' }},
            descriptions: {{ $descriptions }},
            get description() {
                return this.selectedCategory ? (this.descriptions[this.selectedCategory] ?? null) : null;
            }
        }"
    >

        <form method="POST"
              action="{{ route('tickets.store') }}"
              enctype="multipart/form-data">
            @csrf

            <div class="bg-white p-6 rounded shadow mb-6 space-y-4">

                {{-- Category --}}
                <div>
                    <label class="block font-semibold mb-1">{{ t('tickets.category') ?: 'Category' }} *</label>
                    <select
                        name="ticket_category_id"
                        class="w-full border rounded px-3 py-2"
                        x-model="selectedCategory"
                        required
                    >
                        <option value="">— {{ t('tickets.select_category') ?: 'Select a category' }} —</option>
                        @foreach ($categories as $category)
                            <option
                                value="{{ $category->id }}"
                                @selected(old('ticket_category_id', $preselectedId) == $category->id)
                            >
                                {{ optional($category->translation())->name }}
                            </option>
                        @endforeach
                    </select>

                    {{-- Category description --}}
                    <div
                        x-show="description"
                        x-transition
                        class="mt-3 p-4 bg-status-info/10 border border-status-info rounded-lg text-sm text-status-info whitespace-pre-line"
                        x-text="description"
                    ></div>
                </div>

                {{-- Title --}}
                <div>
                    <label class="block font-semibold mb-1">{{ t('tickets.title') ?: 'Title' }} *</label>
                    <input type="text"
                           name="title"
                           value="{{ old('title') }}"
                           class="w-full border rounded px-3 py-2"
                           required>
                </div>

                {{-- Message --}}
                <div>
                    <label class="block font-semibold mb-1">{{ t('tickets.message') ?: 'Message' }} *</label>
                    <textarea name="message"
                              rows="5"
                              class="w-full border rounded px-3 py-2"
                              required>{{ old('message') }}</textarea>
                </div>

                {{-- Due Date --}}
                <div>
                    <label class="block font-semibold mb-1">{{ t('tickets.due_date') ?: 'Due Date' }}</label>
                    <input type="date"
                           name="due_date"
                           value="{{ old('due_date') }}"
                           class="border rounded px-3 py-2">
                </div>

                {{-- Files --}}
                <div>
                    <label class="block font-semibold mb-1">{{ t('tickets.files') ?: 'Files' }}</label>
                    <input type="file"
                           name="files[]"
                           multiple>
                </div>

                {{-- Google reCAPTCHA --}}
                <div>
                    <div class="g-recaptcha" data-sitekey="{{ config('services.recaptcha.site_key') }}"></div>
                    @error('g-recaptcha-response')
                        <p class="text-status-error text-sm mt-1">{{ $message }}</p>
                    @enderror
                </div>

                @include('partials.recaptcha-loader')
            </div>

            <div class="flex justify-end gap-3">
                <x-optional-cta as="a" :href="route('tickets.index')">
                    {{ t('tickets.cancel') ?: 'Cancel' }}
                </x-optional-cta>
                <x-primary-cta>
                    {{ t('tickets.submit') ?: 'Open Ticket' }}
                </x-primary-cta>
            </div>
        </form>

    </div>


</x-app-layout>
