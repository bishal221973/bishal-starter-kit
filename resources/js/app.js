
import '../css/app.css';

import { createApp, h, watchEffect } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';
// import { useZoom } from "@/composables/useZoom";

import { useTheme } from '@/composables/useTheme'
const { theme } = useTheme()

watchEffect(() => {
    // Theme Color
    document.documentElement.style.setProperty(
        '--primary',
        theme.value.primary
    )
    document.documentElement.style.setProperty(
        '--secondary',
        theme.value.secondary
    )
    document.documentElement.style.setProperty(
        '--accent',
        theme.value.accent
    )
    document.documentElement.style.setProperty(
        '--success',
        theme.value.success
    )
    document.documentElement.style.setProperty(
        '--warning',
        theme.value.warning
    )
    document.documentElement.style.setProperty(
        '--danger',
        theme.value.danger
    )
    document.documentElement.style.setProperty(
        '--info',
        theme.value.info
    )
    document.documentElement.style.setProperty(
        '--background',
        theme.value.background
    )
    document.documentElement.style.setProperty(
        '--text_color',
        theme.value.text_color
    )
    document.documentElement.style.setProperty(
        '--border_color',
        theme.value.border_color
    )

    // Sidebar
    document.documentElement.style.setProperty(
        '--sidebar_position',
        theme.value.sidebar_position
    )
    document.documentElement.style.setProperty(
        '--sidebar_width',
        `${theme.value.sidebar_width}px`
    );
    document.documentElement.style.setProperty(
        '--sidebar',
        theme.value.sidebar
    )
    document.documentElement.style.setProperty(
        '--sidebar_text',
        theme.value.sidebar_text
    )
    document.documentElement.style.setProperty(
        '--sidebar_hover_color',
        theme.value.sidebar_hover_color
    )
    document.documentElement.style.setProperty(
        '--sidebar_hover_text_color',
        theme.value.sidebar_hover_text_color
    )
    document.documentElement.style.setProperty(
        '--sidebar_active_color',
        theme.value.sidebar_active_color
    )
    document.documentElement.style.setProperty(
        '--sidebar_icon_color',
        theme.value.sidebar_icon_color
    )

    // Navbar
    document.documentElement.style.setProperty(
        '--navbar',
        theme.value.navbar
    )
    document.documentElement.style.setProperty(
        '--navbar_text',
        theme.value.navbar_text
    )
    document.documentElement.style.setProperty(
        '--navbar_border',
        theme.value.navbar_border
    )
    document.documentElement.style.setProperty(
        '--navbar_height',
        `${theme.value.navbar_height}px`
    )

    // Card
    document.documentElement.style.setProperty(
        '--card_border_radius',
        theme.value.card_border_radius
    )
     document.documentElement.style.setProperty(
        '--card_bg_color',
        theme.value.card_bg_color
    )
    document.documentElement.style.setProperty(
        '--card_header_color',
        theme.value.card_header_color
    )
    document.documentElement.style.setProperty(
        '--card_footer_color',
        theme.value.card_footer_color
    )

    // Button
    document.documentElement.style.setProperty(
        '--button_border_radius',
        theme.value.button_border_radius
    )

    // Font
    document.documentElement.style.setProperty(
        '--font_family',
        theme.value.font_family
    )
    document.documentElement.style.setProperty(
        '--font_size',
        theme.value.font_size
    )

    console.log(theme.value)

})

const appName = import.meta.env.VITE_APP_NAME || 'Laravel';

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) => resolvePageComponent(`./Pages/${name}.vue`, import.meta.glob('./Pages/**/*.vue')),
    setup({ el, App, props, plugin }) {
        return createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(ZiggyVue)
            .mount(el);
            // useZoom();
    },
    progress: {
        color: '#4B5563',
    },
    // Initialize site-wide zoom
});
