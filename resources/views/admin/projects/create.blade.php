<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-grey-dark leading-tight">
            Create Project
        </h2>
    </x-slot>

    <div class="py-6 max-w-7xl mx-auto sm:px-6 lg:px-8">
        @include('admin.projects._form', ['mode' => 'create'])
    </div>
</x-app-layout>
