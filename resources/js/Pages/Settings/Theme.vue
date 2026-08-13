<script setup>
import { useForm } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';

const props=defineProps({
    setting:Object,
})

const form = useForm({
    site_name: '',
    site_description: '',

    primary_color: props.setting?.primary_color ?? '#3d98aa',
    secondary_color: props?.setting?.secondary_color ?? '#2f7f8f',
    accent_color: props?.setting?.accent_color ?? '#4fb6c8',

    background_color:props?.setting?.background_color ?? '#ffffff',
    text_color:props?.setting?.text_color ?? '#1e293b',

    sidebar_bg_color: props.setting?.sidebar_bg_color ?? '#0f172a',
    sidebar_text_color:props?.setting?.sidebar_text_color ?? '#ffffff',
    sidebar_hover_color:props?.setting?.sidebar_hover_color ?? '#1e293b',
    sidebar_hover_text_color:props?.setting?.sidebar_hover_text_color ?? '#1e293b',
    sidebar_active_color:props?.setting?.sidebar_active_color ?? '#3d98aa',
    sidebar_width:props?.setting?.sidebar_width ?? '280',

    navbar_bg_color:props?.setting?.navbar_bg_color ?? '#ffffff',
    navbar_text_color:props?.setting?.navbar_text_color ?? '#1e293b',
    navbar_border_color:props?.setting?.navbar_border_color ?? '#1e293b',
    navbar_height:props?.setting?.navbar_height ?? '70',

    theme_mode:props?.setting.theme_mode ?? 'light',
    layout_mode:props?.setting.layout_mode ?? 'full',

    card_border_radius:props?.card_border_radius ?? 16,
    button_border_radius:props?.button_border_radius ?? 10,

    font_family:props?.setting?.font_family ?? 'Inter',
    font_size:props?.setting?.font_size ?? 14,

    enable_animations:props?.setting?.enable_animations ?? true,
    enable_breadcrumbs:props?.setting?.enable_breadcrumbs ?? true,
    enable_notifications:props?.setting?.enable_notifications ?? true,
});

const save = () => {
    form.post(route('theme.setting.update'));
};
</script>

