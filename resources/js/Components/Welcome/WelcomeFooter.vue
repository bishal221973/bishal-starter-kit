<script setup>
import { useTheme } from "@/composables/useTheme";

const { theme } = useTheme();
</script>

<template>
    <footer class="mt-12 pt-8">
        <!-- <div
            class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-6"
        >
            <div>
                <h4
                    class="text-sm font-bold text-slate-900 uppercase tracking-wider mb-4"
                >
                    Essential Links
                </h4>

                <div
                    class="flex flex-wrap items-center gap-x-6 gap-y-3 text-sm"
                >
                    <a
                        href="https://laravel.com/docs"
                        target="_blank"
                        class="flex items-center gap-2 text-slate-600 transition"
                        :style="{ color: theme.text }"
                    >
                        📖 Documentation
                    </a>

                    <a
                        href="https://github.com/laravel/laravel"
                        target="_blank"
                        class="flex items-center gap-2 text-slate-600 transition"
                        :style="{ color: theme.text }"
                    >
                        🐙 GitHub Repo
                    </a>

                    <a
                        href="https://laravel.com"
                        target="_blank"
                        class="flex items-center gap-2 text-slate-600 transition"
                        :style="{ color: theme.text }"
                    >
                        📦 Laravel Ecosystem
                    </a>

                    <a
                        href="https://tailwindcss.com"
                        target="_blank"
                        class="flex items-center gap-2 text-slate-600 transition"
                        :style="{ color: theme.text }"
                    >
                        🎨 Tailwind CSS
                    </a>
                </div>
            </div> 
            <div
                class="rounded-2xl border p-4 flex items-center justify-between gap-4 w-full lg:w-auto lg:min-w-[320px]"
                :style="{
                    backgroundColor: `${theme.primary}10`,
                    borderColor: `${theme.primary}30`,
                }"
            >
                <div>
                    <p
                        class="font-bold text-sm"
                        :style="{ color: theme.primary }"
                    >
                        Need Help?
                    </p>

                    <p class="text-xs text-slate-600 mt-1">
                        Join discussions and get support from the community.
                    </p>
                </div>

                <a
                    href="https://github.com/laravel/jetstream/discussions"
                    target="_blank"
                    class="px-4 py-2 rounded-xl text-white text-sm font-semibold whitespace-nowrap transition-all hover:opacity-90"
                    :style="{ backgroundColor: theme.primary }"
                >
                    Contact Support
                </a>
            </div>
        </div> -->

        <!-- Bottom Footer -->
        <div
            class="mt-8 pt-6 border-t border-slate-200 flex flex-col sm:flex-row items-center justify-between gap-3 max-w-4xl mx-auto pb-[20px]"
        >
            <p class="text-sm text-slate-500">
                © {{ new Date().getFullYear() }}
                {{ $page.props.settings?.site_name || 'Bishal Starter Kit' }}.
                All rights reserved.
            </p>

            <div class="flex items-center gap-4 text-sm text-slate-500">
                <span>Laravel</span>
                <span>•</span>
                <span>Vue 3</span>
                <span>•</span>
                <span>Inertia.js</span>
                <span>•</span>
                <span>Tailwind CSS</span>
            </div>
        </div>
    </footer>
</template>