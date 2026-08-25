<script setup>
import { useForm } from "@inertiajs/vue3";

const form = useForm({
  current_password: "",
  password: "",
  password_confirmation: "",
});

const submit = () => {
  form.put(route("profile.password.update"), {
    preserveScroll: true,
    onSuccess: () => {
      form.reset("current_password", "password", "password_confirmation");
    },
  });
};
</script>

<template>
  <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <!-- Header -->
    <div class="border-b border-slate-100 px-6 py-5">
      <div class="flex items-center gap-3">
        <div
          class="flex h-10 w-10 items-center justify-center rounded-xl bg-slate-100 text-slate-600"
        >
          <i class="fa-solid fa-lock"></i>
        </div>

        <div>
          <h2 class="font-semibold text-slate-900">Change Password</h2>

          <p class="mt-1 text-sm text-slate-500">
            Update your password to keep your account secure.
          </p>
        </div>
      </div>
    </div>

    <!-- Form -->
    <form @submit.prevent="submit">
      <div class="grid grid-cols-1 gap-5 p-6">
        <!-- Current Password -->
        <div>
          <label for="current_password" class="text-sm font-medium text-slate-700">
            Current Password
            <span class="text-red-500">*</span>
          </label>

          <div class="relative mt-1.5">
            <i
              class="fa-solid fa-lock absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-400"
            ></i>

            <input
              id="current_password"
              v-model="form.current_password"
              type="password"
              autocomplete="current-password"
              placeholder="Enter current password"
              class="w-full rounded-lg border border-slate-300 py-2.5 pl-10 pr-3 text-sm outline-none transition focus:border-slate-500 focus:ring-1 focus:ring-slate-500"
            />
          </div>

          <p v-if="form.errors.current_password" class="mt-1 text-xs text-red-500">
            {{ form.errors.current_password }}
          </p>
        </div>

        <!-- New Password -->
        <div>
          <label for="password" class="text-sm font-medium text-slate-700">
            New Password
            <span class="text-red-500">*</span>
          </label>

          <div class="relative mt-1.5">
            <i
              class="fa-solid fa-key absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-400"
            ></i>

            <input
              id="password"
              v-model="form.password"
              type="password"
              autocomplete="new-password"
              placeholder="Enter new password"
              class="w-full rounded-lg border border-slate-300 py-2.5 pl-10 pr-3 text-sm outline-none transition focus:border-slate-500 focus:ring-1 focus:ring-slate-500"
            />
          </div>

          <p v-if="form.errors.password" class="mt-1 text-xs text-red-500">
            {{ form.errors.password }}
          </p>
        </div>

        <!-- Confirm Password -->
        <div>
          <label for="password_confirmation" class="text-sm font-medium text-slate-700">
            Confirm New Password
            <span class="text-red-500">*</span>
          </label>

          <div class="relative mt-1.5">
            <i
              class="fa-solid fa-shield-halved absolute left-3 top-1/2 -translate-y-1/2 text-sm text-slate-400"
            ></i>

            <input
              id="password_confirmation"
              v-model="form.password_confirmation"
              type="password"
              autocomplete="new-password"
              placeholder="Confirm new password"
              class="w-full rounded-lg border border-slate-300 py-2.5 pl-10 pr-3 text-sm outline-none transition focus:border-slate-500 focus:ring-1 focus:ring-slate-500"
            />
          </div>

          <p v-if="form.errors.password_confirmation" class="mt-1 text-xs text-red-500">
            {{ form.errors.password_confirmation }}
          </p>
        </div>
      </div>

      <!-- Footer -->
      <div
        class="flex flex-col gap-3 border-t border-slate-100 bg-slate-50 px-6 py-4 sm:flex-row sm:items-center sm:justify-between"
      >
        <p class="text-xs text-slate-500">
          <i class="fa-solid fa-circle-info mr-1"></i>
          Use a strong password that you don't use elsewhere.
        </p>

        <button
          type="submit"
          :disabled="form.processing"
          class="inline-flex items-center justify-center gap-2 rounded-lg bg-slate-900 px-5 py-2.5 text-sm font-semibold text-white transition hover:bg-slate-800 disabled:cursor-not-allowed disabled:opacity-50"
        >
          <i v-if="form.processing" class="fa-solid fa-spinner fa-spin text-xs"></i>

          <i v-else class="fa-solid fa-check text-xs"></i>

          {{ form.processing ? "Updating..." : "Update Password" }}
        </button>
      </div>
    </form>
  </div>
</template>
