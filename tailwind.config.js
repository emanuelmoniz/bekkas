import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],

    theme: {
        extend: {
            colors: {
                primary: 'var(--color-primary)',
                secondary: 'var(--color-secondary)',
                'accent-primary': 'var(--color-accent-primary)',
                'accent-secondary': 'var(--color-accent-secondary)',
                'grey-dark': 'var(--color-grey-dark)',
                'grey-medium': 'var(--color-grey-medium)',
                'grey-light': 'var(--color-grey-light)',
                'status-success': 'var(--color-success)',
                'status-error': 'var(--color-error)',
                'status-warning': 'var(--color-warning)',
                'status-info': 'var(--color-info)',
                'background': 'var(--color-background)',
            },
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
