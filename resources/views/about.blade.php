<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800">
            {{ t('nav.about') ?: 'About Us' }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    <h3 class="text-lg font-semibold mb-4">{{ t('about.title') ?: 'About BEKKAS' }}</h3>
                    <p class="mb-4">
                        {{ t('about.description') ?: 'Welcome to BEKKAS. We specialize in quality products and exceptional service.' }}
                    </p>
                    <p>
                        {{ t('about.content') ?: 'More information coming soon.' }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
