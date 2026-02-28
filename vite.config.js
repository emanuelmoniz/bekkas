import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import path from 'path';

export default defineConfig({
    // make sure font files are treated as assets so they get copied and hashed
    assetsInclude: ['**/*.ttf', '**/*.otf', '**/*.woff', '**/*.woff2'],
    plugins: [
        laravel({
            input: ['resources/css/app.css', 'resources/css/home-splash.css', 'resources/js/app.js', 'resources/js/contact-tracking.js'],
            refresh: true,
        }),
    ],
    build: {
        rollupOptions: {
            output: {
                // keep a dedicated `fonts/` folder under build output
                assetFileNames: (assetInfo) => {
                    if (/(?:\.ttf|\.otf|\.woff2?|\.eot)$/.test(assetInfo.name || '')) {
                        return 'fonts/[name][extname]';
                    }
                    return assetInfo.name;
                },
            },
        },
    },
});
