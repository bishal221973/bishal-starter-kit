<script setup>
import { useForm } from "@inertiajs/vue3";
import Label from "@/Components/Label.vue";
import TextInput from "@/Components/Elements/TextInput.vue";
import Button from "../Elements/Button.vue";
import Modal from "../Modal.vue";
import { ref } from "vue";

const props = defineProps({
  config: {
    type: Object,
    default: () => ({}),
  },
});

const showModal = ref(false);

const toggleModal = () => {
  showModal.value = !showModal.value;
};

/*
|--------------------------------------------------------------------------
| Form
|--------------------------------------------------------------------------
*/

const form = useForm({
  // Login Security
  enable_login_attempt_limit:
    props.config?.enable_login_attempt_limit == 1,

  max_login_attempts:
    props.config?.max_login_attempts ?? 5,

  login_lockout_duration:
    props.config?.login_lockout_duration ?? 15,

  // Auto Logout
  enable_auto_logout:
    props.config?.enable_auto_logout == 1,

  auto_logout_time:
    props.config?.auto_logout_time ?? 30,

  show_logout_warning:
    props.config?.show_logout_warning == 1,

  logout_warning_time:
    props.config?.logout_warning_time ?? 1,

  // Registration
  enable_registration:
    props.config?.enable_registration == 1,

  enable_email_verification:
    props.config?.enable_email_verification == 1,

  enable_2fa:
    props.config?.enable_2fa == 1,

  enable_multiple_branch:
    props.config?.enable_multiple_branch == 1,

  // Password Change
  force_logout_on_password_change:
    props.config?.force_logout_on_password_change == 1,

  invalidate_other_sessions:
    props.config?.invalidate_other_sessions == 1,
});

/*
|--------------------------------------------------------------------------
| Save
|--------------------------------------------------------------------------
*/

const save = () => {
  form.post(route("configuration.setting.update"), {
    preserveScroll: true,

    onSuccess: () => {
      showModal.value = false;
    },
  });
};
</script>

