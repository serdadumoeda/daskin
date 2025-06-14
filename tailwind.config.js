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
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
            },
            colors: {
                // Warna yang sudah ada di app.blade.php atau bawaan
                primary: '#3b82f6',
                secondary: '#64748b',

                // Warna kustom baru untuk sidebar (sesuai contoh index.html)
                'sidebar-bg': '#1e2a4c',
                'sidebar-text': '#a9b4d4',
                'sidebar-active-bg': '#364263',
                'sidebar-border-color': '#364263', // Bisa sama dengan active-bg atau warna lain
                'sidebar-active-indicator': '#4a90e2',
                'white': '#ffffff', // Definisikan putih jika sering digunakan dengan nama kustom
                'transparent': 'transparent', // Definisikan transparan

                // Warna untuk filter buttons (dari index.html)
                'filter-btn-clear-bg': '#f8d7da',
                'filter-btn-clear-text': '#721c24',
                'filter-btn-clear-border': '#f5c6cb',
                'filter-btn-apply-bg': '#007bff',
                'filter-btn-apply-text': '#ffffff',
                'filter-btn-apply-border': '#007bff',

                // Warna ikon dari contoh style.css
                'icon-summary-1-bg': '#fff0c7',   // Kuning
                'icon-summary-1-text': '#ffab00',
                'icon-summary-2-bg': '#ffe0e6',   // Pink
                'icon-summary-2-text': '#f5365c',
                'icon-summary-3-bg': '#d1e9ff',   // Biru muda
                'icon-summary-3-text': '#1172ef',
                'icon-summary-4-bg': '#d4f8e1',   // Hijau muda
                'icon-summary-4-text': '#2dce89',
                'icon-summary-5-bg': '#E9D5FF',   // Ungu muda (Aplikasi Terintegrasi)
                'icon-summary-5-text': '#9333EA',
                // ...

                'icon-itjen-bg': '#fff0c7',        // Kuning (sama dengan summary 1)
                'icon-itjen-text': '#ffab00',
                'icon-sekjen-bg': '#ffe0e6',       // Pink (sama dengan summary 2)
                'icon-sekjen-text': '#f5365c',
            },
            borderRadius: { // borderRadius yang sudah ada
                'none':'0px',
                'sm':'4px',
                DEFAULT:'8px',
                'md':'12px',
                'lg':'16px',
                'xl':'20px',
                '2xl':'24px',
                '3xl':'32px',
                'full':'9999px',
                'button':'8px'
            }
        },
    },

    plugins: [forms],
};