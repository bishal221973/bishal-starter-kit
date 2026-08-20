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
  enable_password_policy:
    props.config?.enable_password_policy == 1,

  minimum_password_length:
    props.config?.minimum_password_length ?? 8,

  require_uppercase:
    props.config?.require_uppercase == 1,

  require_lowercase:
    props.config?.require_lowercase == 1,

  require_number:
    props.config?.require_number == 1,

  require_special_character:
    props.config?.require_special_character == 1,

  password_expiry_days:
    props.config?.password_expiry_days ?? 90,
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
    <!-- Icon -->
    <div
      class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary transition-all duration-200 group-hover:bg-primary group-hover:text-white"
    >
      <i class="fa-solid fa-user-shield"></i>
    </div>

    <!-- Content -->
    <div class="text-left">
      <Label class="block font-medium text-sm">
        Password Policy
      </Label>

      <Label class="block text-[12px] opacity-60 mt-0.5">
        Manage password strength and security requirements
      </Label>
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
            <i class="fa-solid fa-user-shield"></i>
          </div>

          <!-- Header Text -->
          <div>
            <Label class="font-bold text-xl text-slate-800">
              Password Policy
            </Label>

            <p class="text-sm text-slate-500 mt-0.5">
              Configure password strength requirements and
              expiration rules for user accounts.
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
               ENABLE PASSWORD POLICY
          ================================================== -->
          <label
            for="enable_password_policy"
            class="group flex items-center justify-between gap-4 rounded-xl border p-4 cursor-pointer transition-all duration-200"
            :class="
              form.enable_password_policy
                ? 'border-primary/30 bg-primary/5'
                : 'border-slate-200 hover:bg-slate-50'
            "
          >
            <div class="flex items-start gap-3">
              <!-- Icon -->
              <div
                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg transition-colors"
                :class="
                  form.enable_password_policy
                    ? 'bg-primary text-white'
                    : 'bg-slate-100 text-slate-500'
                "
              >
                <i class="fa-solid fa-lock"></i>
              </div>

              <div>
                <Label
                  for="enable_password_policy"
                  class="font-semibold text-slate-800 cursor-pointer"
                >
                  Enable Password Policy
                </Label>

                <p class="text-sm text-slate-500 mt-1">
                  Enforce password complexity and expiration
                  requirements for user accounts.
                </p>
              </div>
            </div>

            <!-- Toggle -->
            <div class="relative shrink-0">
              <input
                id="enable_password_policy"
                v-model="form.enable_password_policy"
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

          <!-- =================================================
               POLICY SETTINGS
          ================================================== -->
          <div
            v-if="form.enable_password_policy"
            class="mt-5 space-y-6"
          >
            <!-- ===============================================
                 BASIC PASSWORD SETTINGS
            ================================================ -->
            <div>
              <div class="flex items-center gap-2 mb-3">
                <div
                  class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary/10 text-primary"
                >
                  <i class="fa-solid fa-sliders text-sm"></i>
                </div>

                <div>
                  <Label
                    class="font-semibold text-base text-slate-800"
                  >
                    Password Settings
                  </Label>

                  <p class="text-xs text-slate-500">
                    Define the minimum password length and
                    expiration period.
                  </p>
                </div>
              </div>

              <div
                class="grid grid-cols-1 md:grid-cols-2 gap-4"
              >
                <!-- Minimum Length -->
                <div
                  class="rounded-xl border border-slate-200 p-4 bg-white"
                >
                  <TextInput
                    v-model="form.minimum_password_length"
                    type="number"
                    text="Minimum Password Length"
                    placeholder="Enter minimum length"
                    min="4"
                    max="100"
                    required
                  />

                  <p class="mt-1.5 text-xs text-slate-500">
                    Minimum number of characters required in a
                    password.
                  </p>
                </div>

                <!-- Expiry -->
                <div
                  class="rounded-xl border border-slate-200 p-4 bg-white"
                >
                  <TextInput
                    v-model="form.password_expiry_days"
                    type="number"
                    text="Password Expiry (Days)"
                    placeholder="Enter expiry days"
                    min="0"
                    max="3650"
                  />

                  <p class="mt-1.5 text-xs text-slate-500">
                    Number of days before users must change their
                    password. Use 0 for no expiration.
                  </p>
                </div>
              </div>
            </div>

            <!-- ===============================================
                 PASSWORD REQUIREMENTS
            ================================================ -->
            <div>
              <div class="flex items-center justify-between gap-3 mb-3">
                <div class="flex items-center gap-2">
                  <div
                    class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary/10 text-primary"
                  >
                    <i class="fa-solid fa-key text-sm"></i>
                  </div>

                  <div>
                    <Label
                      class="font-semibold text-base text-slate-800"
                    >
                      Password Requirements
                    </Label>

                    <p class="text-xs text-slate-500">
                      Select the character types users must include.
                    </p>
                  </div>
                </div>

                <span
                  class="hidden sm:inline-flex items-center rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-500"
                >
                  Security
                </span>
              </div>

              <div
                class="grid grid-cols-1 md:grid-cols-2 gap-3"
              >
                <!-- Uppercase -->
                <label
                  class="group flex items-start gap-3 rounded-xl border p-4 cursor-pointer transition-all duration-200"
                  :class="
                    form.require_uppercase
                      ? 'border-primary/30 bg-primary/5'
                      : 'border-slate-200 hover:border-slate-300 hover:bg-slate-50'
                  "
                >
                  <input
                    v-model="form.require_uppercase"
                    type="checkbox"
                    class="mt-0.5 w-5 h-5 rounded cursor-pointer accent-primary"
                  />

                  <div>
                    <Label
                      class="font-medium text-slate-800 cursor-pointer"
                    >
                      Uppercase Letters
                    </Label>

                    <p class="text-xs text-slate-500 mt-1">
                      Require at least one uppercase character
                      such as <strong>A-Z</strong>.
                    </p>
                  </div>
                </label>

                <!-- Lowercase -->
                <label
                  class="group flex items-start gap-3 rounded-xl border p-4 cursor-pointer transition-all duration-200"
                  :class="
                    form.require_lowercase
                      ? 'border-primary/30 bg-primary/5'
                      : 'border-slate-200 hover:border-slate-300 hover:bg-slate-50'
                  "
                >
                  <input
                    v-model="form.require_lowercase"
                    type="checkbox"
                    class="mt-0.5 w-5 h-5 rounded cursor-pointer accent-primary"
                  />

                  <div>
                    <Label
                      class="font-medium text-slate-800 cursor-pointer"
                    >
                      Lowercase Letters
                    </Label>

                    <p class="text-xs text-slate-500 mt-1">
                      Require at least one lowercase character
                      such as <strong>a-z</strong>.
                    </p>
                  </div>
                </label>

                <!-- Number -->
                <label
                  class="group flex items-start gap-3 rounded-xl border p-4 cursor-pointer transition-all duration-200"
                  :class="
                    form.require_number
                      ? 'border-primary/30 bg-primary/5'
                      : 'border-slate-200 hover:border-slate-300 hover:bg-slate-50'
                  "
                >
                  <input
                    v-model="form.require_number"
                    type="checkbox"
                    class="mt-0.5 w-5 h-5 rounded cursor-pointer accent-primary"
                  />

                  <div>
                    <Label
                      class="font-medium text-slate-800 cursor-pointer"
                    >
                      Numbers
                    </Label>

                    <p class="text-xs text-slate-500 mt-1">
                      Require at least one numeric character such
                      as <strong>0-9</strong>.
                    </p>
                  </div>
                </label>

                <!-- Special Character -->
                <label
                  class="group flex items-start gap-3 rounded-xl border p-4 cursor-pointer transition-all duration-200"
                  :class="
                    form.require_special_character
                      ? 'border-primary/30 bg-primary/5'
                      : 'border-slate-200 hover:border-slate-300 hover:bg-slate-50'
                  "
                >
                  <input
                    v-model="form.require_special_character"
                    type="checkbox"
                    class="mt-0.5 w-5 h-5 rounded cursor-pointer accent-primary"
                  />

                  <div>
                    <Label
                      class="font-medium text-slate-800 cursor-pointer"
                    >
                      Special Characters
                    </Label>

                    <p class="text-xs text-slate-500 mt-1">
                      Require symbols such as
                      <strong>! @ # $ %</strong>.
                    </p>
                  </div>
                </label>
              </div>
            </div>

            <!-- ===============================================
                 SECURITY INFORMATION
            ================================================ -->
            <div
              class="rounded-xl border border-slate-200 bg-slate-50/80 p-4"
            >
              <div class="flex items-start gap-3">
                <div
                  class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary"
                >
                  <i class="fa-solid fa-shield-halved"></i>
                </div>

                <div>
                  <Label
                    class="font-semibold text-slate-800"
                  >
                    Password Security
                  </Label>

                  <p
                    class="text-sm leading-6 text-slate-500 mt-1"
                  >
                    Strong password requirements help protect
                    user accounts from unauthorized access. We
                    recommend enabling multiple character
                    requirements and using an appropriate expiration
                    period.
                  </p>
                </div>
              </div>

              <!-- Security Summary -->
              <div
                class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-3"
              >
                <!-- Length -->
                <div
                  class="rounded-lg bg-white border border-slate-200 p-3"
                >
                  <div
                    class="text-xs text-slate-500 mb-1"
                  >
                    Minimum Length
                  </div>

                  <div
                    class="text-lg font-bold text-slate-800"
                  >
                    {{ form.minimum_password_length }}
                  </div>

                  <div class="text-[11px] text-slate-400">
                    characters
                  </div>
                </div>

                <!-- Expiry -->
                <div
                  class="rounded-lg bg-white border border-slate-200 p-3"
                >
                  <div
                    class="text-xs text-slate-500 mb-1"
                  >
                    Expiration
                  </div>

                  <div
                    class="text-lg font-bold text-slate-800"
                  >
                    {{
                      form.password_expiry_days == 0
                        ? "Never"
                        : form.password_expiry_days
                    }}
                  </div>

                  <div class="text-[11px] text-slate-400">
                    {{
                      form.password_expiry_days == 0
                        ? "no expiry"
                        : "days"
                    }}
                  </div>
                </div>

                <!-- Character Rules -->
                <div
                  class="rounded-lg bg-white border border-slate-200 p-3"
                >
                  <div
                    class="text-xs text-slate-500 mb-1"
                  >
                    Requirements
                  </div>

                  <div
                    class="text-lg font-bold text-slate-800"
                  >
                    {{
                      Number(form.require_uppercase) +
                      Number(form.require_lowercase) +
                      Number(form.require_number) +
                      Number(form.require_special_character)
                    }}
                    / 4
                  </div>

                  <div class="text-[11px] text-slate-400">
                    rules enabled
                  </div>
                </div>

                <!-- Status -->
                <div
                  class="rounded-lg bg-white border border-slate-200 p-3"
                >
                  <div
                    class="text-xs text-slate-500 mb-1"
                  >
                    Policy Status
                  </div>

                  <div
                    class="text-sm font-bold"
                    :class="
                      form.enable_password_policy
                        ? 'text-emerald-600'
                        : 'text-slate-500'
                    "
                  >
                    {{
                      form.enable_password_policy
                        ? "Enabled"
                        : "Disabled"
                    }}
                  </div>

                  <div class="text-[11px] text-slate-400">
                    global setting
                  </div>
                </div>
              </div>

              <!-- Tip -->
              <div
                class="mt-4 flex items-start gap-2.5 rounded-lg bg-primary/5 border border-primary/10 px-3.5 py-3"
              >
                <i
                  class="fa-solid fa-lightbulb text-primary mt-0.5 text-sm"
                ></i>

                <p
                  class="text-xs leading-5 text-slate-600"
                >
                  <span class="font-semibold text-slate-700">
                    Recommended:
                  </span>
                  Use a minimum length of 8 characters and require
                  uppercase letters, lowercase letters, numbers,
                  and special characters for stronger account
                  protection.
                </p>
              </div>
            </div>
          </div>

          <!-- =================================================
               DISABLED POLICY MESSAGE
          ================================================== -->
          <div
            v-else
            class="mt-5 rounded-xl border border-dashed border-slate-300 bg-slate-50 p-6 text-center"
          >
            <div
              class="mx-auto flex h-12 w-12 items-center justify-center rounded-full bg-slate-100 text-slate-400"
            >
              <i class="fa-solid fa-lock-open text-lg"></i>
            </div>

            <Label
              class="block mt-3 font-semibold text-slate-700"
            >
              Password Policy is Disabled
            </Label>

            <p
              class="max-w-md mx-auto mt-1.5 text-sm leading-6 text-slate-500"
            >
              Enable the password policy above to configure
              password strength requirements and expiration rules
              for your users.
            </p>
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