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
                // project tokens (existing) – variables now contain raw RGB numbers
                primary: 'rgb(var(--color-primary) / <alpha-value>)',
                secondary: 'rgb(var(--color-secondary) / <alpha-value>)',
                'accent-primary': 'rgb(var(--color-accent-primary) / <alpha-value>)',
                'accent-secondary': 'rgb(var(--color-accent-secondary) / <alpha-value>)',

                // normalize Tailwind defaults to project tokens (requested mappings)
                // variables now store rgb triplets; opacity helpers still work via the same pattern
                blue: {
                    100: 'rgb(var(--color-accent-primary) / <alpha-value>)',
                    300: 'rgb(var(--color-accent-primary) / <alpha-value>)',
                    500: 'rgb(var(--color-accent-primary) / <alpha-value>)',
                    700: 'rgb(var(--color-accent-primary) / <alpha-value>)',
                    800: 'rgb(var(--color-accent-primary) / <alpha-value>)'
                },

                gray: {
                    100: 'rgb(var(--color-grey-light) / <alpha-value>)',
                    200: 'rgb(var(--color-grey-light) / <alpha-value>)',
                    400: 'rgb(var(--color-grey-medium) / <alpha-value>)',
                    500: 'rgb(var(--color-grey-medium) / <alpha-value>)',
                    700: 'rgb(var(--color-grey-dark) / <alpha-value>)',
                    800: 'rgb(var(--color-grey-dark) / <alpha-value>)'
                },

                amber: {
                    200: 'rgb(var(--color-accent-secondary) / <alpha-value>)'
                },

                yellow: {
                    100: 'rgb(var(--color-accent-secondary) / <alpha-value>)'
                },

                // map white to the project's light token where used (supports opacity)
                white: 'rgb(var(--color-white) / <alpha-value>)',

                // existing semantic tokens (kept as vars for backward compatibility)
                'grey-dark': 'rgb(var(--color-grey-dark) / <alpha-value>)',
                'grey-medium': 'rgb(var(--color-grey-medium) / <alpha-value>)',
                'grey-light': 'rgb(var(--color-grey-light) / <alpha-value>)',
                'status-success': 'rgb(var(--color-success) / <alpha-value>)',
                'status-error': 'rgb(var(--color-error) / <alpha-value>)',
                'status-warning': 'rgb(var(--color-warning) / <alpha-value>)',
                'status-info': 'rgb(var(--color-info) / <alpha-value>)',
                light: 'rgb(var(--color-light) / <alpha-value>)',
                dark: 'rgb(var(--color-dark) / <alpha-value>)'
            },
            fontFamily: {
                // primary interface font – all generic text
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
                // explicit family for headings; base layer will apply it to h1‑h6
                heading: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
