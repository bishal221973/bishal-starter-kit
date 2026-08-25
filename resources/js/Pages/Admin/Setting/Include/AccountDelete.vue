<script setup>
import { computed, ref } from "vue";
import { router, usePage } from "@inertiajs/vue3";
import axios from "axios";

const page = usePage();

/*
|--------------------------------------------------------------------------
| User
|--------------------------------------------------------------------------
*/

const user = computed(() => {
    return page.props.auth?.user ?? {};
});

/*
|--------------------------------------------------------------------------
| Feature Availability
|--------------------------------------------------------------------------
*/

const canDeleteAccount = computed(() => {
    return (
        page.props.jetstream?.hasAccountDeletionFeatures === true
    );
});

/*
|--------------------------------------------------------------------------
| State
|--------------------------------------------------------------------------
*/

const showPasswordModal = ref(false);

const password = ref("");

const passwordError = ref("");

const error = ref("");

const passwordConfirmLoading = ref(false);

const deleting = ref(false);

/*
|--------------------------------------------------------------------------
| Open Delete Modal
|--------------------------------------------------------------------------
*/

const openDeleteModal = () => {
    error.value = "";
    passwordError.value = "";
    password.value = "";

    showPasswordModal.value = true;
};

/*
|--------------------------------------------------------------------------
| Close Modal
|--------------------------------------------------------------------------
*/

const closeDeleteModal = () => {
    if (
        passwordConfirmLoading.value ||
        deleting.value
    ) {
        return;
    }

    showPasswordModal.value = false;

    password.value = "";

    passwordError.value = "";
    error.value = "";
};

/*
|--------------------------------------------------------------------------
| Confirm Password
|--------------------------------------------------------------------------
*/

const confirmPassword = async () => {
    if (!password.value) {
        passwordError.value =
            "Please enter your current password.";

        return;
    }

    passwordConfirmLoading.value = true;

    passwordError.value = "";
    error.value = "";

    try {
        /*
        |--------------------------------------------------------------------------
        | Confirm current password
        |--------------------------------------------------------------------------
        */

        await axios.post(
            "/user/confirm-password",
            {
                password: password.value,
            }
        );

        /*
        |--------------------------------------------------------------------------
        | Password confirmed
        |--------------------------------------------------------------------------
        */

        passwordConfirmLoading.value = false;

        showPasswordModal.value = false;

        await deleteAccount();

    } catch (e) {
        passwordError.value =
            e.response?.data?.message ??
            "The password is incorrect.";

        passwordConfirmLoading.value = false;
    }
};

/*
|--------------------------------------------------------------------------
| Delete Account
|--------------------------------------------------------------------------
*/

const deleteAccount = async () => {
    deleting.value = true;

    error.value = "";

    try {
        /*
        |--------------------------------------------------------------------------
        | Laravel Jetstream delete account endpoint
        |--------------------------------------------------------------------------
        */

        router.delete(
            route("current-user.destroy"),
            {
                preserveScroll: false,

                onError: (errors) => {
                    console.error(
                        "Delete Account Error:",
                        errors
                    );

                    error.value =
                        errors?.message ??
                        "Unable to delete your account.";
                },

                onFinish: () => {
                    deleting.value = false;
                },
            }
        );
    } catch (e) {
        console.error(
            "Delete Account Error:",
            e
        );

        error.value =
            e.response?.data?.message ??
            "Unable to delete your account.";

        deleting.value = false;
    }
};
</script>

