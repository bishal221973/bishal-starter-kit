<script setup>
import { useForm } from "@inertiajs/vue3";
import Label from "@/Components/Label.vue";
import TextInput from "@/Components/Elements/TextInput.vue";
import Card from "../Elements/Card.vue";
import Button from "../Elements/Button.vue";
import Modal from "../Modal.vue";
import { ref } from "vue";
import { RisingSelect } from "rising-select";
import { useTheme } from "@/composables/useTheme.js";
import { RisingPicker } from "rising-picker";
import FileBrows from "../FileBrows.vue";
const props = defineProps({
  config: Object,
});
const { theme } = useTheme();

const showModal = ref(false);

const toggleModal = () => {
  showModal.value = !showModal.value;
};

const form = useForm({
  enable_login_attempt_limit: props?.config?.enable_login_attempt_limit ?? "",
  max_login_attempts: props?.config?.max_login_attempts ?? "",
  login_lockout_duration: props?.config?.login_lockout_duration ?? "",
});

const save = () => {
  form.post(route("configuration.setting.update"), {
    onSuccess: () => {
      showModal.value = false;
    },
  });
};

const selectOptions = [
  { label: "AD Date", value: "ad" },
  { label: "BS Date", value: "bs" },
];
const dateOptions = [
  { label: "Y-m-d", value: "Y-m-d" },
  { label: "Y/m/d", value: "Y/m/d" },
];
const timeOptions = [
  { label: "12 Hour", value: "12hour" },
  { label: "24 Hour", value: "24hour" },
];
</script>

<template>
  <button
    @click="toggleModal"
    class="flex items-center gap-3 w-full p-4 rounded-xl border border-slate-200 hover:border-primary hover:shadow-sm transition"
  >
    <Label class="flex gap-3 justify-center items-center">
      <i class="fa-solid fa-user-shield"></i>
      <span class="font-medium">Login / Logout Config</span>
    </Label>
  </button>
  <Modal :show="showModal">
    <div class="mb-5">
      <div class="px-6 py-3 border-b border-slate-100 bg-card_header_color">
        <div>
          <Label class="font-bold text-xl"> Login / Logout Config </Label>
        </div>
      </div>

      <div class="p-3">
        <form @submit.prevent="save">
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <div class="col-span-2">
              <label
                class="flex items-center gap-3 border rounded-xl px-4 py-2 cursor-pointer"
              >
                <input
                  v-model="form.enable_login_attempt_limit"
                  type="checkbox"
                  class="w-5 h-5 rounded"
                />

                <div>
                  <Label class="font-medium">Enable Login Attempt Limit</Label>
                  <Label class="text-sm text-slate-500 block" opacity="80">
                    Enable limit for login
                  </Label>
                </div>
              </label>
            </div>
            <div>
              <Label class="font-medium text-slate-700 mb-[7px] block">
                Date Format
                <span class="text-red-500 ml-1"> * </span>
              </Label>
              <RisingSelect
                v-model="form.date_format"
                :options="dateOptions"
                wrapperBg="bg-white"
                wrapperRounded="rounded-xl"
                wrapperClass="border border-primary shadow-sm transition-all duration-200 ease-in-out outline-none hover:border-slate-300 focus:border-primary focus:ring-4 focus:ring-indigo-500/10 focus:shadow-md"
                inputClass="text-sm focus:ring-0 py-[10px] focus:outline-0"
                :primaryColor="theme?.primary"
                placeholder="Backup Frequency"
              />
            </div>
            <div>
              <Label class="font-medium text-slate-700 mb-[7px] block">
                Time Format
                <span class="text-red-500 ml-1"> * </span>
              </Label>
              <RisingSelect
                v-model="form.time_format"
                :options="timeOptions"
                wrapperBg="bg-white"
                wrapperRounded="rounded-xl"
                wrapperClass="border border-primary shadow-sm transition-all duration-200 ease-in-out outline-none hover:border-slate-300 focus:border-primary focus:ring-4 focus:ring-indigo-500/10 focus:shadow-md"
                inputClass="text-sm focus:ring-0 py-[10px] focus:outline-0"
                :primaryColor="theme?.primary"
                placeholder="Backup Frequency"
              />
            </div>
          </div>

          <!-- Footer -->
          <div class="mt-6 flex gap-3 justify-end border-t pt-4">
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
