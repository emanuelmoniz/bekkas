<footer class="bg-gray-900 dark:bg-gray-950 text-gray-300 py-12 border-t border-gray-800">
    <div class="container mx-auto px-4">
        <div class="text-center pb-8">
            <a href="{{ route('terms') }}" class="underline text-sm me-4">{{ t('footer.terms') ?: 'Terms' }}</a>
            <a href="{{ route('privacy') }}" class="underline text-sm">{{ t('footer.privacy') ?: 'Privacy' }}</a>
        </div>

        <!-- Copyright -->
        <div class="border-t border-gray-800 pt-8 text-center text-sm">
            <p>&copy; {{ date('Y') }} {{ config('app.name', 'BEKKAS') }}. {{ t('footer.rights') ?: 'All rights reserved.' }}</p>
        </div>
    </div>
</footer>
