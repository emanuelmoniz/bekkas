<footer class="bg-dark dark:bg-dark text-grey-medium py-12 border-t border-grey-dark">
    <div class="container mx-auto px-4">
        <div class="text-center pb-8">
            <a href="{{ route('terms') }}" class="underline text-sm me-4">{{ t('footer.terms') ?: 'Terms' }}</a>
            <a href="{{ route('privacy') }}" class="underline text-sm">{{ t('footer.privacy') ?: 'Privacy' }}</a>
        </div>

        <!-- Copyright -->
        <div class="border-t border-grey-dark pt-8 text-center text-sm">
            <p>&copy; {{ date('Y') }} {{ config('app.name', 'BEKKAS') }}. {{ t('footer.rights') ?: 'All rights reserved.' }}</p>
            <p class="mt-2 text-sm">
                {!! t('footer.designed_by', [
                    'az' => '<a href="https://azseashell.com" target="_blank" rel="noopener noreferrer" class="underline text-accent-primary hover:text-accent-primary">AZSeashell</a>',
                    'sofia' => '<a href="https://www.linkedin.com/in/sofia-leite-alves-b5752a262/" target="_blank" rel="noopener noreferrer" class="underline text-accent-primary hover:text-accent-primary">Sofia Alves</a>'
                ]) ?: 'Created by <a href="https://azseashell.com" target="_blank" rel="noopener noreferrer" class="underline text-accent-primary hover:text-accent-primary">AZSeashell</a> and <a href="https://www.linkedin.com/in/sofia-leite-alves-b5752a262/" target="_blank" rel="noopener noreferrer" class="underline text-accent-primary hover:text-accent-primary">Sofia Alves</a>' !!}
            </p>
        </div>
    </div>
</footer>
