<script setup>
import { useForm } from "@inertiajs/vue3";
import Label from "@/Components/Label.vue";
import ColorPicker from "../Elements/ColorPicker.vue";
import Card from "../Elements/Card.vue";
import Button from "../Elements/Button.vue";
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
  <Card class="mb-5">
    <template #header>
      <Label class="font-bold mb-4 text-[19px]">Theme Colors</Label>
    </template>
    <form @submit.prevent="save">
      <div class="">
        <div class="grid grid-cols-3 gap-4">
          <div>
            <ColorPicker
              v-model="form.primary_color"
              type="color"
              text="Primary"
              placeholder="Enter Primary Color"
              required
            />
          </div>

          <div>
            <ColorPicker
              v-model="form.secondary_color"
              type="color"
              text="Secondary"
              placeholder="Enter Secondary Color"
              required
            />
          </div>

          <div>
            <ColorPicker
              v-model="form.accent_color"
              type="color"
              text="Accent"
              placeholder="Enter Accent Color"
              required
            />
          </div>

          <div>
            <ColorPicker
              v-model="form.success_color"
              type="color"
              text="Success"
              placeholder="Enter Success Color"
              required
            />
          </div>

          <div>
            <ColorPicker
              v-model="form.warning_color"
              type="color"
              text="Warning"
              placeholder="Enter Warning Color"
              required
            />
          </div>

          <div>
            <ColorPicker
              v-model="form.danger_color"
              type="color"
              text="Danger"
              placeholder="Enter Danger Color"
              required
            />
          </div>

          <div>
            <ColorPicker
              v-model="form.info_color"
              type="color"
              text="Info"
              placeholder="Enter Info Color"
              required
            />
          </div>

          <div>
            <ColorPicker
              v-model="form.background_color"
              type="color"
              text="Background"
              placeholder="Enter Background Color"
              required
            />
          </div>
          <div>
            <ColorPicker
              v-model="form.text_color"
              type="color"
              text="Text Color"
              placeholder="Enter Text Color"
              required
            />
          </div>
        </div>
      </div>
      <div class="mt-6 flex justify-end bg-[#f3f3f3] p-2 rounded-xl">
        <Button
          :submit="true"
          :text="form.processing ? 'Saveing...' : 'Save Settings'"
          :processing="form.processing"
          :disabled="form.processing"
        />
      </div>
    </form>
  </Card>
</template>
