import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
    ],

    theme: {
        extend: {
            colors: {
                'pac-yellow': {
                    '50':  '#FDFAF3',
                    '100': '#FBF5E3',
                    '200': '#F7E8BD',
                    '300': '#F2D68A',
                    '400': '#EBC150',
                    '500': '#E5AD16',
                    '600': '#C99813',
                    '700': '#A47C0F',
                    '800': '#7D5F0C',
                    '900': '#574108',
                },
                'pac-black': {
                    '50':  '#F5F5F5',
                    '100': '#E8E8E8',
                    '200': '#C8C8C8',
                    '300': '#A0A0A0',
                    '400': '#707070',
                    '500': '#404040',
                    '600': '#2D2020',
                    '700': '#231818',
                    '800': '#1E1414',
                    '900': '#1A1212',
                },
                'pac-pink': {
                    '50':  '#FDF2F5',
                    '100': '#FCE0E8',
                    '200': '#F8B7CA',
                    '300': '#F37FA1',
                    '400': '#ED3F72',
                    '500': '#E80043',
                    '600': '#CC003A',
                    '700': '#A70030',
                    '800': '#7F0024',
                    '900': '#580019',
                },
                'pac-green': {
                    '500': '#10b981',
                    '600': '#059669',
                },
                'pac-red': {
                    '500': '#ef4444',
                    '600': '#dc2626',
                },
            },
            fontFamily: {
                'display': ['"Barlow Condensed"', 'sans-serif'],
                'body':    ['"Noto Sans KR"', 'sans-serif'],
                sans: ['"Noto Sans KR"', ...defaultTheme.fontFamily.sans],
            },
        },
    },

    plugins: [forms],
};
