import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],
    darkMode: 'class', // <-- important!
    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
                serif: ['Playfair Display', 'serif'],
                body: ['Poppins', 'sans-serif'],
            },
            colors: {
                cream: '#DCC7AA',
                sage: '#A7B99E',
                brown: '#6B584C',
                offwhite: '#F6F1EB',
                darksage: '#5C6D5B',
                darkbrown: '#4A3F36',
            },
        },
    },
    
    plugins: [require('@tailwindcss/forms')],
};