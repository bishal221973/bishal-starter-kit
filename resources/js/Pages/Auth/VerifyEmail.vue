<script setup>
import { Head, router } from "@inertiajs/vue3";
import { ref, computed } from "vue";
import Logo from "@/Components/Logo/Logo.vue";

const props = defineProps({
    email: {
        type: String,
        default: "",
    },

    status: {
        type: String,
        default: "",
    },

    errors: {
        type: Object,
        default: () => ({}),
    },
});

const email = ref(props.email || "");
const processing = ref(false);
const status = ref(props.status || "");
const error = ref("");

const isValidEmail = computed(() => {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(
        email.value.trim()
    );
});

const resendVerification = () => {
    error.value = "";
    status.value = "";

    if (!email.value.trim()) {
        error.value = "Please enter your email address.";
        return;
    }

    if (!isValidEmail.value) {
        error.value = "Please enter a valid email address.";
        return;
    }

    processing.value = true;

    router.post(
        route("verification.send"),
        {
            email: email.value.trim(),
        },
        {
            preserveScroll: true,

            onSuccess: (page) => {
                status.value =
                    page.props.flash?.status ||
                    "A fresh verification link has been sent to your email address.";
            },

            onError: (errors) => {
                error.value =
                    errors.email ||
                    errors.message ||
                    "Unable to send the verification email. Please try again.";
            },

            onFinish: () => {
                processing.value = false;
            },
        }
    );
};

const logout = () => {
    router.post(route("logout"));
};
</script>

