import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import { createRequire } from 'module';

const require = createRequire(import.meta.url);

/** @type {import('tailwindcss').Config} */
export default {
    darkMode: 'class',
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
        './app/**/*.php',
    ],

    theme: {
        extend: {
            colors: {
                primary: {
                    50: '#eef9ff',
                    100: '#d8f1ff',
                    200: '#b9e8ff',
                    300: '#89dbff',
                    400: '#51c5ff',
                    500: '#00A3FF',
                    600: '#0088DD',
                    700: '#006BB3',
                    800: '#005A93',
                    900: '#004A7A',
                    950: '#002E4D',
                },
                sidebar: {
                    light: '#ffffff',
                    dark: '#0f172a',
                },
            },
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            boxShadow: {
                card: '0 1px 3px 0 rgba(0,0,0,0.04), 0 1px 2px -1px rgba(0,0,0,0.06)',
                'card-hover': '0 10px 15px -3px rgba(0,0,0,0.08), 0 4px 6px -4px rgba(0,0,0,0.04)',
                nav: '0 1px 3px 0 rgba(0,0,0,0.05)',
                dropdown: '0 10px 40px rgba(0,0,0,0.12)',
            },
            keyframes: {
                fadeIn: {
                    '0%': { opacity: '0', transform: 'translateY(-10px)' },
                    '100%': { opacity: '1', transform: 'translateY(0)' },
                },
                slideIn: {
                    '0%': { opacity: '0', transform: 'translateX(-20px)' },
                    '100%': { opacity: '1', transform: 'translateX(0)' },
                },
            },
            animation: {
                'fade-in': 'fadeIn 0.2s ease-out',
                'slide-in': 'slideIn 0.3s ease-out',
            },
        },
    },

    plugins: [forms],
};
