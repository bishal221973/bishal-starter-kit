<script setup>
import { ref, computed } from "vue";
import { useForm } from "@inertiajs/vue3";

const useRecoveryCode = ref(false);

const form = useForm({
    code: "",
    recovery_code: "",
});

const submit = () => {
    form.post(route("two-factor.login"), {
        onFinish: () => {
            form.reset("code", "recovery_code");
        },
    });
};

const switchMode = () => {
    useRecoveryCode.value = !useRecoveryCode.value;

    form.clearErrors();
    form.reset("code", "recovery_code");
};

const inputLabel = computed(() =>
    useRecoveryCode.value
        ? "Recovery Code"
        : "Authentication Code"
);

const inputPlaceholder = computed(() =>
    useRecoveryCode.value
        ? "Enter your recovery code"
        : "000000"
);
</script>

<template>
    <div
        class="flex min-h-screen items-center justify-center bg-slate-50 px-4 py-10"
    >
        <div class="w-full max-w-md">
            <!-- Card -->
            <div
                class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-xl"
            >
                <!-- Header -->
                <div class="border-b border-slate-100 px-6 py-7 text-center">
                    <!-- Icon -->
                    <div
                        class="mx-auto flex h-16 w-16 items-center justify-center rounded-2xl bg-[#348797]/10 text-[#348797]"
                    >
                        <i
                            class="fa-solid fa-shield-halved text-2xl"
                        ></i>
                    </div>

                    <h1
                        class="mt-5 text-xl font-bold text-slate-900"
                    >
                        Two-Factor Authentication
                    </h1>

                    <p
                        class="mx-auto mt-2 max-w-sm text-sm leading-6 text-slate-500"
                    >
                        {{
                            useRecoveryCode
                                ? "Enter one of your recovery codes to continue."
                                : "Enter the authentication code from your authenticator app."
                        }}
                    </p>
                </div>

                <!-- Form -->
                <form
                    @submit.prevent="submit"
                    class="px-6 py-6"
                >
                    <!-- Authentication Code -->
                    <div v-if="!useRecoveryCode">
                        <label
                            class="mb-2 block text-sm font-medium text-slate-700"
                        >
                            Authentication Code
                        </label>

                        <input
                            v-model="form.code"
                            type="text"
                            inputmode="numeric"
                            autocomplete="one-time-code"
                            maxlength="6"
                            autofocus
                            placeholder="000000"
                            class="w-full rounded-xl border border-slate-300 px-4 py-3 text-center text-xl font-semibold tracking-[0.4em] text-slate-900 outline-none transition placeholder:tracking-normal placeholder:text-slate-300 focus:border-[#348797] focus:ring-2 focus:ring-[#348797]/20"
                        />

                        <p
                            v-if="form.errors.code"
                            class="mt-2 text-sm text-red-500"
                        >
                            {{ form.errors.code }}
                        </p>
                    </div>

                    <!-- Recovery Code -->
                    <div v-else>
                        <label
                            class="mb-2 block text-sm font-medium text-slate-700"
                        >
                            Recovery Code
                        </label>

                        <input
                            v-model="form.recovery_code"
                            type="text"
                            autocomplete="off"
                            autofocus
                            placeholder="Enter recovery code"
                            class="w-full rounded-xl border border-slate-300 px-4 py-3 text-sm text-slate-900 outline-none transition focus:border-[#348797] focus:ring-2 focus:ring-[#348797]/20"
                        />

                        <p
                            v-if="form.errors.recovery_code"
                            class="mt-2 text-sm text-red-500"
                        >
                            {{ form.errors.recovery_code }}
                        </p>
                    </div>

                    <!-- General Error -->
                    <div
                        v-if="form.errors.two_factor_code"
                        class="mt-3 rounded-lg bg-red-50 px-3 py-2 text-sm text-red-600"
                    >
                        {{ form.errors.two_factor_code }}
                    </div>

                    <!-- Submit -->
                    <button
                        type="submit"
                        :disabled="form.processing"
                        class="mt-6 flex w-full items-center justify-center gap-2 rounded-xl bg-[#348797] px-4 py-3 text-sm font-semibold text-white shadow-sm transition hover:brightness-110 disabled:cursor-not-allowed disabled:opacity-60"
                    >
                        <i
                            v-if="form.processing"
                            class="fa-solid fa-spinner fa-spin"
                        ></i>

                        <i
                            v-else
                            class="fa-solid fa-arrow-right"
                        ></i>

                        {{
                            form.processing
                                ? "Verifying..."
                                : "Verify & Continue"
                        }}
                    </button>

                    <!-- Switch -->
                    <button
                        type="button"
                        @click="switchMode"
                        class="mt-5 w-full text-center text-sm font-medium text-[#348797] hover:underline"
                    >
                        <template v-if="useRecoveryCode">
                            Use authentication code instead
                        </template>

                        <template v-else>
                            Use a recovery code instead
                        </template>
                    </button>
                </form>

                <!-- Security Footer -->
                <div
                    class="border-t border-slate-100 bg-slate-50 px-6 py-4"
                >
                    <div
                        class="flex items-start gap-3"
                    >
                        <i
                            class="fa-solid fa-lock mt-0.5 text-sm text-slate-400"
                        ></i>

                        <p
                            class="text-xs leading-5 text-slate-500"
                        >
                            Your account is protected with
                            two-factor authentication. Do not
                            share your authentication or recovery
                            codes with anyone.
                        </p>
                    </div>
                </div>
            </div>

            <!-- Back -->
            <div class="mt-5 text-center">
                <button
                    type="button"
                    @click="$inertia.visit(route('login'))"
                    class="text-sm text-slate-500 hover:text-slate-800"
                >
                    <i class="fa-solid fa-arrow-left mr-1"></i>
                    Back to login
                </button>
            </div>
        </div>
    </div>
</template>