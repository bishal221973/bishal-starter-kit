<script setup>
import { computed, onMounted, ref } from "vue";
import axios from "axios";
import { usePage } from "@inertiajs/vue3";

const page = usePage();

/*
|--------------------------------------------------------------------------
| Props / User
|--------------------------------------------------------------------------
*/

const user = computed(() => {
    return page.props.auth?.user ?? {};
});

/*
|--------------------------------------------------------------------------
| 2FA Status
|--------------------------------------------------------------------------
*/

const enabled = computed(() => {
    return !!user.value?.two_factor_confirmed_at;
});

const canManageTwoFactor = computed(() => {
    return (
        page.props.registration?.two_factor === true &&
        page.props.jetstream?.canManageTwoFactorAuthentication === true
    );
});

/*
|--------------------------------------------------------------------------
| Setup
|--------------------------------------------------------------------------
*/

const pendingSetup = ref(false);

const qrCode = ref(null);

const confirmationCode = ref("");

const recoveryCodes = ref([]);

/*
|--------------------------------------------------------------------------
| Loading
|--------------------------------------------------------------------------
*/

const loading = ref(false);

const confirming = ref(false);

const regenerating = ref(false);

const disabling = ref(false);

const recoveryLoading = ref(false);

/*
|--------------------------------------------------------------------------
| Messages
|--------------------------------------------------------------------------
*/

const error = ref("");

const success = ref("");

/*
|--------------------------------------------------------------------------
| Password Confirmation
|--------------------------------------------------------------------------
*/

const showPasswordModal = ref(false);

const password = ref("");

const passwordError = ref("");

const passwordConfirmLoading = ref(false);

const passwordConfirmAction = ref(null);

/*
|--------------------------------------------------------------------------
| Clear Messages
|--------------------------------------------------------------------------
*/

const clearMessages = () => {
    error.value = "";
    success.value = "";
};

/*
|--------------------------------------------------------------------------
| Password Confirmation
|--------------------------------------------------------------------------
*/

const requirePasswordConfirmation = (action) => {
    clearMessages();

    password.value = "";

    passwordError.value = "";

    passwordConfirmAction.value = action;

    showPasswordModal.value = true;
};

/*
|--------------------------------------------------------------------------
| Confirm Password
|--------------------------------------------------------------------------
*/

const confirmPassword = async () => {
    if (!password.value) {
        passwordError.value = "Please enter your password.";

        return;
    }

    passwordConfirmLoading.value = true;

    passwordError.value = "";

    try {
        await axios.post(
            "/user/confirm-password",
            {
                password: password.value,
            }
        );

        showPasswordModal.value = false;

        password.value = "";

        /*
        |--------------------------------------------------------------------------
        | Execute requested action
        |--------------------------------------------------------------------------
        */

        if (passwordConfirmAction.value) {
            await passwordConfirmAction.value();
        }

        passwordConfirmAction.value = null;
    } catch (e) {
        passwordError.value =
            e.response?.data?.message ??
            "The password is incorrect.";
    } finally {
        passwordConfirmLoading.value = false;
    }
};

/*
|--------------------------------------------------------------------------
| Load QR Code
|--------------------------------------------------------------------------
*/

const loadQrCode = async () => {
    try {
        const response = await axios.get(
            "/user/two-factor-qr-code"
        );

        qrCode.value =
            response.data?.svg ??
            response.data?.qr_code ??
            null;
    } catch (e) {
        console.error("QR Code Error:", e);

        error.value =
            e.response?.data?.message ??
            "Unable to load QR code.";
    }
};

/*
|--------------------------------------------------------------------------
| Load Recovery Codes
|--------------------------------------------------------------------------
*/

const loadRecoveryCodes = async () => {
    recoveryLoading.value = true;

    try {
        const response = await axios.get(
            "/user/two-factor-recovery-codes"
        );

        recoveryCodes.value =
            response.data ?? [];
    } catch (e) {
        console.error(
            "Recovery Codes Error:",
            e
        );
    } finally {
        recoveryLoading.value = false;
    }
};

