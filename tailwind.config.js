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
                // Warna bawaan
                primary: '#3b82f6',
                secondary: '#64748b',

                // PALET WARNA SIDEBAR
                'sidebar-bg': '#2F6968',
                'sidebar-text': '#E0E0E0',
                'sidebar-active-bg': '#3E8785',
                'sidebar-border-color': '#245352',
                'sidebar-active-indicator': '#FFBF00',
                
                'white': '#ffffff', 
                'transparent': 'transparent', 

                'theme-primary': '#3E8785',         // Hijau Cerah untuk Aksi Utama
                'theme-primary-dark': '#2F6968',    // Hijau Tua untuk Hover
                'theme-primary-darker': '#245352', // Hijau Paling Gelap untuk Active
                'theme-accent': '#FFBF00',          // Aksen Emas

                // ** PALET WARNA BARU UNTUK TOMBOL FILTER **
                'filter-btn-clear-bg': '#FEF2F2',       // Merah sangat muda
                'filter-btn-clear-text': '#DC2626',      // Merah
                'filter-btn-clear-border': '#FECACA',   // Merah muda
                'filter-btn-apply-bg': '#3E8785',       // Hijau Cerah (Sama dengan menu aktif)
                'filter-btn-apply-text': '#ffffff',      // Putih
                'filter-btn-apply-border': '#3E8785',   // Hijau Cerah

                // Warna ikon dashboard (biarkan default atau sesuaikan nanti)
                'icon-summary-1-bg': '#fff0c7',
                'icon-summary-1-text': '#ffab00',
                'icon-summary-2-bg': '#ffe0e6',
                'icon-summary-2-text': '#f5365c',
                'icon-summary-3-bg': '#d1e9ff',
                'icon-summary-3-text': '#1172ef',
                'icon-summary-4-bg': '#d4f8e1',
                'icon-summary-4-text': '#2dce89',
                'icon-summary-5-bg': '#E9D5FF',
                'icon-summary-5-text': '#9333EA',
                'icon-itjen-bg': '#fff0c7',
                'icon-itjen-text': '#ffab00',
                'icon-sekjen-bg': '#ffe0e6',
                'icon-sekjen-text': '#f5365c',
            },
            borderRadius: { 
                'none':'0px',
                'sm': '0.125rem',
                'md': '0.375rem',
                'lg': '0.5rem',
                'full': '9999px',
                'large': '12px',
            }
        },
    },

    plugins: [forms],
};