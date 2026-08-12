import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

const defaultTheme = {
    primary: '#3d98aa',
    secondary: '#2f7f8f',
    accent: '#4fb6c8',
    text: '#1e293b',
    background: '#ffffff',
}

export function useTheme() {
    const page = usePage()

    const theme = computed(() => {
        const dbTheme = page.props?.theme || {}

        return {
            primary: dbTheme.primary || defaultTheme.primary,
            secondary: dbTheme.secondary || defaultTheme.secondary,
            accent: dbTheme.accent || defaultTheme.accent,
            text: dbTheme.text || defaultTheme.text,
            background: dbTheme.background || defaultTheme.background,
        }
    })

    return {
        theme,
    }
}