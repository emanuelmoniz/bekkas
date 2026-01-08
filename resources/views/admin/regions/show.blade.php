<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            Region Details
        </h2>
    </x-slot>

    <div class="py-6 max-w-3xl mx-auto sm:px-6 lg:px-8">
        <div class="bg-white shadow rounded p-6 space-y-4">
            <div>
                <strong class="text-gray-700">Country:</strong>
                <p class="text-gray-900">
                    {{ app()->getLocale() === 'pt' ? $region->country->name_pt : $region->country->name_en }}
                </p>
            </div>

            <div>
                <strong class="text-gray-700">Name:</strong>
                <p class="text-gray-900">{{ $region->name }}</p>
            </div>

            <div class="grid grid-cols-2 gap-4">
                <div>
                    <strong class="text-gray-700">Postal Code From:</strong>
                    <p class="text-gray-900">{{ $region->postal_code_from }}</p>
                </div>

                <div>
                    <strong class="text-gray-700">Postal Code To:</strong>
                    <p class="text-gray-900">{{ $region->postal_code_to }}</p>
                </div>
            </div>

            <div>
                <strong class="text-gray-700">Active:</strong>
                <p class="text-gray-900">
                    <span class="px-2 py-1 rounded text-xs {{ $region->is_active ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                        {{ $region->is_active ? 'Yes' : 'No' }}
                    </span>
                </p>
            </div>
        </div>

        <div class="mt-6 flex justify-between">
            <a href="{{ route('admin.regions.index') }}"
               class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded">
                Back
            </a>

            <a href="{{ route('admin.regions.edit', $region) }}"
               class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-3 rounded">
                Edit Region
            </a>
        </div>
    </div>
</x-app-layout>