<template>
  <!-- =========================================================
       SETTINGS BUTTON
  ========================================================== -->

  <button
    type="button"
    @click="toggleModal"
    class="group flex items-center gap-3 w-full p-4 rounded-xl border border-slate-200 bg-white hover:border-primary hover:shadow-sm transition-all duration-200"
  >
    <!-- Icon -->
    <div
      class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary transition-all duration-200 group-hover:bg-primary group-hover:text-white"
    >
      <i class="fa-solid fa-shield-halved"></i>
    </div>

    <!-- Content -->
    <div class="text-left">
      <span class="block font-medium text-slate-800">
        Login / Security Config
      </span>

      <span class="block text-xs text-slate-500 mt-0.5">
        Manage authentication, sessions and account security
      </span>
    </div>

    <!-- Arrow -->
    <i
      class="fa-solid fa-chevron-right ml-auto text-sm text-slate-400 group-hover:text-primary transition-colors"
    ></i>
  </button>

  <!-- =========================================================
       MODAL
  ========================================================== -->

  <Modal :show="showModal">
    <div class="overflow-hidden rounded-xl bg-white">

      <!-- =====================================================
           HEADER
      ====================================================== -->

      <div
        class="px-6 py-4 border-b border-slate-100 bg-card_header_color"
      >
        <div class="flex items-center gap-3">

          <!-- Icon -->
          <div
            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary"
          >
            <i class="fa-solid fa-shield-halved"></i>
          </div>

          <!-- Header -->
          <div>
            <Label class="font-bold text-xl text-slate-800">
              Login & Security Configuration
            </Label>

            <p class="text-sm text-slate-500 mt-0.5">
              Configure login protection, session management,
              registration and authentication security.
            </p>
          </div>
        </div>
      </div>

      <!-- =====================================================
           BODY
      ====================================================== -->

      <div class="p-5 sm:p-6">

        <form @submit.prevent="save">

          <!-- =================================================
               LOGIN SECURITY
          ================================================== -->

          <div>

            <!-- Section Header -->

            <div class="flex items-center gap-2 mb-3">

              <div
                class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary/10 text-primary"
              >
                <i class="fa-solid fa-right-to-bracket text-sm"></i>
              </div>

              <div>
                <Label
                  class="font-semibold text-base text-slate-800"
                >
                  Login Security
                </Label>

                <p class="text-xs text-slate-500">
                  Protect accounts against repeated failed login
                  attempts.
                </p>
              </div>

            </div>

            <!-- Enable Login Limit -->

            <label
              for="enable_login_attempt_limit"
              class="group flex items-center justify-between gap-4 rounded-xl border p-4 cursor-pointer transition-all duration-200"
              :class="
                form.enable_login_attempt_limit
                  ? 'border-primary/30 bg-primary/5'
                  : 'border-slate-200 hover:bg-slate-50'
              "
            >

              <div class="flex items-start gap-3">

                <div
                  class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg"
                  :class="
                    form.enable_login_attempt_limit
                      ? 'bg-primary text-white'
                      : 'bg-slate-100 text-slate-500'
                  "
                >
                  <i class="fa-solid fa-lock"></i>
                </div>

                <div>

                  <Label
                    for="enable_login_attempt_limit"
                    class="font-semibold text-slate-800 cursor-pointer"
                  >
                    Enable Login Attempt Limit
                  </Label>

                  <p class="text-sm text-slate-500 mt-1">
                    Temporarily lock an account after too many
                    unsuccessful login attempts.
                  </p>

                </div>

              </div>

              <!-- Toggle -->

              <div class="relative shrink-0">

                <input
                  id="enable_login_attempt_limit"
                  v-model="form.enable_login_attempt_limit"
                  type="checkbox"
                  class="peer sr-only"
                />

                <div
                  class="w-11 h-6 rounded-full bg-slate-300 peer-checked:bg-primary transition-colors duration-200"
                ></div>

                <div
                  class="absolute left-1 top-1 w-4 h-4 rounded-full bg-white shadow-sm transition-transform duration-200 peer-checked:translate-x-5"
                ></div>

              </div>

            </label>

            <!-- Login Attempt Settings -->

            <div
              v-if="form.enable_login_attempt_limit"
              class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4"
            >

              <!-- Maximum Attempts -->

              <div
                class="rounded-xl border border-slate-200 p-4 bg-white"
              >

                <TextInput
                  v-model="form.max_login_attempts"
                  type="number"
                  min="1"
                  max="20"
                  text="Maximum Login Attempts"
                  placeholder="Enter maximum attempts"
                  required
                />

                <p class="mt-1.5 text-xs text-slate-500">
                  Number of failed attempts allowed before the
                  account is temporarily locked.
                </p>

              </div>

              <!-- Lockout Duration -->

              <div
                class="rounded-xl border border-slate-200 p-4 bg-white"
              >

                <TextInput
                  v-model="form.login_lockout_duration"
                  type="number"
                  min="1"
                  text="Lockout Duration (Minutes)"
                  placeholder="Enter lockout duration"
                  required
                />

                <p class="mt-1.5 text-xs text-slate-500">
                  How long the account remains locked after
                  reaching the maximum attempts.
                </p>

              </div>

            </div>

          </div>

          <!-- =================================================
               AUTO LOGOUT
          ================================================== -->

          <div class="mt-7">

            <div class="flex items-center gap-2 mb-3">

              <div
                class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary/10 text-primary"
              >
                <i class="fa-solid fa-arrow-right-from-bracket text-sm"></i>
              </div>

              <div>

                <Label
                  class="font-semibold text-base text-slate-800"
                >
                  Automatic Logout
                </Label>

                <p class="text-xs text-slate-500">
                  Automatically end inactive user sessions.
                </p>

              </div>

            </div>

            <!-- Enable Auto Logout -->

            <label
              for="enable_auto_logout"
              class="group flex items-center justify-between gap-4 rounded-xl border p-4 cursor-pointer transition-all duration-200"
              :class="
                form.enable_auto_logout
                  ? 'border-primary/30 bg-primary/5'
                  : 'border-slate-200 hover:bg-slate-50'
              "
            >

              <div class="flex items-start gap-3">

                <div
                  class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg"
                  :class="
                    form.enable_auto_logout
                      ? 'bg-primary text-white'
                      : 'bg-slate-100 text-slate-500'
                  "
                >
                  <i class="fa-solid fa-clock"></i>
                </div>

                <div>

                  <Label
                    for="enable_auto_logout"
                    class="font-semibold text-slate-800 cursor-pointer"
                  >
                    Enable Automatic Logout
                  </Label>

                  <p class="text-sm text-slate-500 mt-1">
                    Automatically log users out after a period of
                    inactivity.
                  </p>

                </div>

              </div>

              <!-- Toggle -->

              <div class="relative shrink-0">

                <input
                  id="enable_auto_logout"
                  v-model="form.enable_auto_logout"
                  type="checkbox"
                  class="peer sr-only"
                />

                <div
                  class="w-11 h-6 rounded-full bg-slate-300 peer-checked:bg-primary transition-colors"
                ></div>

                <div
                  class="absolute left-1 top-1 w-4 h-4 rounded-full bg-white shadow-sm transition-transform peer-checked:translate-x-5"
                ></div>

              </div>

            </label>

            <!-- Auto Logout Settings -->

            <div
              v-if="form.enable_auto_logout"
              class="grid grid-cols-1 md:grid-cols-2 gap-4 mt-4"
            >

              <!-- Logout Time -->

              <div
                class="rounded-xl border border-slate-200 p-4"
              >

                <TextInput
                  v-model="form.auto_logout_time"
                  type="number"
                  min="1"
                  text="Auto Logout Time (Minutes)"
                  placeholder="Enter logout time"
                  required
                />

                <p class="mt-1.5 text-xs text-slate-500">
                  Users will be logged out after this period of
                  inactivity.
                </p>

              </div>

              <!-- Warning -->

              <label
                class="flex items-center gap-3 rounded-xl border p-4 cursor-pointer"
                :class="
                  form.show_logout_warning
                    ? 'border-primary/30 bg-primary/5'
                    : 'border-slate-200'
                "
              >

                <input
                  v-model="form.show_logout_warning"
                  type="checkbox"
                  class="w-5 h-5 rounded cursor-pointer accent-primary"
                />

                <div>

                  <Label class="font-medium text-slate-800">
                    Show Logout Warning
                  </Label>

                  <p class="text-xs text-slate-500 mt-1">
                    Warn users before automatically logging them
                    out.
                  </p>

                </div>

              </label>

              <!-- Warning Time -->

              <div
                v-if="form.show_logout_warning"
                class="md:col-span-2 rounded-xl border border-slate-200 p-4"
              >

                <TextInput
                  v-model="form.logout_warning_time"
                  type="number"
                  min="1"
                  text="Logout Warning Time (Minutes)"
                  placeholder="Enter warning time"
                  required
                />

                <p class="mt-1.5 text-xs text-slate-500">
                  Show the warning this many minutes before the
                  automatic logout.
                </p>

              </div>

            </div>

          </div>

          <!-- =================================================
               REGISTRATION & AUTHENTICATION
          ================================================== -->

          <div class="mt-7">

            <div class="flex items-center gap-2 mb-3">

              <div
                class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary/10 text-primary"
              >
                <i class="fa-solid fa-user-plus text-sm"></i>
              </div>

              <div>

                <Label
                  class="font-semibold text-base text-slate-800"
                >
                  Registration & Authentication
                </Label>

                <p class="text-xs text-slate-500">
                  Control account registration and additional
                  authentication features.
                </p>

              </div>

            </div>

            <div
              class="grid grid-cols-1 md:grid-cols-2 gap-3"
            >

              <!-- Registration -->

              <label
                class="flex items-start gap-3 rounded-xl border p-4 cursor-pointer transition-all"
                :class="
                  form.enable_registration
                    ? 'border-primary/30 bg-primary/5'
                    : 'border-slate-200 hover:bg-slate-50'
                "
              >

                <input
                  v-model="form.enable_registration"
                  type="checkbox"
                  class="mt-0.5 w-5 h-5 rounded cursor-pointer accent-primary"
                />

                <div>

                  <Label class="font-medium text-slate-800">
                    Enable Registration
                  </Label>

                  <p class="text-xs text-slate-500 mt-1">
                    Allow new users to create accounts.
                  </p>

                </div>

              </label>

              <!-- Email Verification -->

              <label
                class="flex items-start gap-3 rounded-xl border p-4 cursor-pointer transition-all"
                :class="
                  form.enable_email_verification
                    ? 'border-primary/30 bg-primary/5'
                    : 'border-slate-200 hover:bg-slate-50'
                "
              >

                <input
                  v-model="form.enable_email_verification"
                  type="checkbox"
                  class="mt-0.5 w-5 h-5 rounded cursor-pointer accent-primary"
                />

                <div>

                  <Label class="font-medium text-slate-800">
                    Email Verification
                  </Label>

                  <p class="text-xs text-slate-500 mt-1">
                    Require users to verify their email address.
                  </p>

                </div>

              </label>

              <!-- 2FA -->

              <label
                class="flex items-start gap-3 rounded-xl border p-4 cursor-pointer transition-all"
                :class="
                  form.enable_2fa
                    ? 'border-primary/30 bg-primary/5'
                    : 'border-slate-200 hover:bg-slate-50'
                "
              >

                <input
                  v-model="form.enable_2fa"
                  type="checkbox"
                  class="mt-0.5 w-5 h-5 rounded cursor-pointer accent-primary"
                />

                <div>

                  <Label class="font-medium text-slate-800">
                    Two-Factor Authentication
                  </Label>

                  <p class="text-xs text-slate-500 mt-1">
                    Add an additional verification step during
                    login.
                  </p>

                </div>

              </label>

              <!-- Multiple Branch -->

              <label
                class="flex items-start gap-3 rounded-xl border p-4 cursor-pointer transition-all"
                :class="
                  form.enable_multiple_branch
                    ? 'border-primary/30 bg-primary/5'
                    : 'border-slate-200 hover:bg-slate-50'
                "
              >

                <input
                  v-model="form.enable_multiple_branch"
                  type="checkbox"
                  class="mt-0.5 w-5 h-5 rounded cursor-pointer accent-primary"
                />

                <div>

                  <Label class="font-medium text-slate-800">
                    Multiple Branch Access
                  </Label>

                  <p class="text-xs text-slate-500 mt-1">
                    Allow users to access multiple branches from
                    one account.
                  </p>

                </div>

              </label>

            </div>

          </div>

          <!-- =================================================
               PASSWORD CHANGE SECURITY
          ================================================== -->

          <div class="mt-7">

            <div class="flex items-center gap-2 mb-3">

              <div
                class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary/10 text-primary"
              >
                <i class="fa-solid fa-key text-sm"></i>
              </div>

              <div>

                <Label
                  class="font-semibold text-base text-slate-800"
                >
                  Password Change Security
                </Label>

                <p class="text-xs text-slate-500">
                  Control active sessions when a password is changed.
                </p>

              </div>

            </div>

            <div
              class="grid grid-cols-1 md:grid-cols-2 gap-3"
            >

              <!-- Force Logout -->

              <label
                class="flex items-start gap-3 rounded-xl border p-4 cursor-pointer transition-all"
                :class="
                  form.force_logout_on_password_change
                    ? 'border-primary/30 bg-primary/5'
                    : 'border-slate-200 hover:bg-slate-50'
                "
              >

                <input
                  v-model="form.force_logout_on_password_change"
                  type="checkbox"
                  class="mt-0.5 w-5 h-5 rounded cursor-pointer accent-primary"
                />

                <div>

                  <Label class="font-medium text-slate-800">
                    Force Logout After Password Change
                  </Label>

                  <p class="text-xs text-slate-500 mt-1">
                    Log the current user out immediately after
                    changing their password.
                  </p>

                </div>

              </label>

              <!-- Invalidate Sessions -->

              <label
                class="flex items-start gap-3 rounded-xl border p-4 cursor-pointer transition-all"
                :class="
                  form.invalidate_other_sessions
                    ? 'border-primary/30 bg-primary/5'
                    : 'border-slate-200 hover:bg-slate-50'
                "
              >

                <input
                  v-model="form.invalidate_other_sessions"
                  type="checkbox"
                  class="mt-0.5 w-5 h-5 rounded cursor-pointer accent-primary"
                />

                <div>

                  <Label class="font-medium text-slate-800">
                    Invalidate Other Sessions
                  </Label>

                  <p class="text-xs text-slate-500 mt-1">
                    End all other active sessions when the password
                    is changed.
                  </p>

                </div>

              </label>

            </div>

          </div>

          <!-- =================================================
               SECURITY SUMMARY
          ================================================== -->

          <div
            class="mt-7 rounded-xl border border-slate-200 bg-slate-50/80 p-4"
          >

            <div class="flex items-start gap-3">

              <div
                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary"
              >
                <i class="fa-solid fa-shield-halved"></i>
              </div>

              <div>

                <Label class="font-semibold text-slate-800">
                  Security Overview
                </Label>

                <p class="text-sm leading-6 text-slate-500 mt-1">
                  Review the current authentication and session
                  security configuration before saving.
                </p>

              </div>

            </div>

            <!-- Summary -->

            <div
              class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-3"
            >

              <!-- Login -->

              <div
                class="rounded-lg bg-white border border-slate-200 p-3"
              >

                <div class="text-xs text-slate-500 mb-1">
                  Login Protection
                </div>

                <div
                  class="text-sm font-bold"
                  :class="
                    form.enable_login_attempt_limit
                      ? 'text-emerald-600'
                      : 'text-slate-500'
                  "
                >
                  {{
                    form.enable_login_attempt_limit
                      ? "Enabled"
                      : "Disabled"
                  }}
                </div>

                <div class="text-[11px] text-slate-400">
                  failed attempt limit
                </div>

              </div>

              <!-- Auto Logout -->

              <div
                class="rounded-lg bg-white border border-slate-200 p-3"
              >

                <div class="text-xs text-slate-500 mb-1">
                  Auto Logout
                </div>

                <div
                  class="text-sm font-bold"
                  :class="
                    form.enable_auto_logout
                      ? 'text-emerald-600'
                      : 'text-slate-500'
                  "
                >
                  {{
                    form.enable_auto_logout
                      ? "Enabled"
                      : "Disabled"
                  }}
                </div>

                <div class="text-[11px] text-slate-400">
                  session timeout
                </div>

              </div>

              <!-- 2FA -->

              <div
                class="rounded-lg bg-white border border-slate-200 p-3"
              >

                <div class="text-xs text-slate-500 mb-1">
                  Two-Factor Auth
                </div>

                <div
                  class="text-sm font-bold"
                  :class="
                    form.enable_2fa
                      ? 'text-emerald-600'
                      : 'text-slate-500'
                  "
                >
                  {{
                    form.enable_2fa
                      ? "Enabled"
                      : "Disabled"
                  }}
                </div>

                <div class="text-[11px] text-slate-400">
                  additional protection
                </div>

              </div>

              <!-- Registration -->

              <div
                class="rounded-lg bg-white border border-slate-200 p-3"
              >

                <div class="text-xs text-slate-500 mb-1">
                  Registration
                </div>

                <div
                  class="text-sm font-bold"
                  :class="
                    form.enable_registration
                      ? 'text-emerald-600'
                      : 'text-slate-500'
                  "
                >
                  {{
                    form.enable_registration
                      ? "Open"
                      : "Restricted"
                  }}
                </div>

                <div class="text-[11px] text-slate-400">
                  new accounts
                </div>

              </div>

            </div>

            <!-- Security Tip -->

            <div
              class="mt-4 flex items-start gap-2.5 rounded-lg bg-primary/5 border border-primary/10 px-3.5 py-3"
            >

              <i
                class="fa-solid fa-lightbulb text-primary mt-0.5 text-sm"
              ></i>

              <p class="text-xs leading-5 text-slate-600">

                <span class="font-semibold text-slate-700">
                  Security Tip:
                </span>

                Enable login attempt protection, automatic logout,
                email verification and two-factor authentication
                when appropriate for your application's security
                requirements.

              </p>

            </div>

          </div>

          <!-- =================================================
               FOOTER
          ================================================== -->

          <div
            class="mt-6 flex flex-col-reverse sm:flex-row gap-3 justify-end border-t border-slate-100 pt-4"
          >

            <Button
              @click="toggleModal"
              type="danger"
              text="Close"
            />

            <Button
              :submit="true"
              :text="
                form.processing
                  ? 'Saving...'
                  : 'Save Settings'
              "
              :processing="form.processing"
              :disabled="form.processing"
            />

          </div>

        </form>

      </div>

    </div>
  </Modal>
</template>