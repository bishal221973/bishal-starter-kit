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

const props = defineProps({
  config: Object,
});
const { theme } = useTheme();

const showModal = ref(false);

const toggleModal = () => {
  showModal.value = !showModal.value;
};

const form = useForm({
  enable_auto_backup: props?.config?.enable_auto_backup == 1 ? true : false,
  backup_frequency: props?.config?.backup_frequency ?? "daily",
  backup_retention_days: props?.config?.backup_retention_days ?? 30,
});

const save = () => {
  form.post(route("configuration.setting.update"), {
    onSuccess: () => {
      showModal.value = false;
    },
  });
};

const options = [
  { label: "Daily", value: "daily" },
  { label: "Weekly", value: "weekly" },
  { label: "Monthly", value: "monthly" },
];
</script>

<template>
  <button
    @click="toggleModal"
    class="flex items-center gap-3 w-full p-4 rounded-xl border border-slate-200 hover:border-primary hover:shadow-sm transition"
  >
    <Label class="flex gap-3 justify-center items-center">
      <i class="fa-solid fa-user-shield"></i>
      <span class="font-medium">Backup Config</span>
    </Label>
  </button>
  <Modal :show="showModal">
    <div class="mb-5">
      <div class="px-6 py-3 border-b border-slate-100 bg-card_header_color">
        <div>
          <Label class="font-bold text-xl"> Backup Config </Label>
        </div>
      </div>

      <div class="p-3">
        <form @submit.prevent="save">
          <!-- Enable Policy -->
          <label
            for="enable_password_policy"
            class="flex items-center justify-between border rounded-xl p-3 mb-3 cursor-pointer hover:bg-slate-50 transition"
          >
            <div>
              <Label for="enable_password_policy" class="font-medium">
                Enable Auto Config
              </Label>

              <Label class="text-sm text-slate-500 mt-1 block" opacity="80">
                Automatically create scheduled backups to protect your application data
                and ensure recovery when needed.
              </Label>
            </div>

            <input
              id="enable_auto_backup"
              v-model="form.enable_auto_backup"
              type="checkbox"
              class="w-5 h-5 rounded cursor-pointer"
            />
          </label>

          <div v-if="form.enable_auto_backup" class="space-y-2">
            <div>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div>
                  <Label class="font-medium text-slate-700 mb-[7px] block">
                    Backup Frequency
                    <span class="text-red-500 ml-1"> * </span>
                  </Label>
                  <RisingSelect
                    v-model="form.backup_frequency"
                    :options="options"
                    wrapperBg="bg-white"
                    wrapperRounded="rounded-xl"
                    wrapperClass="border border-primary shadow-sm transition-all duration-200 ease-in-out outline-none hover:border-slate-300 focus:border-primary focus:ring-4 focus:ring-indigo-500/10 focus:shadow-md"
                    inputClass="text-sm focus:ring-0 py-[10px] focus:outline-0"
                    :primaryColor="theme?.primary"
                    placeholder="Backup Frequency"
                  />
                </div>

                <TextInput
                  v-model="form.backup_retention_days"
                  type="number"
                  step="1"
                  text="Backup Retention Days"
                  placeholder="Backup Retention Days"
                />
              </div>
              <Label class="text-slate-500 block mt-10" opacity="80">
                Configure automatic backups, retention policies, and data recovery
                settings to ensure your application data remains secure and protected.
              </Label>
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
