<script setup>
import { useForm } from "@inertiajs/vue3";
import Label from "@/Components/Label.vue";
import TextInput from "@/Components/Elements/TextInput.vue";
import Button from "../Elements/Button.vue";
import Modal from "../Modal.vue";
import { ref, computed } from "vue";
import { RisingSelect } from "rising-select";
import { useTheme } from "@/composables/useTheme.js";

const props = defineProps({
  config: {
    type: Object,
    default: () => ({}),
  },
});

const { theme } = useTheme();

const showModal = ref(false);
const newIp = ref("");
const ipError = ref("");

const toggleModal = () => {
  showModal.value = !showModal.value;
};

/*
|--------------------------------------------------------------------------
| Parse Maintenance Allowed IPs
|--------------------------------------------------------------------------
*/

const parseAllowedIps = () => {
  const value = props.config?.maintenance_mode_allowed_ips;

  if (!value) {
    return [];
  }

  if (Array.isArray(value)) {
    return [...value];
  }

  if (typeof value === "string") {
    try {
      const parsed = JSON.parse(value);

      if (Array.isArray(parsed)) {
        return parsed;
      }
    } catch (error) {
      // Fallback below
    }

    return value
      .split(",")
      .map((ip) => ip.trim())
      .filter(Boolean);
  }

  return [];
};

/*
|--------------------------------------------------------------------------
| Form
|--------------------------------------------------------------------------
*/

const form = useForm({
  application_version: props.config?.application_version ?? "1.0.0",

  default_language: props.config?.default_language ?? "en",

  timezone: props.config?.timezone ?? "Asia/Kathmandu",

  decimal_places: props.config?.decimal_places ?? 2,

  maintenance_mode: props.config?.maintenance_mode == 1,

  maintenance_mode_allowed_ips: parseAllowedIps(),

  session_timeout: props.config?.session_timeout ?? 2,

  data_table_source: props.config?.data_table_source ?? "server",

  default_pagination_size: props.config?.default_pagination_size ?? 20,
});

/*
|--------------------------------------------------------------------------
| Options
|--------------------------------------------------------------------------
*/

const languageOptions = [
  {
    label: "English",
    value: "en",
  },
  {
    label: "नेपाली",
    value: "np",
  },
];

const timezoneOptions = [
  {
    label: "Nepal — Asia/Kathmandu",
    value: "Asia/Kathmandu",
  },
  {
    label: "UTC",
    value: "UTC",
  },
];

const tableSourceOptions = [
  {
    label: "Server Side",
    value: "server",
  },
  {
    label: "Client Side",
    value: "client",
  },
];

const paginationOptions = [
  {
    label: "10 Records",
    value: "10",
  },
  {
    label: "20 Records",
    value: "20",
  },
  {
    label: "25 Records",
    value: "25",
  },
  {
    label: "50 Records",
    value: "50",
  },
  {
    label: "100 Records",
    value: "100",
  },
];

/*
|--------------------------------------------------------------------------
| IP Validation
|--------------------------------------------------------------------------
*/

const isValidIPv4 = (ip) => {
  const parts = ip.trim().split(".");

  if (parts.length !== 4) {
    return false;
  }

  return parts.every((part) => {
    if (!/^\d+$/.test(part)) {
      return false;
    }

    const number = Number(part);

    return number >= 0 && number <= 255;
  });
};

const isValidIPv6 = (ip) => {
  return /^[0-9a-fA-F:]+$/.test(ip) && ip.includes(":");
};

const isValidIp = (ip) => {
  return isValidIPv4(ip) || isValidIPv6(ip);
};

/*
|--------------------------------------------------------------------------
| Add Maintenance IP
|--------------------------------------------------------------------------
*/

const addIp = () => {
  const ip = newIp.value.trim();

  ipError.value = "";

  if (!ip) {
    ipError.value = "Please enter an IP address.";
    return;
  }

  if (!isValidIp(ip)) {
    ipError.value = "Please enter a valid IPv4 or IPv6 address.";
    return;
  }

  if (form.maintenance_mode_allowed_ips.includes(ip)) {
    ipError.value = "This IP address is already in the allowed list.";
    return;
  }

  form.maintenance_mode_allowed_ips.push(ip);

  newIp.value = "";
};

