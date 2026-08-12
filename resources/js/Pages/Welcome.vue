<script setup>
import { Head, Link } from '@inertiajs/vue3';
import ApplicationMark from '@/Components/ApplicationMark.vue';
import JetDropdown from '@/Components/Dropdown.vue';
import JetDropdownLink from '@/Components/DropdownLink.vue';
import JetNavLink from '@/Components/NavLink.vue';
import JetResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import HorizontalLogo from '@/Components/Logo/HorizontalLogo.vue';
import ProfileDropdown from '@/Components/ProfileDropdown.vue';
defineProps({
    auth: Object,
});

// Simple reactive state for testing the command execution feedback
import { ref } from 'vue';
const isCopied = ref(false);

const copyCommand = () => {
    navigator.clipboard.writeText('php artisan jetstream:install');
    isCopied.value = true;
    setTimeout(() => isCopied.value = false, 2000);
};
</script>

<template>
    <Head title="Bishal Starter Kit" />

    <div class="min-h-screen bg-slate-50 text-slate-800 antialiased font-sans">
        <!-- Top Navigation Bar -->
        <nav class="bg-white border-b border-slate-200 sticky top-0 z-50 px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center h-16 max-w-7xl mx-auto">
                <div class="flex items-center space-x-8">
                    <!-- Logo -->
                    <div class="shrink-0 flex items-center">
                        <Link :href="route('dashboard')">
                            <HorizontalLogo class="h-[100px]"/>
                        </Link>
                        
                    </div>
                </div>

                <!-- User Profile Dropdown -->
                <ProfileDropdown/>
            </div>
        </nav>

        <!-- Main Workspace Container -->
        <main class="max-w-4xl mx-auto px-4 py-10 sm:px-6">
            
            <!-- Hero Welcome Card -->
            <div class="bg-gradient-to-br from-indigo-900 via-indigo-950 to-slate-900 rounded-3xl p-6 sm:p-10 text-white shadow-xl mb-10 relative overflow-hidden">
                <div class="absolute -right-10 -bottom-10 w-40 h-40 bg-indigo-500/10 rounded-full blur-2xl"></div>
                <div class="absolute -left-10 -top-10 w-40 h-40 bg-purple-500/10 rounded-full blur-2xl"></div>

                <div class="max-w-2xl relative z-10">
                    <span class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium bg-indigo-500/20 text-indigo-300 border border-indigo-500/30 mb-4">
                        ⚡ Setup Complete
                    </span>
                    <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight mb-3">
                        Welcome to the Laravel Jetstream Starter Kit!
                    </h1>
                    <p class="text-indigo-200 text-base sm:text-lg leading-relaxed">
                        Hello {{ auth?.user?.name?.split(' ')[0] || 'Developer' }}! 👋 We're thrilled to have you here. Let's get your production-ready Laravel jetstream application perfectly optimized, built, and launched.
                    </p>
                </div>
            </div>

            <!-- Content Section: Action Items -->
            <div class="mb-10">
                <h2 class="text-xl font-bold text-slate-900 mb-5 flex items-center">
                    <span class="w-1.5 h-5 bg-indigo-600 rounded-sm mr-3"></span>
                    Your First Steps
                </h2>

                <div class="space-y-4">
                    <!-- Step 1: Initialize -->
                    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-start space-x-4">
                            <div class="p-3 bg-amber-50 text-amber-600 rounded-xl shrink-0 border border-amber-100">
                                🚀
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-800 text-base">Initialize Your Jetstream Project</h3>
                                <p class="text-sm text-slate-500 mt-1 max-w-xl">
                                    Finalize setup by verifying stack features or execution configurations. Run <code class="bg-slate-100 px-1.5 py-0.5 rounded text-rose-600 font-mono text-xs">php artisan jetstream:install</code> if missing.
                                </p>
                            </div>
                        </div>
                        <button 
                            @click="copyCommand"
                            class="px-4 py-2 bg-slate-900 hover:bg-slate-800 text-white text-xs font-semibold rounded-xl tracking-wide shadow-sm shrink-0 transition"
                        >
                            {{ isCopied ? 'Copied! ✓' : 'Run Command' }}
                        </button>
                    </div>

                    <!-- Step 2: Configure Auth -->
                    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-start space-x-4">
                            <div class="p-3 bg-indigo-50 text-indigo-600 rounded-xl shrink-0 border border-indigo-100">
                                ⚙️
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-800 text-base">Configure Authentication & Teams</h3>
                                <p class="text-sm text-slate-500 mt-1 max-w-xl">
                                    Review options inside the backend layout profile configuration files located at <code class="bg-slate-100 px-1.5 py-0.5 rounded text-indigo-600 font-mono text-xs">config/jetstream.php</code> to manage API/Teams.
                                </p>
                            </div>
                        </div>
                        <Link 
                            :href="route('profile.show')"
                            class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl tracking-wide border border-slate-200 shrink-0 text-center transition"
                        >
                            Review Config
                        </Link>
                    </div>

                    <!-- Step 3: Documentation -->
                    <div class="bg-white p-5 rounded-2xl border border-slate-200/80 shadow-sm hover:shadow-md transition flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                        <div class="flex items-start space-x-4">
                            <div class="p-3 bg-emerald-50 text-emerald-600 rounded-xl shrink-0 border border-emerald-100">
                                💡
                            </div>
                            <div>
                                <h3 class="font-bold text-slate-800 text-base">Explore Your Starter Resources</h3>
                                <p class="text-sm text-slate-500 mt-1 max-w-xl">
                                    Read the official Laravel Jetstream reference manual to harness absolute control over security scaffolding layers, Livewire/Inertia options, and team management tools.
                                </p>
                            </div>
                        </div>
                        <a 
                            href="https://laravel.com" 
                            target="_blank"
                            class="px-4 py-2 bg-slate-100 hover:bg-slate-200 text-slate-700 text-xs font-semibold rounded-xl tracking-wide border border-slate-200 shrink-0 text-center transition"
                        >
                            Read Docs
                        </a>
                    </div>
                </div>
            </div>

            <!-- Footer Essential Links Grid -->
            <div class="border-t border-slate-200 pt-8">
                <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-6">
                    <div>
                        <h4 class="text-sm font-bold text-slate-900 tracking-wide uppercase mb-3">Essential Links</h4>
                        <div class="flex flex-wrap gap-4 text-xs font-medium text-slate-500">
                            <a href="https://laravel.com" target="_blank" class="hover:text-indigo-600 transition flex items-center gap-1">📖 Documentation</a>
                            <a href="https://github.com" target="_blank" class="hover:text-indigo-600 transition flex items-center gap-1">🐙 GitHub Repo</a>
                            <a href="https://laravel.com" target="_blank" class="hover:text-indigo-600 transition flex items-center gap-1">📦 Laravel Ecosystem</a>
                            <a href="https://tailwindcss.com" target="_blank" class="hover:text-indigo-600 transition flex items-center gap-1">🎨 Tailwind CSS</a>
                        </div>
                    </div>
                                        <!-- Continuation from Essential Links Container -->
                    <div class="bg-indigo-50 border border-indigo-100 p-4 rounded-2xl flex items-center justify-between gap-4 max-w-md w-full md:w-auto shrink-0">
                        <div class="text-xs">
                            <p class="font-bold text-indigo-900">Need Help?</p>
                            <p class="text-indigo-700 mt-0.5">Join the Jetstream Discussions.</p>
                        </div>
                        <a 
                            href="https://github.com" 
                            target="_blank"
                            class="bg-indigo-600 hover:bg-indigo-700 text-white font-semibold text-xs py-2 px-3 rounded-xl shadow-sm transition whitespace-nowrap"
                        >
                            Contact Support
                        </a>
                    </div>
                </div>
            </div>

        </main>
    </div>
</template>

                    
