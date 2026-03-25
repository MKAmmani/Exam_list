import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    darkMode: "class",
    theme: {
                extend: {
                    colors: {
                        "primary": "#FBAA9B",
                        "on-primary-fixed": "#3d1c16",
                        "background-light": "#fdf8f7",
                        "background-dark": "#3d1c16",

                        "outline-variant": "#d4c4c0",
                        "tertiary-fixed-dim": "#ffc4b5",
                        "on-primary": "#3d1c16",
                        "on-secondary": "#ffffff",
                        "on-tertiary-container": "#5c2a22",
                        "surface-container-highest": "#e8e2e0",
                        "tertiary-fixed": "#ffe5de",
                        "outline": "#8a756f",
                        "inverse-primary": "#ff8a75",
                        "primary-fixed": "#ffd5cc",
                        "inverse-surface": "#3d2a26",
                        "on-primary-container": "#5c2216",
                        "secondary-fixed": "#e8d4cf",
                        "on-primary-fixed-variant": "#521f16",
                        "on-error": "#ffffff",
                        "background": "#fdf8f7",
                        "on-secondary-fixed": "#3d2a26",
                        "on-surface-variant": "#5c4f4a",
                        "surface-container-low": "#fdf8f7",
                        "surface-container-high": "#f0e9e7",
                        "secondary-fixed-dim": "#d4bfb8",
                        "surface-tint": "#FBAA9B",
                        "on-tertiary-fixed-variant": "#52221c",
                        "error-container": "#ffdad6",
                        "surface-bright": "#ffffff",
                        "surface-variant": "#e8e2e0",
                        "on-secondary-fixed-variant": "#521f16",
                        "on-surface": "#1f1714",
                        "on-background": "#1f1714",
                        "surface": "#ffffff",
                        "on-error-container": "#93000a",
                        "inverse-on-surface": "#f5ebe8",
                        "primary-fixed-dim": "#ff8a75",
                        "on-tertiary-fixed": "#3d1c16",
                        "tertiary": "#c98a7a",
                        "secondary-container": "#e8d4cf",
                        "surface-container-lowest": "#ffffff",
                        "surface-container": "#f5ebe8",
                        "on-tertiary": "#ffffff",
                        "primary-container": "#FBAA9B",
                        "surface-dim": "#fdf8f7",
                        "error": "#ba1a1a",
                        "tertiary-container": "#ffc4b5"
                    },
                    fontFamily: {
                        "display": ["Public Sans", "sans-serif"]
                    },
                    borderRadius: {"DEFAULT": "0.25rem", "lg": "0.5rem", "xl": "0.75rem", "full": "9999px"},
                },
            },

    plugins: [forms],
};
