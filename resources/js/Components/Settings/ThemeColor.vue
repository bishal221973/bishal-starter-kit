<script setup>
import { useForm } from "@inertiajs/vue3";
import Label from "@/Components/Label.vue";
import TextInput from "@/Components/Elements/TextInput.vue";

const props = defineProps({
  setting: Object,
});

const form = useForm({
  primary_color: props.setting?.primary_color ?? "#3d98aa",
  secondary_color: props?.setting?.secondary_color ?? "#2f7f8f",
  accent_color: props?.setting?.accent_color ?? "#4fb6c8",
  success_color: props?.setting?.success_color ?? "#22c55e",
  warning_color: props?.setting?.warning_color ?? "#f59e0b",
  danger_color: props?.setting?.danger_color ?? "#ef4444",
  info_color: props?.setting?.info_color ?? "#06b6d4",

  background_color: props?.setting?.background_color ?? "#ffffff",
  text_color: props?.setting?.text_color ?? "#1e293b",
});

const save = () => {
  form.post(route("theme.setting.update"));
};
</script>
<template>
  <form @submit.prevent="save">
    <div class="bg-white rounded-2xl shadow-sm border p-6">
      <Label class="font-semibold mb-4">Theme Colors</Label>

      <div class="grid grid-cols-2 gap-4">
        <div>
          <TextInput
            id="email"
            v-model="form.primary_color"
            type="color"
            text="Primary"
            placeholder="Enter Primary Color"
            required
            autofocus
          />
        </div>

        <div>
          <TextInput
            id="email"
            v-model="form.secondary_color"
            type="color"
            text="Secondary"
            placeholder="Enter Secondary Color"
            required
            autofocus
          />
        </div>

        <div>
          <TextInput
            id="email"
            v-model="form.accent_color"
            type="color"
            text="Accent"
            placeholder="Enter Accent Color"
            required
            autofocus
          />
        </div>

        <div>
          <TextInput
            id="email"
            v-model="form.success_color"
            type="color"
            text="Success"
            placeholder="Enter Success Color"
            required
            autofocus
          />
        </div>

        <div>
          <TextInput
            id="email"
            v-model="form.warning_color"
            type="color"
            text="Warning"
            placeholder="Enter Warning Color"
            required
            autofocus
          />
        </div>

        <div>
          <TextInput
            id="email"
            v-model="form.danger_color"
            type="color"
            text="Danger"
            placeholder="Enter Danger Color"
            required
            autofocus
          />
        </div>

        <div>
          <TextInput
            id="email"
            v-model="form.info_color"
            type="color"
            text="Info"
            placeholder="Enter Info Color"
            required
            autofocus
          />
        </div>

        <div>
          <TextInput
            id="email"
            v-model="form.background_color"
            type="color"
            text="Background"
            placeholder="Enter Background Color"
            required
            autofocus
          />
        </div>
        <div>
          <TextInput
            id="email"
            v-model="form.text_color"
            type="color"
            text="Text Color"
            placeholder="Enter Text Color"
            required
            autofocus
          />
        </div>
      </div>
    </div>
    <div class="mt-6">
      <button type="submit" class="px-6 py-3 rounded-xl bg-primary text-white">
        Save Settings
      </button>
    </div>
  </form>
</template>
