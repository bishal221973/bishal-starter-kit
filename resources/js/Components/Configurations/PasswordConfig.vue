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
  enable_password_policy: props?.config?.enable_password_policy == 1 ? true : false,
  minimum_password_length: props?.config?.minimum_password_length ?? 8,
  require_uppercase: props?.config?.require_uppercase == 1 ? true : false,
  require_lowercase: props?.config?.require_lowercase == 1 ? true : false,
  require_number: props?.config?.require_number == 1 ? true : false,
  require_special_character: props?.config?.require_special_character == 1 ? true : false,
  password_expiry_days: props?.config?.password_expiry_days ?? 90,
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
      <span class="font-medium">Password Policy</span>
    </Label>
  </button>
  <Modal :show="showModal">
    <div class="mb-5">
      <div class="px-6 py-3 border-b border-slate-100 bg-card_header_color">
        <div>
          <Label class="font-bold text-xl"> Password Policy </Label>

          <Label class="text-slate-500 block">
            Configure password strength requirements and expiration rules for user
            accounts.
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
                Enable Password Policy
              </Label>

              <Label class="text-sm text-slate-500 mt-1 block" opacity="80">
                Enforce password complexity and expiry requirements.
              </Label>
            </div>

            <input
              id="enable_password_policy"
              v-model="form.enable_password_policy"
              type="checkbox"
              class="w-5 h-5 rounded cursor-pointer"
            />
          </label>

          <div v-if="form.enable_password_policy" class="space-y-2">
            <div>
              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <TextInput
                  v-model="form.minimum_password_length"
                  type="number"
                  text="Minimum Password Length"
                  placeholder="Minimum length"
                  min="4"
                  max="100"
                  required
                />

                <TextInput
                  v-model="form.password_expiry_days"
                  type="number"
                  text="Password Expiry (Days)"
                  placeholder="Expiry days"
                  min="0"
                />
              </div>
            </div>

            <!-- Password Requirements -->
            <div>
              <Label class="font-semibold text-base mb-2 block">
                Password Requirements
              </Label>

              <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <label
                  class="flex items-center gap-3 border rounded-xl px-4 py-2 cursor-pointer"
                >
                  <input
                    v-model="form.require_uppercase"
                    type="checkbox"
                    class="w-5 h-5 rounded"
                  />

                  <div>
                    <Label class="font-medium">Uppercase Letters</Label>
                    <Label class="text-sm text-slate-500 block" opacity="80">
                      Require at least one A-Z character.
                    </Label>
                  </div>
                </label>

                <label
                  class="flex items-center gap-3 border rounded-xl px-4 py-2 cursor-pointer"
                >
                  <input
                    v-model="form.require_lowercase"
                    type="checkbox"
                    class="w-5 h-5 rounded"
                  />

                  <div>
                    <Label class="font-medium">Lowercase Letters</Label>
                    <Label class="text-sm text-slate-500 block" opacity="80">
                      Require at least one a-z character.
                    </Label>
                  </div>
                </label>

                <label
                  class="flex items-center gap-3 border rounded-xl px-4 py-2 cursor-pointer"
                >
                  <input
                    v-model="form.require_number"
                    type="checkbox"
                    class="w-5 h-5 rounded"
                  />

                  <div>
                    <Label class="font-medium">Numbers</Label>
                    <Label class="text-sm text-slate-500 block" opacity="80">
                      Require at least one numeric character.
                    </Label>
                  </div>
                </label>

                <label
                  class="flex items-center gap-3 border rounded-xl px-4 py-2 cursor-pointer"
                >
                  <input
                    v-model="form.require_special_character"
                    type="checkbox"
                    class="w-5 h-5 rounded"
                  />

                  <div>
                    <Label class="font-medium">Special Characters</Label>
                    <Label class="text-sm text-slate-500 block" opacity="80">
                      Require symbols such as !@#$%^&*.
                    </Label>
                  </div>
                </label>
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
