<script setup>
import { useForm } from "@inertiajs/vue3";
import Label from "@/Components/Label.vue";
import Button from "../Elements/Button.vue";
import Modal from "../Modal.vue";
import { ref, computed } from "vue";

const props = defineProps({
  config: {
    type: Object,
    default: () => ({}),
  },
});

const showModal = ref(false);
const showLicenseKey = ref(false);
const copied = ref(false);

const toggleModal = () => {
  showModal.value = !showModal.value;

  if (!showModal.value) {
    showLicenseKey.value = false;
    copied.value = false;
  }
};

const form = useForm({
  license_key: props.config?.license_key ?? "",
});

const maskedLicenseKey = computed(() => {
  if (!form.license_key) {
    return "Not configured";
  }

  if (showLicenseKey.value) {
    return form.license_key;
  }

  const key = form.license_key;

  if (key.length <= 8) {
    return "•".repeat(key.length);
  }

  return (
    key.substring(0, 4) +
    "•".repeat(Math.max(4, key.length - 8)) +
    key.substring(key.length - 4)
  );
});

const licenseStatus = computed(() => {
  if (!form.license_key) {
    return {
      label: "Not Configured",
      class: "bg-slate-100 text-slate-600",
      icon: "fa-circle-question",
    };
  }

  return {
    label: "License Key Configured",
    class: "bg-emerald-100 text-emerald-700",
    icon: "fa-circle-check",
  };
});

const copyLicenseKey = async () => {
  if (!form.license_key) {
    return;
  }

  try {
    await navigator.clipboard.writeText(form.license_key);

    copied.value = true;

    setTimeout(() => {
      copied.value = false;
    }, 2000);
  } catch (error) {
    console.error("Unable to copy license key:", error);
  }
};

const clearLicenseKey = () => {
  form.license_key = "";
};

const save = () => {
  form.post(route("configuration.setting.update"), {
    preserveScroll: true,

    onSuccess: () => {
      showModal.value = false;
      showLicenseKey.value = false;
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
      <i class="fa-solid fa-key"></i>
    </div>

    <div class="text-left">
      <span class="block font-medium text-slate-800"> License Configuration </span>

      <span class="block text-xs text-slate-500 mt-0.5">
        Manage your application license key and licensing information
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
            <i class="fa-solid fa-key"></i>
          </div>

          <div>
            <Label class="font-bold text-xl text-slate-800">
              License Configuration
            </Label>

            <p class="text-sm text-slate-500 mt-0.5">
              Configure the license key used to identify and authorize this application.
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
               LICENSE STATUS
          ================================================== -->

          <div
            class="flex items-center justify-between gap-4 rounded-xl border border-slate-200 bg-slate-50 p-4 mb-5"
          >
            <div class="flex items-center gap-3">
              <div
                class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg"
                :class="
                  form.license_key
                    ? 'bg-emerald-100 text-emerald-600'
                    : 'bg-slate-200 text-slate-500'
                "
              >
                <i
                  class="fa-solid"
                  :class="form.license_key ? 'fa-shield-check' : 'fa-shield-halved'"
                ></i>
              </div>

              <div>
                <Label class="font-semibold text-slate-800"> License Status </Label>

                <p class="text-xs text-slate-500 mt-1">
                  {{
                    form.license_key
                      ? "A license key has been configured for this application."
                      : "No license key has been configured yet."
                  }}
                </p>
              </div>
            </div>

            <span
              class="inline-flex items-center gap-1.5 rounded-full px-3 py-1.5 text-xs font-medium whitespace-nowrap"
              :class="licenseStatus.class"
            >
              <i class="fa-solid" :class="licenseStatus.icon"></i>

              {{ licenseStatus.label }}
            </span>
          </div>

          <!-- =================================================
               LICENSE KEY
          ================================================== -->

          <div>
            <div class="flex items-center justify-between mb-2">
              <div>
                <Label class="font-semibold text-slate-700"> License Key </Label>

                <p class="text-xs text-slate-500 mt-1">
                  Enter the license key provided for your application.
                </p>
              </div>
            </div>

            <!-- License Input -->

            <div class="relative">
              <input
                v-model="form.license_key"
                :type="showLicenseKey ? 'text' : 'password'"
                maxlength="255"
                autocomplete="off"
                placeholder="Enter your license key"
                class="w-full rounded-xl border border-slate-200 bg-white px-4 py-3 pr-32 text-sm text-slate-700 placeholder-slate-400 outline-none transition-all duration-200 hover:border-slate-300 focus:border-primary focus:ring-4 focus:ring-primary/10"
              />

              <!-- Actions -->

              <div
                class="absolute right-2 top-1/2 -translate-y-1/2 flex items-center gap-1"
              >
                <!-- Show / Hide -->

                <button
                  type="button"
                  @click="showLicenseKey = !showLicenseKey"
                  class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-600 transition"
                  :title="showLicenseKey ? 'Hide license key' : 'Show license key'"
                >
                  <i
                    class="fa-solid"
                    :class="showLicenseKey ? 'fa-eye-slash' : 'fa-eye'"
                  ></i>
                </button>

                <!-- Copy -->

                <button
                  type="button"
                  @click="copyLicenseKey"
                  :disabled="!form.license_key"
                  class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-600 disabled:opacity-40 disabled:cursor-not-allowed transition"
                  :title="copied ? 'Copied' : 'Copy license key'"
                >
                  <i
                    class="fa-solid"
                    :class="copied ? 'fa-check text-emerald-500' : 'fa-copy'"
                  ></i>
                </button>
              </div>
            </div>

            <!-- Clear -->

            <div class="flex justify-end mt-2">
              <button
                v-if="form.license_key"
                type="button"
                @click="clearLicenseKey"
                class="text-xs text-red-500 hover:text-red-600 flex items-center gap-1"
              >
                <i class="fa-solid fa-trash-can"></i>

                Remove License Key
              </button>
            </div>
          </div>

          <!-- =================================================
               SECURITY NOTICE
          ================================================== -->

          <div class="mt-5 rounded-xl border border-amber-200 bg-amber-50 p-4">
            <div class="flex items-start gap-3">
              <div
                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-amber-100 text-amber-600"
              >
                <i class="fa-solid fa-shield-halved"></i>
              </div>

              <div>
                <Label class="font-semibold text-amber-800">
                  Keep Your License Key Secure
                </Label>

                <p class="text-sm text-amber-700 leading-6 mt-1">
                  Your license key may grant access to application features or services.
                  Do not share it publicly or include it in source code, screenshots,
                  logs, or client-side configuration.
                </p>
              </div>
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
