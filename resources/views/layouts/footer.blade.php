<footer class="bg-dark text-grey-medium py-12 border-t border-grey-dark">
    <div class="container mx-auto px-4">
        <div class="text-center pb-8">
            <a href="{{ route('terms') }}" class="text-sm me-4 text-accent-primary hover:text-accent-primary/90 no-underline">{{ t('footer.terms') }}</a>
            <a href="{{ route('terms') }}#returns" class="text-sm me-4 text-accent-primary hover:text-accent-primary/90 no-underline">{{ t('footer.return_refunds') }}</a>
            <a href="{{ route('terms') }}#shipping" class="text-sm me-4 text-accent-primary hover:text-accent-primary/90 no-underline">{{ t('footer.shipping_policy') }}</a>
            <a href="{{ route('privacy') }}" class="text-sm text-accent-primary hover:text-accent-primary/90 no-underline">{{ t('footer.privacy') }}</a>
        </div>

        <!-- Copyright -->
        <div class="border-t border-grey-dark pt-8 text-center text-sm">
            <p>&copy; {{ date('Y') }} {{ config('app.name', 'BEKKAS') }}. {{ t('footer.rights') ?: 'All rights reserved.' }}</p>
            <p class="mt-2 text-sm">
                {!! t('footer.designed_by', [
                    'az' => '<a href="https://azseashell.com" target="_blank" rel="noopener noreferrer" class="text-accent-primary hover:text-accent-primary hover:text-accent-primary/90 no-underline">AZSeashell</a>',
                    'sofia' => 'Sofia Alves'
                ]) ?: 'Created by <a href="https://azseashell.com" target="_blank" rel="noopener noreferrer" class="text-accent-primary hover:text-accent-primary hover:text-accent-primary/90 no-underline">AZSeashell</a> and <a href="https://www.linkedin.com/in/sofia-leite-alves-b5752a262/" target="_blank" rel="noopener noreferrer" class="text-accent-primary hover:text-accent-primary hover:text-accent-primary/90 no-underline">Sofia Alves</a>' !!}
            </p>
        </div>
    </div>
</footer>
