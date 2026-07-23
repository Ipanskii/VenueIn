import defaultTheme from 'tailwindcss/defaultTheme';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.js',
    ],

    theme: {
        extend: {
            colors: {
                'brand-teal': {
                    DEFAULT: '#0F6E56',
                    50:  '#E8F5F1',
                    100: '#C4E8DC',
                    200: '#9DD9C5',
                    300: '#72C9AC',
                    400: '#44B892',
                    500: '#22A07A',
                    600: '#0F6E56',
                    700: '#0A5242',
                    800: '#063630',
                    900: '#021C18',
                },
                'brand-orange': {
                    DEFAULT: '#D85A30',
                    50:  '#FDF0EB',
                    100: '#FAD9CC',
                    200: '#F4B09A',
                    300: '#EC8567',
                    400: '#E36B47',
                    500: '#D85A30',
                    600: '#B84625',
                    700: '#94341A',
                    800: '#702510',
                    900: '#4A1508',
                },
            },

            fontFamily: {
                sans: ['"Plus Jakarta Sans"', 'Inter', ...defaultTheme.fontFamily.sans],
            },

            boxShadow: {
                'card': '0 2px 8px 0 rgb(0 0 0 / 0.06)',
                'card-hover': '0 8px 24px 0 rgb(0 0 0 / 0.10)',
            },
        },
    },

    plugins: [],
};
