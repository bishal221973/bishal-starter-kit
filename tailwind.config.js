import defaultTheme from 'tailwindcss/defaultTheme';
import forms from '@tailwindcss/forms';
import typography from '@tailwindcss/typography';

/** @type {import('tailwindcss').Config} */
export default {
    content: [
        './vendor/laravel/framework/src/Illuminate/Pagination/resources/views/*.blade.php',
        './vendor/laravel/jetstream/**/*.blade.php',
        './storage/framework/views/*.php',
        './resources/views/**/*.blade.php',
        './resources/js/**/*.vue',
    ],

    theme: {
        extend: {
            fontFamily: {
                sans: ['Figtree', ...defaultTheme.fontFamily.sans],
                Inter:['Inter']
            },
             font_size: {
                base: 'var(--font_size)',
            },
            colors: {
                primary: 'var(--primary)',
                secondary: 'var(--secondary)',
                accent: 'var(--accent)',
                success: 'var(--success)',
                warning: 'var(--warning)',
                danger: 'var(--danger)',
                info: 'var(--info)',
                background: 'var(--background)',
                text_color: 'var(--text_color)',
                border_color: 'var(--border_color)',
                sidebar: 'var(--sidebar)',
                sidebar_text: 'var(--sidebar_text)',
                sidebar_hover_color: 'var(--sidebar_hover_color)',
                sidebar_hover_text_color: 'var(--sidebar_hover_text_color)',
                sidebar_active_color: 'var(--sidebar_active_color)',
                sidebar_icon_color: 'var(--sidebar_icon_color)',
                navbar: 'var(--navbar)',
                navbar_text: 'var(--navbar_text)',
                navbar_border: 'var(--navbar_border)',
                card_bg_color: 'var(--card_bg_color)',
                card_header_color: 'var(--card_header_color)',
                card_footer_color: 'var(--card_footer_color)',
            },
            width: {
                sidebar_width: 'var(--sidebar_width)',
            },
            height: {
                navbar_height: 'var(--navbar_height)',
            },

            borderRadius: {
                card_border_radius: 'var(--card_border_radius)',
                button_border_radius: 'var(--button_border_radius)',
            },
        },
    },

    plugins: [forms, typography],
};
