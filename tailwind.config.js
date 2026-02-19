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
                // project tokens (existing)
                primary: 'var(--color-primary)',
                secondary: 'var(--color-secondary)',
                'accent-primary': 'var(--color-accent-primary)',
                'accent-secondary': 'var(--color-accent-secondary)',

                // normalize Tailwind defaults to project tokens (requested mappings)
                // use rgb(var(--...-rgb) / <alpha-value>) so Tailwind opacity utilities map to CSS vars
                blue: {
                    100: 'rgb(var(--color-accent-primary-rgb) / <alpha-value>)',
                    300: 'rgb(var(--color-accent-primary-rgb) / <alpha-value>)',
                    500: 'rgb(var(--color-accent-primary-rgb) / <alpha-value>)',
                    700: 'rgb(var(--color-accent-primary-rgb) / <alpha-value>)',
                    800: 'rgb(var(--color-accent-primary-rgb) / <alpha-value>)'
                },

                gray: {
                    100: 'rgb(var(--color-grey-light-rgb) / <alpha-value>)',
                    200: 'rgb(var(--color-grey-light-rgb) / <alpha-value>)',
                    400: 'rgb(var(--color-grey-medium-rgb) / <alpha-value>)',
                    500: 'rgb(var(--color-grey-medium-rgb) / <alpha-value>)',
                    700: 'rgb(var(--color-grey-dark-rgb) / <alpha-value>)',
                    800: 'rgb(var(--color-grey-dark-rgb) / <alpha-value>)'
                },

                amber: {
                    200: 'rgb(var(--color-accent-secondary-rgb) / <alpha-value>)'
                },

                yellow: {
                    100: 'rgb(var(--color-accent-secondary-rgb) / <alpha-value>)'
                },

                // map white to the project's light token where used (supports opacity)
                white: 'rgb(var(--color-light-rgb) / <alpha-value>)',

                // existing semantic tokens (kept as vars for backward compatibility)
                'grey-dark': 'var(--color-grey-dark)',
                'grey-medium': 'var(--color-grey-medium)',
                'grey-light': 'var(--color-grey-light)',
                'status-success': 'var(--color-success)',
                'status-error': 'var(--color-error)',
                'status-warning': 'var(--color-warning)',
                'status-info': 'var(--color-info)',
                'light': 'var(--color-light)',
                'dark': 'var(--color-dark)'
            },
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