<template>
    <!-- =========================================================
         MAIN CARD
    ========================================================== -->

    <div
        v-if="canDeleteAccount"
        class="overflow-hidden rounded-2xl border border-red-200 bg-white shadow-sm"
    >
        <!-- =====================================================
             HEADER
        ====================================================== -->

        <div
            class="flex flex-col gap-4 border-b border-red-100 px-6 py-5 sm:flex-row sm:items-center sm:justify-between"
        >
            <div class="flex items-center gap-4">
                <div
                    class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-red-100 text-red-600"
                >
                    <i
                        class="fa-solid fa-user-xmark text-lg"
                    ></i>
                </div>

                <div>
                    <h2
                        class="font-semibold text-slate-900"
                    >
                        Delete Account
                    </h2>

                    <p
                        class="mt-1 text-sm text-slate-500"
                    >
                        Permanently delete your account and
                        associated data.
                    </p>
                </div>
            </div>

            <span
                class="inline-flex w-fit items-center gap-2 rounded-full bg-red-100 px-3 py-1.5 text-xs font-semibold text-red-700"
            >
                <span
                    class="h-2 w-2 rounded-full bg-red-500"
                ></span>

                Danger Zone
            </span>
        </div>

        <!-- =====================================================
             BODY
        ====================================================== -->

        <div class="p-6">
            <!-- Error -->

            <div
                v-if="error"
                class="mb-5 flex items-start gap-3 rounded-xl border border-red-200 bg-red-50 px-4 py-3 text-sm text-red-700"
            >
                <i
                    class="fa-solid fa-circle-exclamation mt-0.5"
                ></i>

                <span>{{ error }}</span>
            </div>

            <!-- Warning -->

            <div
                class="rounded-xl border border-red-200 bg-red-50 p-5"
            >
                <div class="flex gap-4">
                    <div
                        class="flex h-11 w-11 shrink-0 items-center justify-center rounded-xl bg-red-100 text-red-600"
                    >
                        <i
                            class="fa-solid fa-triangle-exclamation"
                        ></i>
                    </div>

                    <div>
                        <h3
                            class="font-semibold text-red-800"
                        >
                            This action cannot be undone
                        </h3>

                        <p
                            class="mt-1 text-sm leading-6 text-red-700"
                        >
                            Once your account is deleted, all
                            account information and associated
                            data may be permanently removed.
                            Please make sure you are certain before
                            continuing.
                        </p>
                    </div>
                </div>

                <!-- User Information -->

                <div
                    class="mt-5 rounded-lg border border-red-200 bg-white/70 p-4"
                >
                    <div
                        class="flex flex-col gap-2 text-sm sm:flex-row sm:items-center sm:justify-between"
                    >
                        <span
                            class="text-slate-500"
                        >
                            Account
                        </span>

                        <span
                            class="font-medium text-slate-800"
                        >
                            {{ user.name }}
                        </span>
                    </div>

                    <div
                        class="mt-2 flex flex-col gap-2 text-sm sm:flex-row sm:items-center sm:justify-between"
                    >
                        <span
                            class="text-slate-500"
                        >
                            Email
                        </span>

                        <span
                            class="font-medium text-slate-800"
                        >
                            {{ user.email }}
                        </span>
                    </div>
                </div>

                <!-- Delete Button -->

                <div class="mt-5">
                    <button
                        type="button"
                        @click="openDeleteModal"
                        :disabled="deleting"
                        class="inline-flex items-center gap-2 rounded-lg bg-red-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm transition hover:bg-red-700 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        <i
                            v-if="deleting"
                            class="fa-solid fa-spinner fa-spin"
                        ></i>

                        <i
                            v-else
                            class="fa-solid fa-trash"
                        ></i>

                        {{
                            deleting
                                ? "Deleting Account..."
                                : "Delete My Account"
                        }}
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
                class="fa-solid fa-user-shield text-xl"
            ></i>
        </div>

        <h3
            class="mt-4 font-semibold text-slate-800"
        >
            Account Deletion Unavailable
        </h3>

        <p
            class="mx-auto mt-1 max-w-md text-sm text-slate-500"
        >
            Account deletion is currently disabled for this
            application.
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
                !deleting &&
                closeDeleteModal()
            "
        >
            <div
                class="w-full max-w-md overflow-hidden rounded-2xl bg-white shadow-2xl"
            >
                <!-- Header -->

                <div
                    class="flex items-center justify-between border-b border-slate-100 px-6 py-5"
                >
                    <div class="flex items-center gap-3">
                        <div
                            class="flex h-10 w-10 items-center justify-center rounded-xl bg-red-100 text-red-600"
                        >
                            <i
                                class="fa-solid fa-user-lock"
                            ></i>
                        </div>

                        <div>
                            <h2
                                class="font-semibold text-slate-900"
                            >
                                Confirm Account Deletion
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
                            passwordConfirmLoading ||
                            deleting
                        "
                        @click="closeDeleteModal"
                        class="flex h-9 w-9 items-center justify-center rounded-lg text-slate-400 transition hover:bg-slate-100 hover:text-slate-700 disabled:opacity-50"
                    >
                        <i
                            class="fa-solid fa-xmark"
                        ></i>
                    </button>
                </div>

                <!-- Body -->

                <form
                    @submit.prevent="confirmPassword"
                    class="p-6"
                >
                    <!-- Warning -->

                    <div
                        class="mb-5 rounded-xl border border-red-200 bg-red-50 p-4"
                    >
                        <div class="flex gap-3">
                            <i
                                class="fa-solid fa-triangle-exclamation mt-0.5 text-red-600"
                            ></i>

                            <p
                                class="text-sm leading-6 text-red-700"
                            >
                                You are about to permanently
                                delete your account. This action
                                cannot be undone.
                            </p>
                        </div>
                    </div>

                    <p
                        class="mb-5 text-sm leading-6 text-slate-500"
                    >
                        Enter your current password to confirm
                        that you want to delete your account.
                    </p>

                    <!-- Password -->

                    <div>
                        <label
                            class="text-sm font-medium text-slate-700"
                        >
                            Current Password
                            <span class="text-red-500">
                                *
                            </span>
                        </label>

                        <input
                            v-model="password"
                            type="password"
                            autocomplete="current-password"
                            autofocus
                            placeholder="Enter your current password"
                            class="mt-1.5 w-full rounded-lg border border-slate-300 px-3 py-3 text-sm outline-none transition focus:border-red-500 focus:ring-2 focus:ring-red-100"
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

                    <!-- Footer -->

                    <div
                        class="mt-6 flex items-center justify-end gap-3"
                    >
                        <button
                            type="button"
                            :disabled="
                                passwordConfirmLoading ||
                                deleting
                            "
                            @click="closeDeleteModal"
                            class="rounded-lg border border-slate-300 bg-white px-4 py-2.5 text-sm font-medium text-slate-700 transition hover:bg-slate-50 disabled:opacity-50"
                        >
                            Cancel
                        </button>

                        <button
                            type="submit"
                            :disabled="
                                passwordConfirmLoading ||
                                deleting ||
                                !password
                            "
                            class="inline-flex items-center gap-2 rounded-lg bg-red-600 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-red-700 disabled:cursor-not-allowed disabled:opacity-50"
                        >
                            <i
                                v-if="
                                    passwordConfirmLoading
                                "
                                class="fa-solid fa-spinner fa-spin"
                            ></i>

                            <i
                                v-else
                                class="fa-solid fa-trash"
                            ></i>

                            {{
                                passwordConfirmLoading
                                    ? "Confirming..."
                                    : "Delete Account"
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