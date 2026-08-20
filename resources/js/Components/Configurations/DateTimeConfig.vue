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
      <Label class="block font-medium text-sm"> Date Time Config </Label>

      <Label class="block text-[12px] opacity-60 mt-0.5">
        Manage date and time display preferences
      </Label>
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
            class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10 text-primary"
          >
            <i class="fa-solid fa-calendar-days"></i>
          </div>

          <div>
            <Label class="font-bold text-lg text-slate-800">
              Date & Time Configuration
            </Label>

            <Label class="block text-xs opacity-70 mt-0.5">
              Configure how dates and times are displayed across the application.
            </Label>
          </div>
        </div>
      </div>

      <!-- =====================================================
           BODY
      ====================================================== -->
      <div class="px-5 py-3">
        <form @submit.prevent="save">
          <!-- =================================================
               SETTINGS
          ================================================== -->
          <div class="grid grid-cols-1 md:grid-cols-2 gap-5">
            <!-- Date Type -->
            <div class="md:col-span-2">
              <Label class="font-medium text-sm block">
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

              <Label class="block text-xs opacity-70 mt-1">
                Choose whether the application should use the
                <strong>AD</strong> or <strong>BS</strong> calendar.
              </Label>
            </div>

            <!-- Date Format -->
            <div>
              <Label class="font-medium text-sm block">
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

              <Label class="block text-xs opacity-70 mt-1">
                Example:
                <Label class="text-xs opacity-70"> 2026-08-21 </Label>
                or
                <Label class="text-xs opacity-70"> 2026/08/21 </Label>
              </Label>
            </div>

            <!-- Time Format -->
            <div>
              <Label class="font-medium text-sm block">
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

              <Label class="block text-xs opacity-70 mt-1">
                Choose between
                <Label class="text-xs opacity-70"> 12-hour </Label>
                and
                <Label class="text-xs opacity-70"> 24-hour </Label>
                time display.
              </Label>
            </div>
          </div>

          <!-- =================================================
               INFORMATION SECTION
          ================================================== -->
          <div
            class="mt-4 flex items-start gap-2.5 rounded-lg bg-primary/5 border border-primary/10 px-3.5 py-3"
          >
            <i class="fa-solid fa-lightbulb text-primary mt-0.5 text-sm"></i>

            <Label class="text-xs leading-5 text-slate-600">
              <Label class="font-semibold text-slate-700"> Tip: </Label>
              <span class="opacity-60"
                >These settings are applied globally, so you don't need to configure date
                and time formatting separately on individual pages.</span
              >
            </Label>
          </div>

          <!-- =================================================
               FOOTER
          ================================================== -->
          <div
            class="flex flex-col-reverse sm:flex-row gap-3 justify-end border-t border-slate-100 pt-4"
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
