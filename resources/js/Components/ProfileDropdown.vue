<script setup>
import { ref } from 'vue'
import { Link, usePage } from '@inertiajs/vue3'

const showDropdown = ref(false)

const page = usePage()
const auth = page.props.auth

const toggleDropdown = () => {
    showDropdown.value = !showDropdown.value
}

const closeDropdown = () => {
    showDropdown.value = false
}
</script>

<template>
    <div class="relative">
        <!-- User Button -->
        <button
            @click="toggleDropdown"
            class="flex items-center gap-3 rounded-full bg-white px-2 py-1 shadow-sm transition-all duration-300 hover:shadow-lg"
        >
            <span
                class="hidden sm:block text-sm font-semibold text-slate-700"
            >
                {{ auth?.user?.name || 'Guest User' }}
            </span>

            <img
                :src="auth?.user?.profile_photo_url || '/images/user.png'"
                alt="User"
                class="h-[35px] w-[35px] rounded-full object-cover ring-2"
                style="--tw-ring-color:#3d98aa"
            />
        </button>

        <!-- Backdrop -->
        <div
            v-if="showDropdown"
            class="fixed inset-0 z-40"
            @click="closeDropdown"
        ></div>

        <!-- Dropdown -->
        <div
            v-if="showDropdown"
            class="absolute right-0 top-full mt-3 w-72 overflow-hidden rounded-2xl bg-white shadow-2xl border border-slate-100 z-50"
        >
            <!-- Header -->
            <div
                class="p-5 text-white"
                style="background:linear-gradient(135deg,#3d98aa,#2f7f8f)"
            >
                <div class="flex items-center gap-3">
                    <img
                        :src="auth?.user?.profile_photo_url || '/images/user.png'"
                        alt="User"
                        class="h-12 w-12 rounded-full border-2 border-white object-cover"
                    />

                    <div>
                        <h4 class="font-semibold">
                            {{ auth?.user?.name || 'Guest User' }}
                        </h4>

                        <p
                            v-if="auth?.user?.email"
                            class="text-sm text-white/80"
                        >
                            {{ auth.user.email }}
                        </p>

                        <p
                            v-else
                            class="text-sm text-white/80"
                        >
                            Welcome to our website
                        </p>
                    </div>
                </div>
            </div>

            <!-- Logged In -->
            <template v-if="auth?.user">
                <div class="py-2">

                    <Link
                        href="/profile"
                        class="flex items-center gap-3 px-5 py-3 text-slate-700 transition hover:bg-[#3d98aa]/10 hover:text-[#3d98aa]"
                    >
                        <span>👤</span>
                        <span>My Profile</span>
                    </Link>

                    <Link
                        href="/dashboard"
                        class="flex items-center gap-3 px-5 py-3 text-slate-700 transition hover:bg-[#3d98aa]/10 hover:text-[#3d98aa]"
                    >
                        <span>📊</span>
                        <span>Dashboard</span>
                    </Link>

                    <Link
                        href="/orders"
                        class="flex items-center gap-3 px-5 py-3 text-slate-700 transition hover:bg-[#3d98aa]/10 hover:text-[#3d98aa]"
                    >
                        <span>📦</span>
                        <span>My Orders</span>
                    </Link>

                    <Link
                        href="/settings"
                        class="flex items-center gap-3 px-5 py-3 text-slate-700 transition hover:bg-[#3d98aa]/10 hover:text-[#3d98aa]"
                    >
                        <span>⚙️</span>
                        <span>Settings</span>
                    </Link>

                    <div class="my-2 border-t border-slate-100"></div>

                    <Link
                        href="/logout"
                        method="post"
                        as="button"
                        class="flex w-full items-center gap-3 px-5 py-3 text-red-500 transition hover:bg-red-50"
                    >
                        <span>🚪</span>
                        <span>Logout</span>
                    </Link>
                </div>
            </template>

            <!-- Guest -->
            <template v-else>
                <div class="p-5">
                    <p class="mb-4 text-sm text-slate-500">
                        Login or create an account to continue.
                    </p>

                    <div class="space-y-3">
                        <Link
                            href="/login"
                            class="block rounded-xl py-3 text-center font-medium text-white transition hover:opacity-90"
                            style="background-color:#3d98aa"
                        >
                            Login
                        </Link>

                        <Link
                            href="/register"
                            class="block rounded-xl border py-3 text-center font-medium transition"
                            style="border-color:#3d98aa;color:#3d98aa"
                        >
                            Create Account
                        </Link>
                    </div>
                </div>
            </template>
        </div>
    </div>
</template>