/*
|--------------------------------------------------------------------------
| Enable Two Factor
|--------------------------------------------------------------------------
*/

const enableTwoFactor = async () => {
    loading.value = true;

    clearMessages();

    try {
        await axios.post(
            "/user/two-factor-authentication"
        );

        pendingSetup.value = true;

        /*
        |--------------------------------------------------------------------------
        | Load QR
        |--------------------------------------------------------------------------
        */

        await loadQrCode();

        /*
        |--------------------------------------------------------------------------
        | Load recovery codes
        |--------------------------------------------------------------------------
        */

        await loadRecoveryCodes();

        success.value =
            "Two-factor authentication setup has started. Scan the QR code with your authenticator app.";
    } catch (e) {
        console.error(
            "Enable 2FA Error:",
            e
        );

        error.value =
            e.response?.data?.message ??
            "Unable to enable two-factor authentication.";
    } finally {
        loading.value = false;
    }
};

/*
|--------------------------------------------------------------------------
| Confirm Two Factor
|--------------------------------------------------------------------------
*/

const confirmTwoFactor = async () => {
    if (!confirmationCode.value) {
        error.value =
            "Please enter the 6-digit authentication code.";

        return;
    }

    if (
        confirmationCode.value.length !== 6
    ) {
        error.value =
            "Authentication code must contain 6 digits.";

        return;
    }

    confirming.value = true;

    clearMessages();

    try {
        await axios.post(
            "/user/confirmed-two-factor-authentication",
            {
                code: confirmationCode.value,
            }
        );

        confirmationCode.value = "";

        pendingSetup.value = false;

        success.value =
            "Two-factor authentication has been enabled successfully.";

        /*
        |--------------------------------------------------------------------------
        | Reload page props
        |--------------------------------------------------------------------------
        */

        page.props.auth.user.two_factor_confirmed_at =
            new Date().toISOString();

        await loadRecoveryCodes();
    } catch (e) {
        console.error(
            "Confirm 2FA Error:",
            e
        );

        error.value =
            e.response?.data?.message ??
            "The authentication code is invalid.";
    } finally {
        confirming.value = false;
    }
};

/*
|--------------------------------------------------------------------------
| Regenerate Recovery Codes
|--------------------------------------------------------------------------
*/

const regenerateRecoveryCodes = () => {
    requirePasswordConfirmation(
        regenerateRecoveryCodesConfirmed
    );
};

const regenerateRecoveryCodesConfirmed =
    async () => {
        regenerating.value = true;

        clearMessages();

        try {
            await axios.post(
                "/user/two-factor-recovery-codes"
            );

            await loadRecoveryCodes();

            success.value =
                "Recovery codes regenerated successfully.";
        } catch (e) {
            console.error(
                "Regenerate Codes Error:",
                e
            );

            error.value =
                e.response?.data?.message ??
                "Unable to regenerate recovery codes.";
        } finally {
            regenerating.value = false;
        }
    };

/*
|--------------------------------------------------------------------------
| Disable Two Factor
|--------------------------------------------------------------------------
*/

const disableTwoFactor = () => {
    requirePasswordConfirmation(
        disableTwoFactorConfirmed
    );
};

const disableTwoFactorConfirmed =
    async () => {
        disabling.value = true;

        clearMessages();

        try {
            await axios.delete(
                "/user/two-factor-authentication"
            );

            qrCode.value = null;

            recoveryCodes.value = [];

            pendingSetup.value = false;

            page.props.auth.user.two_factor_confirmed_at =
                null;

            success.value =
                "Two-factor authentication has been disabled successfully.";
        } catch (e) {
            console.error(
                "Disable 2FA Error:",
                e
            );

            error.value =
                e.response?.data?.message ??
                "Unable to disable two-factor authentication.";
        } finally {
            disabling.value = false;
        }
    };

/*
|--------------------------------------------------------------------------
| Cancel Password Modal
|--------------------------------------------------------------------------
*/

