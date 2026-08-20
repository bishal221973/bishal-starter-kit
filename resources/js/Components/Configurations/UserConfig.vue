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

const form = useForm({
  // Users
  auto_disable_inactive_users: props.config?.auto_disable_inactive_users == 1,

  inactive_user_days: props.config?.inactive_user_days ?? 90,

  enable_delete_account: props.config?.enable_delete_account == 1,

  force_single_device_login: props.config?.force_single_device_login == 1,
});

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
    <div
      class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary transition-all duration-200 group-hover:bg-primary group-hover:text-white"
    >
      <i class="fa-solid fa-users-gear"></i>
    </div>

    <div class="text-left">
      <span class="block font-medium text-slate-800"> User Configuration </span>

      <span class="block text-xs text-slate-500 mt-0.5">
        Manage inactive users, account deletion and device access
      </span>
    </div>

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

      <div class="px-6 py-4 border-b border-slate-100 bg-card_header_color">
        <div class="flex items-center gap-3">
          <div
            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary"
          >
            <i class="fa-solid fa-users-gear"></i>
          </div>

          <div>
            <Label class="font-bold text-xl text-slate-800"> User Configuration </Label>

            <p class="text-sm text-slate-500 mt-0.5">
              Configure account lifecycle, inactivity and device login policies for users.
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
               INACTIVE USERS
          ================================================== -->

          <div class="mb-5">
            <div class="flex items-center gap-2 mb-3">
              <div
                class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary/10 text-primary"
              >
                <i class="fa-solid fa-user-clock text-sm"></i>
              </div>

              <div>
                <Label class="font-semibold text-base"> Inactive User Management </Label>

                <p class="text-xs text-slate-500">
                  Automatically disable accounts that have been inactive.
                </p>
              </div>
            </div>

            <!-- Enable Auto Disable -->

            <label
              for="auto_disable_inactive_users"
              class="flex items-center justify-between gap-4 rounded-xl border p-4 cursor-pointer transition-all duration-200"
              :class="
                form.auto_disable_inactive_users
                  ? 'border-primary/30 bg-primary/5'
                  : 'border-slate-200 hover:bg-slate-50'
              "
            >
              <div class="flex items-start gap-3">
                <div
                  class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg"
                  :class="
                    form.auto_disable_inactive_users
                      ? 'bg-primary text-white'
                      : 'bg-slate-100 text-slate-500'
                  "
                >
                  <i class="fa-solid fa-user-slash"></i>
                </div>

                <div>
                  <Label
                    for="auto_disable_inactive_users"
                    class="font-medium text-slate-800 cursor-pointer"
                  >
                    Automatically Disable Inactive Users
                  </Label>

                  <p class="text-sm text-slate-500 mt-1">
                    Automatically disable user accounts when they have not logged in for
                    the configured number of days.
                  </p>
                </div>
              </div>

              <!-- Switch -->

              <div class="relative shrink-0">
                <input
                  id="auto_disable_inactive_users"
                  v-model="form.auto_disable_inactive_users"
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

            <!-- Inactive Days -->

            <div
              v-if="form.auto_disable_inactive_users"
              class="mt-3 rounded-xl border border-slate-200 p-4"
            >
              <TextInput
                v-model="form.inactive_user_days"
                type="number"
                text="Inactive User Period (Days)"
                placeholder="Example: 90"
                min="1"
                max="3650"
                required
              />

              <p class="text-xs text-slate-500 mt-2">
                Users who have not logged in for this number of days will be automatically
                disabled.
              </p>
            </div>
          </div>

          <!-- =================================================
               ACCOUNT DELETION
          ================================================== -->

          <div class="mb-5">
            <div class="flex items-center gap-2 mb-3">
              <div
                class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary/10 text-primary"
              >
                <i class="fa-solid fa-user-xmark text-sm"></i>
              </div>

              <div>
                <Label class="font-semibold text-base"> Account Management </Label>

                <p class="text-xs text-slate-500">
                  Control whether users can delete their accounts.
                </p>
              </div>
            </div>

            <label
              for="enable_delete_account"
              class="flex items-center justify-between gap-4 rounded-xl border p-4 cursor-pointer transition-all duration-200"
              :class="
                form.enable_delete_account
                  ? 'border-red-200 bg-red-50'
                  : 'border-slate-200 hover:bg-slate-50'
              "
            >
              <div class="flex items-start gap-3">
                <div
                  class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg"
                  :class="
                    form.enable_delete_account
                      ? 'bg-red-500 text-white'
                      : 'bg-slate-100 text-slate-500'
                  "
                >
                  <i class="fa-solid fa-user-minus"></i>
                </div>

                <div>
                  <Label
                    for="enable_delete_account"
                    class="font-medium text-slate-800 cursor-pointer"
                  >
                    Allow Account Deletion
                  </Label>

                  <p class="text-sm text-slate-500 mt-1">
                    Allow users to permanently delete their own accounts from the
                    application.
                  </p>
                </div>
              </div>

              <!-- Switch -->

              <div class="relative shrink-0">
                <input
                  id="enable_delete_account"
                  v-model="form.enable_delete_account"
                  type="checkbox"
                  class="peer sr-only"
                />

                <div
                  class="w-11 h-6 rounded-full bg-slate-300 peer-checked:bg-red-500 transition-colors"
                ></div>

                <div
                  class="absolute left-1 top-1 w-4 h-4 rounded-full bg-white shadow-sm transition-transform peer-checked:translate-x-5"
                ></div>
              </div>
            </label>

            <!-- Warning -->

            <div
              v-if="form.enable_delete_account"
              class="mt-3 rounded-xl border border-amber-200 bg-amber-50 p-3"
            >
              <div class="flex items-start gap-2">
                <i class="fa-solid fa-triangle-exclamation text-amber-500 mt-0.5"></i>

                <p class="text-xs text-amber-700 leading-5">
                  Account deletion can be permanent. Make sure important user data is
                  handled according to your application's data retention policy.
                </p>
              </div>
            </div>
          </div>

          <!-- =================================================
               DEVICE LOGIN
          ================================================== -->

          <div class="mb-5">
            <div class="flex items-center gap-2 mb-3">
              <div
                class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary/10 text-primary"
              >
                <i class="fa-solid fa-mobile-screen-button text-sm"></i>
              </div>

              <div>
                <Label class="font-semibold text-base"> Device Login </Label>

                <p class="text-xs text-slate-500">
                  Control simultaneous sessions across devices.
                </p>
              </div>
            </div>

            <label
              for="force_single_device_login"
              class="flex items-center justify-between gap-4 rounded-xl border p-4 cursor-pointer transition-all duration-200"
              :class="
                form.force_single_device_login
                  ? 'border-primary/30 bg-primary/5'
                  : 'border-slate-200 hover:bg-slate-50'
              "
            >
              <div class="flex items-start gap-3">
                <div
                  class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg"
                  :class="
                    form.force_single_device_login
                      ? 'bg-primary text-white'
                      : 'bg-slate-100 text-slate-500'
                  "
                >
                  <i class="fa-solid fa-lock"></i>
                </div>

                <div>
                  <Label
                    for="force_single_device_login"
                    class="font-medium text-slate-800 cursor-pointer"
                  >
                    Force Single Device Login
                  </Label>

                  <p class="text-sm text-slate-500 mt-1">
                    Allow each user to maintain only one active device session at a time.
                  </p>
                </div>
              </div>

              <!-- Switch -->

              <div class="relative shrink-0">
                <input
                  id="force_single_device_login"
                  v-model="form.force_single_device_login"
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
          </div>

          <!-- =================================================
               INFORMATION
          ================================================== -->

          <div class="rounded-xl border border-primary/10 bg-primary/5 p-4">
            <div class="flex items-start gap-3">
              <div
                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary"
              >
                <i class="fa-solid fa-circle-info"></i>
              </div>

              <div>
                <Label class="font-semibold text-slate-800">
                  User Management Information
                </Label>

                <p class="text-sm text-slate-600 leading-6 mt-1">
                  These settings control the lifecycle and access behavior of user
                  accounts. Automatically disabling inactive users can improve security,
                  while single device login can help prevent account sharing.
                </p>
              </div>
            </div>
          </div>

          <!-- =================================================
               CURRENT STATUS
          ================================================== -->

          <div class="mt-4 grid grid-cols-1 sm:grid-cols-3 gap-3">
            <!-- Inactive -->

            <div class="rounded-xl border border-slate-200 p-3">
              <div class="flex items-center gap-2">
                <i class="fa-solid fa-user-clock text-slate-400"></i>

                <span class="text-xs text-slate-500"> Inactive Users </span>
              </div>

              <p
                class="mt-1 text-sm font-bold"
                :class="
                  form.auto_disable_inactive_users ? 'text-primary' : 'text-slate-600'
                "
              >
                {{
                  form.auto_disable_inactive_users
                    ? `${form.inactive_user_days} Days`
                    : "Disabled"
                }}
              </p>
            </div>

            <!-- Delete -->

            <div class="rounded-xl border border-slate-200 p-3">
              <div class="flex items-center gap-2">
                <i class="fa-solid fa-user-xmark text-slate-400"></i>

                <span class="text-xs text-slate-500"> Account Deletion </span>
              </div>

              <p
                class="mt-1 text-sm font-bold"
                :class="form.enable_delete_account ? 'text-red-500' : 'text-slate-600'"
              >
                {{ form.enable_delete_account ? "Allowed" : "Disabled" }}
              </p>
            </div>

            <!-- Device -->

            <div class="rounded-xl border border-slate-200 p-3">
              <div class="flex items-center gap-2">
                <i class="fa-solid fa-mobile-screen-button text-slate-400"></i>

                <span class="text-xs text-slate-500"> Device Login </span>
              </div>

              <p
                class="mt-1 text-sm font-bold"
                :class="
                  form.force_single_device_login ? 'text-primary' : 'text-slate-600'
                "
              >
                {{
                  form.force_single_device_login ? "Single Device" : "Multiple Devices"
                }}
              </p>
            </div>
          </div>

          <!-- =================================================
               FOOTER
          ================================================== -->

          <div
            class="mt-6 flex flex-col-reverse sm:flex-row gap-3 justify-end border-t border-slate-100 pt-4"
          >
            <Button @click="toggleModal" type="danger" text="Close" />

            <Button
              :submit="true"
              :text="form.processing ? 'Saving...' : 'Save Settings'"
              :processing="form.processing"
              :disabled="form.processing"
            />
          </div>
        </form>
      </div>
    </div>
  </Modal>
</template>
