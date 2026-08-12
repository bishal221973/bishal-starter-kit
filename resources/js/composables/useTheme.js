import { computed } from 'vue'
import { usePage } from '@inertiajs/vue3'

const defaultTheme = {
    primary: '#3d98aa',
    secondary: '#2f7f8f',
    success: '#2f7f8f',
    accent: '#4fb6c8',
    warning: '#1e293b',
    danger: '#ffffff',
    info: '#ffffff',
    background: '#ffffff',
    text_color: '#ffffff',
    border_color: '#ffffff',
    sidebar_position: '#ffffff',
    sidebar_width: '#ffffff',
    sidebar: '#ffffff',
    sidebar_text: '#ffffff',
    sidebar_hover_color: '#ffffff',
    sidebar_active_color: '#ffffff',
    sidebar_icon_color: '#ffffff',
    navbar: '#ffffff',
    navbar_text: '#ffffff',
    navbar_border: '#ffffff',
    navbar_height: '#ffffff',
    card_border_radius: '#ffffff',
    button_border_radius: '#ffffff',
    font_family: '#ffffff',
    font_size: '#ffffff',
}

export function useTheme() {
    const page = usePage()

    const theme = computed(() => {
        const dbTheme = page.props?.theme || {}
        // console.log(dbTheme)
        return {
            primary: dbTheme.primary_color || defaultTheme.primary,
            secondary: dbTheme.secondary_color || defaultTheme.secondary,
            accent: dbTheme.accent_color || defaultTheme.accent,
            success: dbTheme.success_color || defaultTheme.success,
            warning: dbTheme.warning_color || defaultTheme.warning,
            danger: dbTheme.danger_color || defaultTheme.danger,
            info: dbTheme.info_color || defaultTheme.info,
            background: dbTheme.background_color || defaultTheme.background,
            text_color: dbTheme.text_color || defaultTheme.text_color,
            border_color: dbTheme.border_color || defaultTheme.border_color,
            sidebar_position: dbTheme.sidebar_position || defaultTheme.sidebar_position,
            sidebar_width: dbTheme.sidebar_width || defaultTheme.sidebar_width,
            sidebar: dbTheme.sidebar_bg_color || defaultTheme.sidebar,
            sidebar_text: dbTheme.sidebar_text_color || defaultTheme.sidebar_text,
            sidebar_hover_color: dbTheme.sidebar_hover_color || defaultTheme.sidebar_hover_color,
            sidebar_active_color: dbTheme.sidebar_active_color || defaultTheme.sidebar_active_color,
            sidebar_icon_color: dbTheme.sidebar_icon_color || defaultTheme.sidebar_icon_color,
            navbar: dbTheme.navbar_bg_color || defaultTheme.navbar,
            navbar_text: dbTheme.navbar_text_color || defaultTheme.navbar_text,
            navbar_border: dbTheme.navbar_border_color || defaultTheme.navbar_border,
            navbar_height: dbTheme.navbar_height || defaultTheme.navbar_height,
            card_border_radius: dbTheme.card_border_radius || defaultTheme.card_border_radius,
            button_border_radius: dbTheme.button_border_radius || defaultTheme.button_border_radius,
            font_family: dbTheme.font_family || defaultTheme.font_family,
            font_size: dbTheme.font_size || defaultTheme.font_size,
        }
    })

    return {
        theme,
    }
}