const closePasswordModal = () => {
    if (passwordConfirmLoading.value) {
        return;
    }

    showPasswordModal.value = false;

    password.value = "";

    passwordError.value = "";

    passwordConfirmAction.value = null;
};

/*
|--------------------------------------------------------------------------
| Initial Load
|--------------------------------------------------------------------------
*/

onMounted(async () => {
    if (!canManageTwoFactor.value) {
        return;
    }

    if (enabled.value) {
        await loadRecoveryCodes();
    }
});
</script>

<template>
    <!-- =========================================================
         MAIN CARD
    ========================================================== -->

    <div
        v-if="canManageTwoFactor"
        class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
    >
        <!-- =====================================================
             HEADER
        ====================================================== -->

        <div
            class="flex flex-col gap-4 border-b border-slate-100 px-6 py-5 sm:flex-row sm:items-center sm:justify-between"
        >
            <div class="flex items-center gap-4">
                <div
                    class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600"
                >
                    <i
                        class="fa-solid fa-shield-halved text-lg"
                    ></i>
                </div>

                <div>
                    <h2
                        class="font-semibold text-slate-900"
                    >
                        Two-Factor Authentication
                    </h2>

                    <p
                        class="mt-1 text-sm text-slate-500"
                    >
                        Add an extra layer of security to your
                        account.
                    </p>
                </div>
            </div>

            <!-- Status -->

            <span
                v-if="enabled"
                class="inline-flex w-fit items-center gap-2 rounded-full bg-emerald-100 px-3 py-1.5 text-xs font-semibold text-emerald-700"
            >
                <span
                    class="h-2 w-2 rounded-full bg-emerald-500"
                ></span>

                Enabled
            </span>

            <span
                v-else
                class="inline-flex w-fit items-center gap-2 rounded-full bg-slate-100 px-3 py-1.5 text-xs font-semibold text-slate-600"
            >
                <span
                    class="h-2 w-2 rounded-full bg-slate-400"
                ></span>

                Disabled
            </span>
        </div>

        <!-- =====================================================
             BODY
        ====================================================== -->

        <div class="p-6">
            <!-- =================================================
                 SUCCESS
            ================================================== -->

            <div
                v-if="success"
                class="mb-5 flex items-start gap-3 rounded-xl border border-emerald-200 bg-emerald-50 px-4 py-3 text-sm text-emerald-700"
            >
                <i
                    class="fa-solid fa-circle-check mt-0.5"
                ></i>

                <span>{{ success }}</span>
            </div>

            <!-- =================================================
                 ERROR
            ================================================== -->

            <div
                v-if="error"
                class="mb-5 flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"
            >
                <i
                    class="fa-solid fa-circle-exclamation mt-0.5"
                ></i>

                <span>{{ error }}</span>
            </div>

            <!-- =================================================
                 DISABLED
            ================================================== -->

            <div
                v-if="
                    !enabled &&
                    !pendingSetup
                "
                class="rounded-xl border border-slate-200 bg-slate-50 p-5"
            >
                <div class="flex gap-4">
                    <div
                        class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-white text-slate-500 shadow-sm"
                    >
                        <i
                            class="fa-solid fa-mobile-screen-button"
                        ></i>
                    </div>

                    <div>
                        <h3
                            class="font-semibold text-slate-800"
                        >
                            Protect your account
                        </h3>

                        <p
                            class="mt-1 text-sm leading-6 text-slate-500"
                        >
                            Two-factor authentication adds an
                            additional security step when signing
                            in. Use Google Authenticator,
                            Microsoft Authenticator, Authy, or
                            another compatible authenticator app.
                        </p>
                    </div>
                </div>

                <button
                    type="button"
                    @click="
                        requirePasswordConfirmation(
                            enableTwoFactor
                        )
                    "
                    :disabled="loading"
                    class="mt-5 inline-flex items-center gap-2 rounded-lg bg-slate-900 px-4 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-50"
                >
                    <i
                        v-if="loading"
                        class="fa-solid fa-spinner fa-spin"
                    ></i>

                    <i
                        v-else
                        class="fa-solid fa-shield-halved"
                    ></i>

                    {{
                        loading
                            ? "Setting up..."
                            : "Enable Two-Factor Authentication"
                    }}
                </button>
            </div>

            <!-- =================================================
                 SETUP
            ================================================== -->

            <div v-if="pendingSetup">
                <div
                    class="mb-6 rounded-xl border border-blue-200 bg-blue-50 p-4"
                >
                    <div class="flex gap-3">
                        <i
                            class="fa-solid fa-circle-info mt-0.5 text-blue-600"
                        ></i>

                        <div>
                            <h3
                                class="font-semibold text-blue-800"
                            >
                                Complete two-factor setup
                            </h3>

                            <p
                                class="mt-1 text-sm leading-6 text-blue-700"
                            >
                                Scan the QR code using your
                                authenticator app and then enter
                                the 6-digit verification code.
                            </p>
                        </div>
                    </div>
                </div>

                <div
                    class="grid grid-cols-1 gap-6 lg:grid-cols-2"
                >
                    <!-- QR CODE -->

                    <div
                        class="rounded-xl border border-slate-200 p-5"
                    >
                        <div class="flex items-center gap-3">
                            <div
                                class="flex h-9 w-9 items-center justify-center rounded-lg bg-slate-100 text-slate-600"
                            >
                                1
                            </div>

                            <div>
                                <h3
                                    class="font-semibold text-slate-800"
                                >
                                    Scan QR Code
                                </h3>

                                <p
                                    class="text-sm text-slate-500"
                                >
                                    Scan using your authenticator
                                    app.
                                </p>
                            </div>
                        </div>

                        <div
                            v-if="qrCode"
                            class="mt-6 flex justify-center rounded-xl border border-slate-100 bg-white p-5"
                            v-html="qrCode"
                        ></div>

                        <div
                            v-else
                            class="mt-6 flex h-56 items-center justify-center rounded-xl bg-slate-50"
                        >
                            <div class="text-center">
                                <i
                                    class="fa-solid fa-spinner fa-spin text-xl text-slate-400"
                                ></i>

                                <p
                                    class="mt-2 text-sm text-slate-500"
                                >
                                    Loading QR code...
                                </p>
                            </div>
                        </div>
                    </div>

                    <!-- CONFIRM -->

                    <div
                        class="rounded-xl border border-slate-200 p-5"
                    >
                        <div class="flex items-center gap-3">
                            <div
                                class="flex h-9 w-9 items-center justify-center rounded-lg bg-slate-100 text-slate-600"
                            >
                                2
                            </div>

                            <div>
                                <h3
                                    class="font-semibold text-slate-800"
                                >
                                    Verify Code
                                </h3>

                                <p
                                    class="text-sm text-slate-500"
                                >
                                    Enter the 6-digit code.
                                </p>
                            </div>
                        </div>

                        <input
                            v-model="confirmationCode"
                            type="text"
                            inputmode="numeric"
                            autocomplete="one-time-code"
                            maxlength="6"
                            placeholder="000000"
                            @input="
                                confirmationCode =
                                    confirmationCode
                                        .replace(/\D/g, '')
                                        .slice(0, 6)
                            "
                            class="mt-6 w-full rounded-xl border border-slate-300 px-4 py-4 text-center text-2xl font-bold tracking-[0.5em] outline-none transition focus:border-slate-500 focus:ring-2 focus:ring-slate-200"
                        />

                        <button
                            type="button"
                            @click="confirmTwoFactor"
                            :disabled="
                                confirming ||
                                confirmationCode.length !== 6
                            "
                            class="mt-4 w-full rounded-xl bg-slate-900 px-4 py-3 text-sm font-semibold text-white transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            <i
                                v-if="confirming"
                                class="fa-solid fa-spinner fa-spin mr-2"
                            ></i>

                            {{
                                confirming
                                    ? "Verifying..."
                                    : "Confirm & Enable"
                            }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- =================================================
                 ENABLED
            ================================================== -->

            <div v-if="enabled">
                <!-- Security status -->

                <div
                    class="rounded-xl border border-emerald-200 bg-emerald-50 p-5"
                >
                    <div class="flex gap-4">
                        <div
                            class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-emerald-100 text-emerald-600"
                        >
                            <i
                                class="fa-solid fa-shield-halved"
                            ></i>
                        </div>

                        <div>
                            <h3
                                class="font-semibold text-emerald-800"
                            >
                                Two-factor authentication is
                                enabled
                            </h3>

                            <p
                                class="mt-1 text-sm leading-6 text-emerald-700"
                            >
                                Your account requires an
                                authentication code when signing
                                in.
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Recovery Codes -->

                <div
                    class="mt-6 rounded-xl border border-slate-200 p-5"
                >
                    <div
                        class="flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between"
                    >
                        <div>
                            <h3
                                class="font-semibold text-slate-800"
                            >
                                Recovery Codes
                            </h3>

                            <p
                                class="mt-1 text-sm leading-6 text-slate-500"
                            >
                                Keep these codes somewhere safe.
                                You can use one if you lose access
                                to your authenticator.
                            </p>
                        </div>

                        <button
                            type="button"
                            @click="
                                requirePasswordConfirmation(
                                    regenerateRecoveryCodesConfirmed
                                )
                            "
                            :disabled="regenerating"
                            class="inline-flex items-center justify-center gap-2 rounded-lg border border-slate-300 bg-white px-3 py-2 text-sm font-medium text-slate-700 transition hover:bg-slate-50 disabled:opacity-50"
                        >
                            <i
                                v-if="regenerating"
                                class="fa-solid fa-spinner fa-spin"
                            ></i>

                            <i
                                v-else
                                class="fa-solid fa-rotate"
                            ></i>

                            Regenerate
                        </button>
                    </div>

                    <div
                        v-if="
                            recoveryLoading
                        "
                        class="mt-5 flex justify-center py-6"
                    >
                        <i
                            class="fa-solid fa-spinner fa-spin text-slate-400"
                        ></i>
                    </div>

                    <div
                        v-else-if="
                            recoveryCodes.length
                        "
                        class="mt-5 grid grid-cols-1 gap-2 sm:grid-cols-2"
                    >
                        <code
                            v-for="code in recoveryCodes"
                            :key="code"
                            class="rounded-lg bg-slate-100 px-3 py-2.5 text-center font-mono text-sm text-slate-700"
                        >
                            {{ code }}
                        </code>
                    </div>

                    <div
                        v-else
                        class="mt-5 rounded-lg bg-slate-50 p-4 text-center text-sm text-slate-500"
                    >
                        No recovery codes available.
                    </div>
                </div>

                <!-- Disable -->

                <div
                    class="mt-6 flex flex-col gap-4 rounded-xl border border-red-200 bg-red-50 p-5 sm:flex-row sm:items-center sm:justify-between"
                >
                    <div>
                        <h3
                            class="font-semibold text-red-800"
                        >
                            Disable Two-Factor Authentication
                        </h3>

                        <p
                            class="mt-1 text-sm text-red-600"
                        >
                            Disabling 2FA will remove this
                            additional security layer from your
                            account.
                        </p>
                    </div>

                    <button
                        type="button"
                        @click="disableTwoFactor"
                        :disabled="disabling"
                        class="inline-flex items-center justify-center gap-2 rounded-lg border border-red-300 bg-white px-4 py-2.5 text-sm font-semibold text-red-600 transition hover:bg-red-100 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        <i
                            v-if="disabling"
                            class="fa-solid fa-spinner fa-spin"
                        ></i>

                        <i
                            v-else
                            class="fa-solid fa-shield-halved"
                        ></i>

                        Disable
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- =========================================================
         FEATURE DISABLED
    ========================================================== -->

    <div
        v-else
        class="rounded-2xl border border-slate-200 bg-white p-8 text-center shadow-sm"
    >
        <div
            class="mx-auto flex h-14 w-14 items-center justify-center rounded-2xl bg-slate-100 text-slate-400"
        >
            <i
                class="fa-solid fa-shield-halved text-xl"
            ></i>
        </div>

        <h3
            class="mt-4 font-semibold text-slate-800"
        >
            Two-Factor Authentication Unavailable
        </h3>

        <p
            class="mx-auto mt-1 max-w-md text-sm text-slate-500"
        >
            Two-factor authentication is currently disabled
            for your application.
        </p>
    </div>

    <!-- =========================================================
         PASSWORD CONFIRMATION MODAL
    ========================================================== -->

    <Teleport to="body">
        <div
            v-if="showPasswordModal"
            class="fixed inset-0 z-[999999] flex items-center justify-center bg-slate-900/50 p-4 backdrop-blur-sm"
            @click.self="
                !passwordConfirmLoading &&
                closePasswordModal()
            "
        >
            <div
                class="w-full max-w-md overflow-hidden rounded-2xl bg-white shadow-2xl"
            >
                <!-- Modal Header -->

                <div
                    class="flex items-center justify-between border-b border-slate-100 px-6 py-5"
                >
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-amber-100 text-amber-600"
                        >
                            <i
                                class="fa-solid fa-lock"
                            ></i>
                        </div>

                        <div>
                            <h2
                                class="font-semibold text-slate-900"
                            >
                                Confirm Password
                            </h2>

                            <p
                                class="mt-1 text-xs text-slate-500"
                            >
                                Password confirmation required.
                            </p>
                        </div>
                    </div>

                    <button
                        type="button"
                        :disabled="
                            passwordConfirmLoading
                        "
                        @click="
                            closePasswordModal
                        "
                        class="flex h-9 w-9 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-700 disabled:opacity-50"
                    >
                        <i
                            class="fa-solid fa-xmark"
                        ></i>
                    </button>
                </div>

                <!-- Modal Body -->

                <form
                    @submit.prevent="confirmPassword"
                    class="p-6"
                >
                    <p
                        class="mb-5 text-sm leading-6 text-slate-500"
                    >
                        For your security, please confirm your
                        current password before changing your
                        two-factor authentication settings.
                    </p>

                    <div>
                        <label
                            class="text-sm font-medium text-slate-700"
                        >
                            Current Password
                            <span
                                class="text-red-500"
                            >
                                *
                            </span>
                        </label>

                        <input
                            v-model="password"
                            type="password"
                            autocomplete="current-password"
                            autofocus
                            placeholder="Enter your current password"
                            class="mt-1.5 w-full rounded-lg border border-slate-300 px-3 py-3 text-sm outline-none transition focus:border-slate-500 focus:ring-2 focus:ring-slate-200"
                        />

                        <p
                            v-if="passwordError"
                            class="mt-2 text-sm text-red-500"
                        >
                            <i
                                class="fa-solid fa-circle-exclamation mr-1"
                            ></i>

                            {{ passwordError }}
                        </p>
                    </div>

                    <!-- Modal Footer -->

                    <div
                        class="mt-6 flex items-center justify-end gap-3"
                    >
                        <button
                            type="button"
                            :disabled="
                                passwordConfirmLoading
                            "
                            @click="
                                closePasswordModal
                            "
                            class="rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50 disabled:opacity-50"
                        >
                            Cancel
                        </button>

                        <button
                            type="submit"
                            :disabled="
                                passwordConfirmLoading ||
                                !password
                            "
                            class="inline-flex items-center gap-2 rounded-lg bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            <i
                                v-if="
                                    passwordConfirmLoading
                                "
                                class="fa-solid fa-spinner fa-spin"
                            ></i>

                            <i
                                v-else
                                class="fa-solid fa-check"
                            ></i>

                            {{
                                passwordConfirmLoading
                                    ? "Confirming..."
                                    : "Confirm Password"
                            }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </Teleport>
</template>

<style scoped>
input[type="password"]::-ms-reveal,
input[type="password"]::-ms-clear {
    display: none;
}
</style>