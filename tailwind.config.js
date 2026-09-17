import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],


    safelist: [
        'bg-blue-600', 'hover:bg-blue-700',
        'bg-purple-600', 'hover:bg-purple-700',
        'bg-green-600', 'hover:bg-green-700',
        'bg-yellow-600', 'hover:bg-yellow-700',
        'bg-orange-600', 'hover:bg-orange-700',
    ],

    theme: {
    extend: {
        fontFamily: {
            sans: ['Poppins', 'sans-serif'],
            montserrat: ['Montserrat', 'sans-serif'],
        },
        colors: {
            'bpn-main': '#F5F0E6',
            'bpn-cards': '#FFFCF5',
            'bpn-brown': '#5C4033',
            'bpn-gold': '#D4AF37',
        }
        },
    },

    plugins: [forms],
};