/*
|--------------------------------------------------------------------------
| Remove IP
|--------------------------------------------------------------------------
*/

const removeIp = (index) => {
  form.maintenance_mode_allowed_ips.splice(index, 1);
};

const clearIps = () => {
  form.maintenance_mode_allowed_ips = [];
};

/*
|--------------------------------------------------------------------------
| Computed
|--------------------------------------------------------------------------
*/

const allowedIpCount = computed(() => {
  return form.maintenance_mode_allowed_ips.length;
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
    <div
      class="flex h-10 w-10 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary transition-all duration-200 group-hover:bg-primary group-hover:text-white"
    >
      <i class="fa-solid fa-sliders"></i>
    </div>

    <div class="text-left">
      <span class="block font-medium text-slate-800"> Application Configuration </span>

      <span class="block text-xs text-slate-500 mt-0.5">
        Manage application, language, maintenance and system preferences
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
            <i class="fa-solid fa-gear"></i>
          </div>

          <div>
            <Label class="font-bold text-xl text-slate-800">
              Application Configuration
            </Label>

            <p class="text-sm text-slate-500 mt-0.5">
              Configure application behavior, localization, maintenance mode and data
              display preferences.
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
               APPLICATION
          ================================================== -->

          <div class="mb-6">
            <div class="flex items-center gap-2 mb-3">
              <div
                class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary/10 text-primary"
              >
                <i class="fa-solid fa-window-maximize text-sm"></i>
              </div>

              <div>
                <Label class="font-semibold text-base"> Application </Label>

                <p class="text-xs text-slate-500">Basic application information.</p>
              </div>
            </div>

            <div class="rounded-xl border border-slate-200 p-4">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <TextInput
                  v-model="form.application_version"
                  type="text"
                  text="Application Version"
                  placeholder="Example: 1.0.0"
                  required
                />

                <TextInput
                  v-model="form.decimal_places"
                  type="number"
                  text="Decimal Places"
                  placeholder="Example: 2"
                  min="0"
                  max="6"
                  required
                />
              </div>

              <p class="text-xs text-slate-500 mt-3">
                The application version is displayed when identifying the current system
                release. Decimal places control how numeric and monetary values are
                displayed.
              </p>
            </div>
          </div>

          <!-- =================================================
               LOCALIZATION
          ================================================== -->

          <div class="mb-6">
            <div class="flex items-center gap-2 mb-3">
              <div
                class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary/10 text-primary"
              >
                <i class="fa-solid fa-language text-sm"></i>
              </div>

              <div>
                <Label class="font-semibold text-base"> Localization </Label>

                <p class="text-xs text-slate-500">
                  Set the default language and timezone.
                </p>
              </div>
            </div>

            <div class="rounded-xl border border-slate-200 p-4">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Language -->

                <div>
                  <Label class="font-medium text-slate-700 mb-[7px] block">
                    Default Language
                    <span class="text-red-500 ml-1">*</span>
                  </Label>

                  <RisingSelect
                    v-model="form.default_language"
                    :options="languageOptions"
                    wrapperBg="bg-white"
                    wrapperRounded="rounded-xl"
                    wrapperClass="border border-primary shadow-sm transition-all duration-200 ease-in-out outline-none hover:border-slate-300 focus:border-primary focus:ring-4 focus:ring-indigo-500/10 focus:shadow-md"
                    inputClass="text-sm focus:ring-0 py-[10px] focus:outline-0"
                    :primaryColor="theme?.primary"
                    placeholder="Select Language"
                  />
                </div>

                <!-- Timezone -->

                <div>
                  <Label class="font-medium text-slate-700 mb-[7px] block">
                    Timezone
                    <span class="text-red-500 ml-1">*</span>
                  </Label>

                  <RisingSelect
                    v-model="form.timezone"
                    :options="timezoneOptions"
                    wrapperBg="bg-white"
                    wrapperRounded="rounded-xl"
                    wrapperClass="border border-primary shadow-sm transition-all duration-200 ease-in-out outline-none hover:border-slate-300 focus:border-primary focus:ring-4 focus:ring-indigo-500/10 focus:shadow-md"
                    inputClass="text-sm focus:ring-0 py-[10px] focus:outline-0"
                    :primaryColor="theme?.primary"
                    placeholder="Select Timezone"
                  />
                </div>
              </div>

              <p class="text-xs text-slate-500 mt-3">
                Language affects the default interface language, while timezone determines
                how dates and times are interpreted throughout the application.
              </p>
            </div>
          </div>

          <!-- =================================================
               MAINTENANCE MODE
          ================================================== -->

          <div class="mb-6">
            <div class="flex items-center gap-2 mb-3">
              <div
                class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary/10 text-primary"
              >
                <i class="fa-solid fa-screwdriver-wrench text-sm"></i>
              </div>

              <div>
                <Label class="font-semibold text-base"> Maintenance Mode </Label>

                <p class="text-xs text-slate-500">
                  Temporarily restrict application access.
                </p>
              </div>
            </div>

            <!-- Enable Maintenance -->

            <label
              for="maintenance_mode"
              class="flex items-center justify-between gap-4 rounded-xl border p-4 cursor-pointer transition-all duration-200"
              :class="
                form.maintenance_mode
                  ? 'border-amber-300 bg-amber-50'
                  : 'border-slate-200 hover:bg-slate-50'
              "
            >
              <div class="flex items-start gap-3">
                <div
                  class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg"
                  :class="
                    form.maintenance_mode
                      ? 'bg-amber-500 text-white'
                      : 'bg-slate-100 text-slate-500'
                  "
                >
                  <i class="fa-solid fa-triangle-exclamation"></i>
                </div>

                <div>
                  <Label
                    for="maintenance_mode"
                    class="font-semibold text-slate-800 cursor-pointer"
                  >
                    Enable Maintenance Mode
                  </Label>

                  <p class="text-sm text-slate-500 mt-1">
                    Temporarily prevent normal users from accessing the application while
                    maintenance is being performed.
                  </p>
                </div>
              </div>

              <div class="relative shrink-0">
                <input
                  id="maintenance_mode"
                  v-model="form.maintenance_mode"
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

            <!-- Allowed IPs -->

            <div
              v-if="form.maintenance_mode"
              class="mt-3 rounded-xl border border-slate-200 p-4"
            >
              <div class="flex items-center justify-between mb-3">
                <div>
                  <Label class="font-semibold text-slate-800">
                    Allowed IP Addresses
                  </Label>

                  <p class="text-xs text-slate-500 mt-1">
                    These addresses can access the application while maintenance mode is
                    enabled.
                  </p>
                </div>

                <span
                  class="rounded-full bg-primary/10 px-2.5 py-1 text-xs font-semibold text-primary"
                >
                  {{ allowedIpCount }}
                  {{ allowedIpCount === 1 ? "IP" : "IPs" }}
                </span>
              </div>

              <!-- Add IP -->

              <div class="flex flex-col sm:flex-row gap-3">
                <div class="flex-1">
                  <TextInput
                    v-model="newIp"
                    type="text"
                    text="Allowed IP Address"
                    placeholder="Example: 192.168.1.100"
                    @keyup.enter="addIp"
                  />
                </div>

                <div class="flex items-end">
                  <button
                    type="button"
                    @click="addIp"
                    class="h-[42px] px-5 rounded-xl bg-primary text-white text-sm font-medium hover:opacity-90 transition flex items-center justify-center gap-2 w-full sm:w-auto"
                  >
                    <i class="fa-solid fa-plus"></i>

                    Add IP
                  </button>
                </div>
              </div>

              <!-- Error -->

              <p v-if="ipError" class="mt-2 text-xs text-red-500 flex items-center gap-1">
                <i class="fa-solid fa-circle-exclamation"></i>

                {{ ipError }}
              </p>

              <!-- IP List -->

              <div
                v-if="form.maintenance_mode_allowed_ips.length"
                class="mt-3 rounded-xl border border-slate-200 overflow-hidden"
              >
                <div
                  class="flex items-center justify-between px-4 py-3 bg-slate-50 border-b"
                >
                  <span class="text-sm font-medium text-slate-700">
                    Maintenance Allowed Addresses
                  </span>

                  <button
                    type="button"
                    @click="clearIps"
                    class="text-xs font-medium text-red-500 hover:text-red-600"
                  >
                    Clear All
                  </button>
                </div>

                <div class="divide-y divide-slate-100">
                  <div
                    v-for="(ip, index) in form.maintenance_mode_allowed_ips"
                    :key="`${ip}-${index}`"
                    class="flex items-center justify-between px-4 py-3 hover:bg-slate-50"
                  >
                    <div class="flex items-center gap-3">
                      <div
                        class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-50 text-emerald-500"
                      >
                        <i class="fa-solid fa-unlock"></i>
                      </div>

                      <code class="text-sm font-medium text-slate-700">
                        {{ ip }}
                      </code>
                    </div>

                    <button
                      type="button"
                      @click="removeIp(index)"
                      class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-red-50 hover:text-red-500 transition"
                    >
                      <i class="fa-solid fa-trash text-sm"></i>
                    </button>
                  </div>
                </div>
              </div>

              <!-- Empty -->

              <div
                v-else
                class="mt-3 rounded-xl border border-dashed border-slate-300 p-5 text-center"
              >
                <i class="fa-solid fa-network-wired text-slate-400"></i>

                <p class="text-sm font-medium text-slate-600 mt-2">
                  No allowed IP addresses
                </p>

                <p class="text-xs text-slate-500 mt-1">
                  Without an allowed IP, maintenance mode may restrict access for everyone
                  except your application's bypass mechanism.
                </p>
              </div>
            </div>
          </div>

          <!-- =================================================
               SESSION
          ================================================== -->

          <div class="mb-6">
            <div class="flex items-center gap-2 mb-3">
              <div
                class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary/10 text-primary"
              >
                <i class="fa-solid fa-clock text-sm"></i>
              </div>

              <div>
                <Label class="font-semibold text-base"> Session </Label>

                <p class="text-xs text-slate-500">
                  Configure how long user sessions remain active.
                </p>
              </div>
            </div>

            <div class="rounded-xl border border-slate-200 p-4">
              <TextInput
                v-model="form.session_timeout"
                type="number"
                text="Session Timeout (Days)"
                placeholder="Example: 2"
                min="1"
                required
              />

              <p class="text-xs text-slate-500 mt-3">
                Users will be required to authenticate again after the configured session
                period expires.
              </p>
            </div>
          </div>

          <!-- =================================================
               DATA TABLE
          ================================================== -->

          <div class="mb-6">
            <div class="flex items-center gap-2 mb-3">
              <div
                class="flex h-8 w-8 items-center justify-center rounded-lg bg-primary/10 text-primary"
              >
                <i class="fa-solid fa-table-list text-sm"></i>
              </div>

              <div>
                <Label class="font-semibold text-base"> Data Tables </Label>

                <p class="text-xs text-slate-500">
                  Configure table loading and pagination behavior.
                </p>
              </div>
            </div>

            <div class="rounded-xl border border-slate-200 p-4">
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <!-- Data Source -->

                <div>
                  <Label class="font-medium text-slate-700 mb-[7px] block">
                    Data Table Source
                    <span class="text-red-500 ml-1">*</span>
                  </Label>

                  <RisingSelect
                    v-model="form.data_table_source"
                    :options="tableSourceOptions"
                    wrapperBg="bg-white"
                    wrapperRounded="rounded-xl"
                    wrapperClass="border border-primary shadow-sm transition-all duration-200 ease-in-out outline-none hover:border-slate-300 focus:border-primary focus:ring-4 focus:ring-indigo-500/10 focus:shadow-md"
                    inputClass="text-sm focus:ring-0 py-[10px] focus:outline-0"
                    :primaryColor="theme?.primary"
                    placeholder="Select Data Source"
                  />
                </div>

                <!-- Pagination -->

                <div>
                  <Label class="font-medium text-slate-700 mb-[7px] block">
                    Default Pagination Size
                    <span class="text-red-500 ml-1">*</span>
                  </Label>

                  <RisingSelect
                    v-model="form.default_pagination_size"
                    :options="paginationOptions"
                    wrapperBg="bg-white"
                    wrapperRounded="rounded-xl"
                    wrapperClass="border border-primary shadow-sm transition-all duration-200 ease-in-out outline-none hover:border-slate-300 focus:border-primary focus:ring-4 focus:ring-indigo-500/10 focus:shadow-md"
                    inputClass="text-sm focus:ring-0 py-[10px] focus:outline-0"
                    :primaryColor="theme?.primary"
                    placeholder="Select Page Size"
                  />
                </div>
              </div>

              <p class="text-xs text-slate-500 mt-3">
                Server-side tables load and paginate records from the backend, while
                client-side tables load data into the browser. Choose a pagination size
                appropriate for your application's dataset.
              </p>
            </div>
          </div>

          <!-- =================================================
               INFORMATION
          ================================================== -->

          <div class="rounded-xl border border-primary/10 bg-primary/5 p-4">
            <div class="flex items-start gap-3">
              <div
                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary"
              >
                <i class="fa-solid fa-lightbulb"></i>
              </div>

              <div>
                <Label class="font-semibold text-slate-800">
                  Configuration Information
                </Label>

                <p class="text-sm leading-6 text-slate-600 mt-1">
                  These settings control important application-wide behavior including
                  localization, maintenance access, user sessions, table performance and
                  pagination. Changes may affect all users of the application.
                </p>
              </div>
            </div>
          </div>

          <!-- =================================================
               SUMMARY
          ================================================== -->

          <div class="mt-4 grid grid-cols-2 md:grid-cols-4 gap-3">
            <!-- Version -->

            <div class="rounded-xl border border-slate-200 p-3">
              <div class="flex items-center gap-2">
                <i class="fa-solid fa-code-branch text-slate-400"></i>

                <span class="text-xs text-slate-500"> Version </span>
              </div>

              <div class="mt-1 text-sm font-bold text-slate-700">
                {{ form.application_version }}
              </div>
            </div>

            <!-- Language -->

            <div class="rounded-xl border border-slate-200 p-3">
              <div class="flex items-center gap-2">
                <i class="fa-solid fa-language text-slate-400"></i>

                <span class="text-xs text-slate-500"> Language </span>
              </div>

              <div class="mt-1 text-sm font-bold text-slate-700">
                {{ form.default_language === "np" ? "नेपाली" : "English" }}
              </div>
            </div>

            <!-- Maintenance -->

            <div class="rounded-xl border border-slate-200 p-3">
              <div class="flex items-center gap-2">
                <i class="fa-solid fa-screwdriver-wrench text-slate-400"></i>

                <span class="text-xs text-slate-500"> Maintenance </span>
              </div>

              <div
                class="mt-1 text-sm font-bold"
                :class="form.maintenance_mode ? 'text-amber-600' : 'text-emerald-600'"
              >
                {{ form.maintenance_mode ? "Enabled" : "Disabled" }}
              </div>
            </div>

            <!-- Pagination -->

            <div class="rounded-xl border border-slate-200 p-3">
              <div class="flex items-center gap-2">
                <i class="fa-solid fa-list-ol text-slate-400"></i>

                <span class="text-xs text-slate-500"> Page Size </span>
              </div>

              <div class="mt-1 text-sm font-bold text-slate-700">
                {{ form.default_pagination_size }}
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
