
import '../css/app.css';

import { createApp, h, watchEffect } from 'vue';
import { createInertiaApp } from '@inertiajs/vue3';
import { resolvePageComponent } from 'laravel-vite-plugin/inertia-helpers';
import { ZiggyVue } from '../../vendor/tightenco/ziggy';

import { useTheme } from '@/composables/useTheme'
const { theme } = useTheme()

watchEffect(() => {
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
        '--background',
        theme.value.background
    )

    document.documentElement.style.setProperty(
        '--text',
        theme.value.text
    )
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
    },
    progress: {
        color: '#4B5563',
    },
});
