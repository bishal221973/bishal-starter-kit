<script setup>
import { useForm } from "@inertiajs/vue3";
import Label from "@/Components/Label.vue";
import TextInput from "@/Components/Elements/TextInput.vue";
import Card from "../Elements/Card.vue";
import Button from "../Elements/Button.vue";
import Modal from "../Modal.vue";
import { ref } from "vue";
const props = defineProps({
  config: Object,
});

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

          <Label class="text-slate-500 block">
            Configure automatic backups, retention policies, and data recovery settings to
            ensure your application data remains secure and protected.
          </Label>
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
                <TextInput
                  v-model="form.backup_frequency"
                  type="text"
                  text="Backup Frequency"
                  placeholder="Backup Frequency"
                  required
                />

                <TextInput
                  v-model="form.backup_retention_days"
                  type="text"
                  text="Backup Frequency"
                  placeholder="Backup Frequency"
                />
              </div>
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
