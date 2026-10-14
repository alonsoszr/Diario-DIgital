import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: ['selector', '.tema-oscuro'],
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            //colores semánticos: cambian solos entre tema claro y oscuro (ver resources/css/app.css)
            colors: {
                page: 'rgb(var(--c-page) / <alpha-value>)',
                surface: 'rgb(var(--c-surface) / <alpha-value>)',
                subtle: 'rgb(var(--c-subtle) / <alpha-value>)',
                ink: 'rgb(var(--c-ink) / <alpha-value>)',
                muted: 'rgb(var(--c-muted) / <alpha-value>)',
                brand: 'rgb(var(--c-brand) / <alpha-value>)',
                line: 'rgb(var(--c-line) / <alpha-value>)',
            },
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
