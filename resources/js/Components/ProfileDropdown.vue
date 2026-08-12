<script setup>
import { ref } from "vue";
import { Link, usePage } from "@inertiajs/vue3";
import { useTheme } from "@/composables/useTheme";

const showDropdown = ref(false);

const page = usePage();
const auth = page.props.auth;

const { theme } = useTheme();

const toggleDropdown = () => {
    showDropdown.value = !showDropdown.value;
};

const closeDropdown = () => {
    showDropdown.value = false;
};
</script>

<template>
    <div class="relative">
        <!-- User Button -->
        <button
            @click="toggleDropdown"
            class="flex items-center gap-3 rounded-full bg-white px-2 py-1 shadow-sm transition-all duration-300 hover:shadow-lg"
        >
            <span class="hidden sm:block text-sm font-semibold text-slate-700">
                {{ auth?.user?.name || "Guest User" }}
            </span>

            <img
                :src="auth?.user?.profile_photo_url || '/images/user.png'"
                alt="User"
                class="h-9 w-9 rounded-full object-cover ring-2"
                :style="{ '--tw-ring-color': theme.primary }"
            />
        </button>

        <!-- Overlay -->
        <div
            v-if="showDropdown"
            class="fixed inset-0 z-40"
            @click="closeDropdown"
        ></div>

        <!-- Dropdown -->
        <div
            v-if="showDropdown"
            class="absolute right-0 top-full mt-3 w-72 overflow-hidden rounded-2xl border border-slate-100 bg-white shadow-2xl z-50"
        >
            <!-- Header -->
            <div
                class="p-5 text-white"
                :style="{
                    background: `linear-gradient(135deg, ${theme.primary}, ${theme.secondary})`,
                }"
            >
                <div class="flex items-center gap-3">
                    <img
                        :src="auth?.user?.profile_photo_url || '/images/user.png'"
                        alt="User"
                        class="h-12 w-12 rounded-full border-2 border-white object-cover"
                    />

                    <div>
                        <h4 class="font-semibold">
                            {{ auth?.user?.name || "Guest User" }}
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

            <!-- Authenticated -->
            <template v-if="auth?.user">
                <div class="py-2">
                    <Link
                        href="/profile"
                        class="dropdown-item"
                    >
                        👤 <span>Profile</span>
                    </Link>

                    <Link
                        href="/dashboard"
                        class="dropdown-item"
                    >
                        📊 <span>Dashboard</span>
                    </Link>

                    <Link
                        href="/orders"
                        class="dropdown-item"
                    >
                        📦 <span>My Orders</span>
                    </Link>

                    <Link
                        href="/settings"
                        class="dropdown-item"
                    >
                        ⚙️ <span>Settings</span>
                    </Link>

                    <div class="my-2 border-t border-slate-100"></div>

                    <Link
                        href="/logout"
                        method="post"
                        as="button"
                        class="flex w-full items-center gap-3 px-5 py-3 text-red-500 hover:bg-red-50 transition"
                    >
                        🚪 <span>Logout</span>
                    </Link>
                </div>
            </template>

            <!-- Guest -->
            <template v-else>
                <div class="p-5 space-y-3">
                    <Link
                        href="/login"
                        class="block w-full rounded-xl py-3 text-center font-medium text-white transition"
                        :style="{ backgroundColor: theme.primary }"
                    >
                        Login
                    </Link>

                    <Link
                        href="/register"
                        class="block w-full rounded-xl border py-3 text-center font-medium transition"
                        :style="{
                            borderColor: theme.primary,
                            color: theme.primary,
                        }"
                    >
                        Create Account
                    </Link>
                </div>
            </template>
        </div>
    </div>
</template>

<style scoped>
.dropdown-item {
    display: flex;
    align-items: center;
    gap: 12px;
    padding: 12px 20px;
    color: #334155;
    transition: all 0.2s ease;
}

.dropdown-item:hover {
    background: rgba(61, 152, 170, 0.08);
    color: #3d98aa;
}
</style>