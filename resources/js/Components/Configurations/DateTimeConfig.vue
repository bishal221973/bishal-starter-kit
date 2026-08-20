<script setup>
import { useForm } from "@inertiajs/vue3";
import Label from "@/Components/Label.vue";
import Button from "../Elements/Button.vue";
import Modal from "../Modal.vue";
import { ref } from "vue";
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

const toggleModal = () => {
  showModal.value = !showModal.value;
};

const form = useForm({
  date_type: props.config?.date_type ?? "",
  date_format: props.config?.date_format ?? "",
  time_format: props.config?.time_format ?? "",
});

const save = () => {
  form.post(route("configuration.setting.update"), {
    preserveScroll: true,

    onSuccess: () => {
      showModal.value = false;
    },
  });
};

const selectOptions = [
  {
    label: "AD Date",
    value: "ad",
  },
  {
    label: "BS Date",
    value: "bs",
  },
];

const dateOptions = [
  {
    label: "Y-m-d",
    value: "Y-m-d",
  },
  {
    label: "Y/m/d",
    value: "Y/m/d",
  },
];

const timeOptions = [
  {
    label: "12 Hour",
    value: "12hour",
  },
  {
    label: "24 Hour",
    value: "24hour",
  },
];
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
      class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10 text-primary transition-colors duration-200 group-hover:bg-primary group-hover:text-white"
    >
      <i class="fa-solid fa-calendar-days"></i>
    </div>

    <div class="text-left">
      <span class="block font-medium text-slate-800">
        Date Time Config
      </span>

      <span class="block text-xs text-slate-500 mt-0.5">
        Manage date and time display preferences
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
      <div
        class="px-6 py-4 border-b border-slate-100 bg-card_header_color"
      >
        <div class="flex items-center gap-3">
          <div
            class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10 text-primary"
          >
            <i class="fa-solid fa-calendar-days"></i>
          </div>

          <div>
            <Label class="font-bold text-xl text-slate-800">
              Date & Time Configuration
            </Label>

            <p class="text-sm text-slate-500 mt-0.5">
              Configure how dates and times are displayed across the
              application.
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
               SETTINGS
          ================================================== -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <!-- Date Type -->
            <div class="md:col-span-2">
              <Label class="font-medium text-slate-700 mb-2 block">
                Date Type
                <span class="text-red-500 ml-1">*</span>
              </Label>

              <RisingSelect
                v-model="form.date_type"
                :options="selectOptions"
                wrapperBg="bg-white"
                wrapperRounded="rounded-xl"
                wrapperClass="border border-slate-200 shadow-sm transition-all duration-200 ease-in-out outline-none hover:border-slate-300 focus:border-primary focus:ring-4 focus:ring-indigo-500/10 focus:shadow-md"
                inputClass="text-sm focus:ring-0 py-[10px] focus:outline-0"
                :primaryColor="theme?.primary"
                placeholder="Select date type"
              />

              <p class="mt-1.5 text-xs text-slate-500">
                Choose whether the application should use the
                <strong>AD</strong> or <strong>BS</strong> calendar.
              </p>
            </div>

            <!-- Date Format -->
            <div>
              <Label class="font-medium text-slate-700 mb-2 block">
                Date Format
                <span class="text-red-500 ml-1">*</span>
              </Label>

              <RisingSelect
                v-model="form.date_format"
                :options="dateOptions"
                wrapperBg="bg-white"
                wrapperRounded="rounded-xl"
                wrapperClass="border border-slate-200 shadow-sm transition-all duration-200 ease-in-out outline-none hover:border-slate-300 focus:border-primary focus:ring-4 focus:ring-indigo-500/10 focus:shadow-md"
                inputClass="text-sm focus:ring-0 py-[10px] focus:outline-0"
                :primaryColor="theme?.primary"
                placeholder="Select date format"
              />

              <p class="mt-1.5 text-xs text-slate-500">
                Example:
                <span class="font-medium text-slate-600">
                  2026-08-21
                </span>
                or
                <span class="font-medium text-slate-600">
                  2026/08/21
                </span>
              </p>
            </div>

            <!-- Time Format -->
            <div>
              <Label class="font-medium text-slate-700 mb-2 block">
                Time Format
                <span class="text-red-500 ml-1">*</span>
              </Label>

              <RisingSelect
                v-model="form.time_format"
                :options="timeOptions"
                wrapperBg="bg-white"
                wrapperRounded="rounded-xl"
                wrapperClass="border border-slate-200 shadow-sm transition-all duration-200 ease-in-out outline-none hover:border-slate-300 focus:border-primary focus:ring-4 focus:ring-indigo-500/10 focus:shadow-md"
                inputClass="text-sm focus:ring-0 py-[10px] focus:outline-0"
                :primaryColor="theme?.primary"
                placeholder="Select time format"
              />

              <p class="mt-1.5 text-xs text-slate-500">
                Choose between
                <span class="font-medium text-slate-600">
                  12-hour
                </span>
                and
                <span class="font-medium text-slate-600">
                  24-hour
                </span>
                time display.
              </p>
            </div>
          </div>

          <!-- =================================================
               INFORMATION SECTION
          ================================================== -->
          <div
            class="mt-6 rounded-xl border border-slate-200 bg-slate-50/80 p-4"
          >
            <!-- Info Header -->
            <div class="flex items-start gap-3">
              <div
                class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary"
              >
                <i class="fa-solid fa-circle-info"></i>
              </div>

              <div>
                <Label class="font-semibold text-slate-800">
                  Date & Time Preferences
                </Label>

                <p class="text-sm leading-6 text-slate-500 mt-1">
                  These preferences control how dates and times are
                  displayed throughout your application. Choose the
                  format that best matches your organization's
                  requirements.
                </p>
              </div>
            </div>

            <!-- Information Cards -->
            <div
              class="mt-4 grid grid-cols-1 md:grid-cols-3 gap-3"
            >
              <!-- Date Type Info -->
              <div
                class="rounded-lg bg-white border border-slate-200 p-3.5"
              >
                <div class="flex items-center gap-2 mb-2">
                  <div
                    class="flex h-7 w-7 items-center justify-center rounded-md bg-primary/10 text-primary"
                  >
                    <i class="fa-regular fa-calendar text-xs"></i>
                  </div>

                  <span
                    class="text-sm font-semibold text-slate-700"
                  >
                    Date Type
                  </span>
                </div>

                <p class="text-xs leading-5 text-slate-500">
                  Select AD for the Gregorian calendar or BS for the
                  Nepali Bikram Sambat calendar.
                </p>
              </div>

              <!-- Date Format Info -->
              <div
                class="rounded-lg bg-white border border-slate-200 p-3.5"
              >
                <div class="flex items-center gap-2 mb-2">
                  <div
                    class="flex h-7 w-7 items-center justify-center rounded-md bg-primary/10 text-primary"
                  >
                    <i
                      class="fa-solid fa-calendar-days text-xs"
                    ></i>
                  </div>

                  <span
                    class="text-sm font-semibold text-slate-700"
                  >
                    Date Format
                  </span>
                </div>

                <p class="text-xs leading-5 text-slate-500">
                  Define how dates appear in tables, forms, reports,
                  dashboards, and other application screens.
                </p>
              </div>

              <!-- Time Format Info -->
              <div
                class="rounded-lg bg-white border border-slate-200 p-3.5"
              >
                <div class="flex items-center gap-2 mb-2">
                  <div
                    class="flex h-7 w-7 items-center justify-center rounded-md bg-primary/10 text-primary"
                  >
                    <i class="fa-regular fa-clock text-xs"></i>
                  </div>

                  <span
                    class="text-sm font-semibold text-slate-700"
                  >
                    Time Format
                  </span>
                </div>

                <p class="text-xs leading-5 text-slate-500">
                  Use 12-hour format with AM/PM or the 24-hour
                  format for displaying time.
                </p>
              </div>
            </div>

            <!-- Global Note -->
            <div
              class="mt-4 flex items-start gap-2.5 rounded-lg bg-primary/5 border border-primary/10 px-3.5 py-3"
            >
              <i
                class="fa-solid fa-lightbulb text-primary mt-0.5 text-sm"
              ></i>

              <p class="text-xs leading-5 text-slate-600">
                <span class="font-semibold text-slate-700">
                  Tip:
                </span>
                These settings are applied globally, so you don't
                need to configure date and time formatting separately
                on individual pages.
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