<template>
    <Head title="Verify Email" />

    <div
        class="
            min-h-screen
            flex
            items-center
            justify-center
            bg-gradient-to-br
            from-[#348797]
            via-[#286f7e]
            to-[#0f172a]
            px-5
            py-10
        "
    >
        <!-- Background -->
        <div
            class="
                pointer-events-none
                fixed
                inset-0
                overflow-hidden
            "
        >
            <div
                class="
                    absolute
                    -left-32
                    -top-32
                    h-96
                    w-96
                    rounded-full
                    bg-white/10
                    blur-3xl
                "
            ></div>

            <div
                class="
                    absolute
                    -bottom-40
                    -right-32
                    h-[30rem]
                    w-[30rem]
                    rounded-full
                    bg-cyan-300/10
                    blur-3xl
                "
            ></div>
        </div>

        <!-- Card -->
        <div
            class="
                relative
                z-10
                w-full
                max-w-md
                overflow-hidden
                rounded-3xl
                border
                border-white/20
                bg-white/95
                shadow-2xl
                backdrop-blur-xl
            "
        >
            <!-- Header -->
            <div
                class="
                    flex
                    flex-col
                    items-center
                    px-7
                    pb-6
                    pt-8
                    text-center
                    sm:px-10
                    sm:pt-10
                "
            >
                <!-- Logo -->
                <div class="mb-7">
                    <Logo />
                </div>

                <!-- Icon -->
                <div
                    class="
                        flex
                        h-20
                        w-20
                        items-center
                        justify-center
                        rounded-full
                        bg-[#348797]/10
                        text-[#348797]
                    "
                >
                    <i class="fas fa-envelope text-3xl"></i>
                </div>

                <h1
                    class="
                        mt-6
                        text-2xl
                        font-bold
                        tracking-tight
                        text-gray-900
                        sm:text-3xl
                    "
                >
                    Verify your email
                </h1>

                <p
                    class="
                        mt-3
                        max-w-sm
                        text-sm
                        leading-6
                        text-gray-500
                    "
                >
                    Enter your email address and we'll send you
                    a verification link.
                </p>
            </div>

            <!-- Content -->
            <div class="px-7 pb-8 sm:px-10">

                <!-- Success -->
                <Transition name="fade">
                    <div
                        v-if="status"
                        class="
                            mb-5
                            flex
                            items-start
                            gap-3
                            rounded-2xl
                            border
                            border-emerald-200
                            bg-emerald-50
                            px-4
                            py-3
                            text-sm
                            text-emerald-700
                        "
                    >
                        <i
                            class="
                                fas
                                fa-check-circle
                                mt-0.5
                            "
                        ></i>

                        <span>{{ status }}</span>
                    </div>
                </Transition>

                <!-- Error -->
                <Transition name="fade">
                    <div
                        v-if="error"
                        class="
                            mb-5
                            flex
                            items-start
                            gap-3
                            rounded-2xl
                            border
                            border-red-200
                            bg-red-50
                            px-4
                            py-3
                            text-sm
                            text-red-700
                        "
                    >
                        <i
                            class="
                                fas
                                fa-exclamation-circle
                                mt-0.5
                            "
                        ></i>

                        <span>{{ error }}</span>
                    </div>
                </Transition>

                <!-- Email -->
                <div>
                    <label
                        for="email"
                        class="
                            mb-2
                            block
                            text-sm
                            font-semibold
                            text-gray-700
                        "
                    >
                        Email Address
                    </label>

                    <div class="relative">
                        <!-- Icon -->
                        <div
                            class="
                                pointer-events-none
                                absolute
                                inset-y-0
                                left-0
                                flex
                                items-center
                                pl-4
                                text-gray-400
                            "
                        >
                            <i class="fas fa-envelope"></i>
                        </div>

                        <input
                            id="email"
                            v-model="email"
                            type="email"
                            autocomplete="email"
                            placeholder="Enter your email address"
                            class="
                                w-full
                                rounded-xl
                                border
                                border-gray-200
                                bg-gray-50
                                py-3
                                pl-11
                                pr-4
                                text-sm
                                text-gray-900
                                outline-none
                                transition
                                placeholder:text-gray-400
                                focus:border-[#348797]
                                focus:bg-white
                                focus:ring-4
                                focus:ring-[#348797]/10
                            "
                            @keyup.enter="resendVerification"
                        />
                    </div>
                </div>

                <!-- Email information -->
                <div
                    class="
                        mt-4
                        flex
                        items-start
                        gap-3
                        rounded-2xl
                        border
                        border-gray-100
                        bg-gray-50
                        p-4
                    "
                >
                    <div
                        class="
                            flex
                            h-9
                            w-9
                            shrink-0
                            items-center
                            justify-center
                            rounded-xl
                            bg-white
                            text-[#348797]
                            shadow-sm
                        "
                    >
                        <i class="fas fa-paper-plane"></i>
                    </div>

                    <div>
                        <p
                            class="
                                text-sm
                                font-semibold
                                text-gray-800
                            "
                        >
                            Check your inbox
                        </p>

                        <p
                            class="
                                mt-1
                                text-xs
                                leading-5
                                text-gray-500
                            "
                        >
                            We'll send a verification link to
                            the email address above. Check your
                            spam or junk folder if you don't see it.
                        </p>
                    </div>
                </div>

                <!-- Submit -->
                <button
                    type="button"
                    :disabled="processing || !isValidEmail"
                    class="
                        mt-6
                        flex
                        w-full
                        items-center
                        justify-center
                        gap-2
                        rounded-xl
                        bg-[#348797]
                        px-5
                        py-3
                        text-sm
                        font-semibold
                        text-white
                        shadow-lg
                        shadow-[#348797]/20
                        transition
                        duration-200
                        hover:brightness-110
                        focus:outline-none
                        focus:ring-2
                        focus:ring-[#348797]/40
                        disabled:cursor-not-allowed
                        disabled:opacity-50
                    "
                    @click="resendVerification"
                >
                    <i
                        v-if="processing"
                        class="fas fa-spinner fa-spin"
                    ></i>

                    <i
                        v-else
                        class="fas fa-paper-plane"
                    ></i>

                    {{
                        processing
                            ? "Sending..."
                            : "Send Verification Link"
                    }}
                </button>

                <!-- Logout -->
                <button
                    type="button"
                    class="
                        mt-4
                        flex
                        w-full
                        items-center
                        justify-center
                        gap-2
                        rounded-xl
                        px-5
                        py-3
                        text-sm
                        font-medium
                        text-gray-500
                        transition
                        hover:bg-gray-100
                        hover:text-gray-800
                    "
                    @click="logout"
                >
                    <i class="fas fa-sign-out-alt"></i>

                    Sign out
                </button>
            </div>

            <!-- Footer -->
            <div
                class="
                    border-t
                    border-gray-100
                    bg-gray-50/80
                    px-6
                    py-4
                    text-center
                "
            >
                <p
                    class="
                        text-xs
                        text-gray-400
                    "
                >
                    Already verified?

                    <span
                        class="
                            font-medium
                            text-[#348797]
                        "
                    >
                        You can continue after verification.
                    </span>
                </p>
            </div>
        </div>
    </div>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition:
        opacity 0.25s ease,
        transform 0.25s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
    transform: translateY(-5px);
}
</style>