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
            fontFamily: {
                sans: [
                    'Pretendard',
                    'Pretendard Variable',
                    'Apple SD Gothic Neo',
                    'Malgun Gothic',
                    ...defaultTheme.fontFamily.sans,
                ],
            },
            colors: {
                // 브랜드 – 토스 블루 (#3182f6 기준)
                brand: {
                    50: '#eff5ff',
                    100: '#d9e8ff',
                    200: '#b8d5ff',
                    300: '#8bbcff',
                    400: '#579bf9',
                    500: '#3182f6',
                    600: '#2570e6',
                    700: '#1f5fd0',
                    800: '#1c4fab',
                    900: '#1a4489',
                },
                gold: {
                    400: '#f6b73c',
                    500: '#f59e0b',
                    600: '#d97706',
                },
            },
            maxWidth: {
                container: '1200px',
            },
        },
    },

    plugins: [forms],
};