<template>
    <AppLayout>
        <div class="max-w-7xl mx-auto p-6">
            <div class="mb-6">
                <h1 class="text-2xl font-bold">
                    Theme Settings
                </h1>

                <p class="text-slate-500">
                    Customize the appearance of your application.
                </p>
            </div>

            <form @submit.prevent="save">
                <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">

                    <!-- Brand Settings -->
                    <div class="bg-white rounded-2xl shadow-sm border p-6">
                        <h2 class="font-semibold mb-4">
                            Brand Settings
                        </h2>

                        <div class="space-y-4">
                            <div>
                                <label class="block mb-2">
                                    Site Name
                                </label>

                                <input
                                    v-model="form.site_name"
                                    class="w-full border rounded-xl px-4 py-2"
                                >
                            </div>

                            <div>
                                <label class="block mb-2">
                                    Site Description
                                </label>

                                <textarea
                                    v-model="form.site_description"
                                    class="w-full border rounded-xl px-4 py-2"
                                />
                            </div>
                        </div>
                    </div>

                    <!-- Theme Colors -->
                    <div class="bg-white rounded-2xl shadow-sm border p-6">
                        <h2 class="font-semibold mb-4">
                            Theme Colors
                        </h2>

                        <div class="grid grid-cols-2 gap-4">

                            <div>
                                <label>Primary</label>
                                <input
                                    type="color"
                                    v-model="form.primary_color"
                                    class="w-full h-12"
                                >
                            </div>

                            <div>
                                <label>Secondary</label>
                                <input
                                    type="color"
                                    v-model="form.secondary_color"
                                    class="w-full h-12"
                                >
                            </div>

                            <div>
                                <label>Accent</label>
                                <input
                                    type="color"
                                    v-model="form.accent_color"
                                    class="w-full h-12"
                                >
                            </div>

                            <div>
                                <label>Background</label>
                                <input
                                    type="color"
                                    v-model="form.background_color"
                                    class="w-full h-12"
                                >
                            </div>

                        </div>
                    </div>

                    <!-- Sidebar -->
                    <div class="bg-white rounded-2xl shadow-sm border p-6">
                        <h2 class="font-semibold mb-4">
                            Sidebar
                        </h2>

                        <div class="space-y-4">

                            <div>
                                <label>Background Color</label>

                                <input
                                    type="color"
                                    v-model="form.sidebar_bg_color"
                                    class="w-full h-12"
                                >
                            </div>

                            <div>
                                <label>Text Color</label>

                                <input
                                    type="color"
                                    v-model="form.sidebar_text_color"
                                    class="w-full h-12"
                                >
                            </div>
                            <div>
                                <label>Hover Background Color</label>

                                <input
                                    type="color"
                                    v-model="form.sidebar_hover_color"
                                    class="w-full h-12"
                                >
                            </div>
                            <div>
                                <label>Hover Text Color</label>

                                <input
                                    type="color"
                                    v-model="form.sidebar_hover_text_color"
                                    class="w-full h-12"
                                >
                            </div>

                            <div>
                                <label>Width</label>

                                <input
                                    type="number"
                                    v-model="form.sidebar_width"
                                    class="w-full border rounded-xl px-4 py-2"
                                >
                            </div>

                        </div>
                    </div>

                    <!-- Navbar -->
                    <div class="bg-white rounded-2xl shadow-sm border p-6">
                        <h2 class="font-semibold mb-4">
                            Navbar
                        </h2>

                        <div class="space-y-4">

                            <div>
                                <label>Navbar Background</label>

                                <input
                                    type="color"
                                    v-model="form.navbar_bg_color"
                                    class="w-full h-12"
                                >
                            </div>
                            <div>
                                <label>Navbar Border Color</label>

                                <input
                                    type="color"
                                    v-model="form.navbar_border_color"
                                    class="w-full h-12"
                                >
                            </div>

                            <div>
                                <label>Navbar Text</label>

                                <input
                                    type="color"
                                    v-model="form.navbar_text_color"
                                    class="w-full h-12"
                                >
                            </div>

                            <div>
                                <label>Width</label>

                                <input
                                    type="number"
                                    v-model="form.navbar_height"
                                    class="w-full border rounded-xl px-4 py-2"
                                >
                            </div>

                        </div>
                    </div>

                    <!-- Typography -->
                    <div class="bg-white rounded-2xl shadow-sm border p-6">
                        <h2 class="font-semibold mb-4">
                            Typography
                        </h2>

                        <div class="space-y-4">

                            <input
                                v-model="form.font_family"
                                placeholder="Font Family"
                                class="w-full border rounded-xl px-4 py-2"
                            >

                            <input
                                type="number"
                                v-model="form.font_size"
                                placeholder="Font Size"
                                class="w-full border rounded-xl px-4 py-2"
                            >

                        </div>
                    </div>

                    <!-- Features -->
                    <div class="bg-white rounded-2xl shadow-sm border p-6">
                        <h2 class="font-semibold mb-4">
                            Features
                        </h2>

                        <div class="space-y-4">

                            <label class="flex items-center gap-3">
                                <input
                                    type="checkbox"
                                    v-model="form.enable_animations"
                                >
                                Enable Animations
                            </label>

                            <label class="flex items-center gap-3">
                                <input
                                    type="checkbox"
                                    v-model="form.enable_breadcrumbs"
                                >
                                Enable Breadcrumbs
                            </label>

                            <label class="flex items-center gap-3">
                                <input
                                    type="checkbox"
                                    v-model="form.enable_notifications"
                                >
                                Enable Notifications
                            </label>

                        </div>
                    </div>

                </div>

                <div class="mt-6">
                    <button
                        type="submit"
                        class="px-6 py-3 rounded-xl bg-primary text-white"
                    >
                        Save Settings
                    </button>
                </div>
            </form>
        </div>
    </AppLayout>
</template>
