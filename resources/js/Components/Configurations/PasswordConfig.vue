<script setup>
import { useForm } from "@inertiajs/vue3";
import Label from "@/Components/Label.vue";
import TextInput from "@/Components/Elements/TextInput.vue";
import Card from "../Elements/Card.vue";
import Button from "../Elements/Button.vue";

const props = defineProps({
  config: Object,
});

const form = useForm({
  enable_password_policy: props?.config?.enable_password_policy ?? false,
  minimum_password_length: props?.config?.minimum_password_length ?? 8,
  require_uppercase: props?.config?.require_uppercase ?? false,
  require_lowercase: props?.config?.require_lowercase ?? false,
  require_number: props?.config?.require_number ?? false,
  require_special_character: props?.config?.require_special_character ?? false,
  password_expiry_days: props?.config?.password_expiry_days ?? 90,
});

const save = () => {
  form.post(route("security.setting.update"));
};
</script>

<template>
  <Card class="mb-5">
    <template #header>
      <div>
        <Label class="font-bold text-xl"> Password Policy </Label>

        <Label class="text-slate-500 block mt-1">
          Configure password strength requirements and expiration rules for user accounts.
        </Label>
      </div>
    </template>

    <form @submit.prevent="save">
      <!-- Enable Policy -->
      <label
        for="enable_password_policy"
        class="flex items-center justify-between border rounded-xl p-4 mb-6 cursor-pointer hover:bg-slate-50 transition"
      >
        <div>
          <Label for="enable_password_policy" class="font-medium">
            Enable Password Policy
          </Label>

          <p class="text-sm text-slate-500 mt-1">
            Enforce password complexity and expiry requirements.
          </p>
        </div>

        <input
          id="enable_password_policy"
          v-model="form.enable_password_policy"
          type="checkbox"
          class="w-5 h-5 rounded cursor-pointer"
        />
      </label>

      <div v-if="form.enable_password_policy" class="space-y-6">
        <!-- General Settings -->
        <div>
          <Label class="font-semibold text-base mb-4 block"> General Settings </Label>

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
          <Label class="font-semibold text-base mb-4 block">
            Password Requirements
          </Label>

          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <label class="flex items-center gap-3 border rounded-xl p-4 cursor-pointer">
              <input
                v-model="form.require_uppercase"
                type="checkbox"
                class="w-5 h-5 rounded"
              />

              <div>
                <p class="font-medium">Uppercase Letters</p>
                <p class="text-sm text-slate-500">Require at least one A-Z character.</p>
              </div>
            </label>

            <label class="flex items-center gap-3 border rounded-xl p-4 cursor-pointer">
              <input
                v-model="form.require_lowercase"
                type="checkbox"
                class="w-5 h-5 rounded"
              />

              <div>
                <p class="font-medium">Lowercase Letters</p>
                <p class="text-sm text-slate-500">Require at least one a-z character.</p>
              </div>
            </label>

            <label class="flex items-center gap-3 border rounded-xl p-4 cursor-pointer">
              <input
                v-model="form.require_number"
                type="checkbox"
                class="w-5 h-5 rounded"
              />

              <div>
                <p class="font-medium">Numbers</p>
                <p class="text-sm text-slate-500">
                  Require at least one numeric character.
                </p>
              </div>
            </label>

            <label class="flex items-center gap-3 border rounded-xl p-4 cursor-pointer">
              <input
                v-model="form.require_special_character"
                type="checkbox"
                class="w-5 h-5 rounded"
              />

              <div>
                <p class="font-medium">Special Characters</p>
                <p class="text-sm text-slate-500">Require symbols such as !@#$%^&*.</p>
              </div>
            </label>
          </div>
        </div>
      </div>

      <!-- Footer -->
      <div class="mt-6 flex justify-end border-t pt-4">
        <Button
          :submit="true"
          :text="form.processing ? 'Saving...' : 'Save Settings'"
          :processing="form.processing"
          :disabled="form.processing"
        />
      </div>
    </form>
  </Card>
</template>
