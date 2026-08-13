<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import AuthenticationCardLogo from '@/Components/AuthenticationCardLogo.vue';
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import TextInput from '@/Components/Elements/TextInput.vue';
import Button from '@/Components/Elements/Button.vue';
import Logo from '@/Components/Logo/Logo.vue';
defineProps({
    status: String,
});

const form = useForm({
    email: '',
});

const submit = () => {
    form.post(route('password.email'));
};
</script>

<template>
    <Head title="Reset Password" />

    <!-- Full-screen background with subtle grid pattern to remove the blank feel -->
    <div class="min-h-screen flex flex-col justify-between bg-slate-50 bg-[radial-gradient(#e2e8f0_1px,transparent_1px)] [background-size:16px_16px] px-4 sm:px-6 lg:px-8 py-8">
        
        <!-- Top empty spacer for vertical balance -->
        <div></div>

        <!-- Central Card Container -->
        <div class="w-full max-w-md mx-auto bg-white border border-warning/90 shadow-xl shadow-slate-200/60 rounded-2xl overflow-hidden transition-all">
            
            <!-- Decorative top accent bar -->
            <div class="h-1.5 w-full bg-gradient-to-r from-primary via-info to-warning"></div>

            <div class="p-8 sm:p-10">
                <!-- Logo Section -->
                <div class="flex justify-center mb-6">
                    <Logo />
                </div>

                <!-- Header Text -->
                <div class="text-center mb-1">
                    <h1 class="text-2xl font-bold tracking-tight text-slate-900 mb-2">
                        Forgot password?
                    </h1>
                    <p class="text-sm text-slate-500 max-w-xs mx-auto leading-relaxed">
                        No stress. Enter your account email and we'll send you a secure link to reset it.
                    </p>
                </div>

                <!-- Success Status Toast -->
                <div v-if="status" class="mb-1 p-4 rounded-xl bg-emerald-50 border border-emerald-200 flex items-start gap-3">
                    <svg class="h-5 w-5 text-emerald-600 mt-0.5 flex-shrink-0" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    <span class="text-sm font-medium text-emerald-800">
                        {{ status }}
                    </span>
                </div>

                <!-- Form -->
                <form @submit.prevent="submit" class="space-y-2">
                    <div>
                        <InputLabel for="email" value="Email Address" class="text-xs font-semibold uppercase tracking-wider text-slate-500 mb-2 block" />
                        
                        <div class="relative rounded-xl shadow-sm">
                            <TextInput
                                id="email"
                                v-model="form.email"
                                type="email"
                                class="block w-full rounded-xl border-slate-300 bg-slate-50/50 focus:border-indigo-600 focus:ring-indigo-600/20 text-slate-900 placeholder-slate-400 py-3"
                                placeholder="name@company.com"
                                required
                                autofocus
                                autocomplete="username"
                            />
                        </div>
                        
                        <InputError class="mt-2 text-xs font-medium text-rose-500" :message="form.errors.email" />
                    </div>

                    <!-- Action Button -->
                    <div class="pt-2">
                        <Button 
                            class="w-full relative"
                            :submit="true"
                            :disabled="form.processing"
                            :processing="form.processing"
                            :text="form.processing ? 'Sending Link...' : 'Send Reset Link'"
                        />
                    </div>
                </form>

                <!-- Back to Login Link -->
                <div class="mt-6 text-center">
                    <a :href="route('login')" class="inline-flex items-center gap-2 text-sm font-medium text-slate-500 hover:text-indigo-600 transition-colors duration-200">
                        <svg class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke-width="2" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" d="M10 19l-7-7m0 0l7-7m-7 7h18" />
                        </svg>
                        Back to sign in
                    </a>
                </div>
            </div>
        </div>

        <!-- Filled Footer Content -->
        <div class="text-center text-xs text-slate-400 py-4">
            Protected by secure verification &bull; <a href="#" class="hover:underline">Need Help?</a>
        </div>

    </div>
</template>
