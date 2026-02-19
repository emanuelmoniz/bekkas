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
                primary: 'rgb(var(--color-primary-rgb) / <alpha-value>)',
                secondary: 'rgb(var(--color-secondary-rgb) / <alpha-value>)',
                'accent-primary': 'rgb(var(--color-accent-primary-rgb) / <alpha-value>)',
                'accent-secondary': 'rgb(var(--color-accent-secondary-rgb) / <alpha-value>)',

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
                'grey-dark': 'rgb(var(--color-grey-dark-rgb) / <alpha-value>)',
                'grey-medium': 'rgb(var(--color-grey-medium-rgb) / <alpha-value>)',
                'grey-light': 'rgb(var(--color-grey-light-rgb) / <alpha-value>)',
                'status-success': 'rgb(var(--color-success-rgb) / <alpha-value>)',
                'status-error': 'rgb(var(--color-error-rgb) / <alpha-value>)',
                'status-warning': 'rgb(var(--color-warning-rgb) / <alpha-value>)',
                'status-info': 'rgb(var(--color-info-rgb) / <alpha-value>)',
                'light': 'rgb(var(--color-light-rgb) / <alpha-value>)',
                'dark': 'rgb(var(--color-dark-rgb) / <alpha-value>)'
            },
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
