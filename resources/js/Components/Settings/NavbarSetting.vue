<script setup>
import { useForm } from "@inertiajs/vue3";
import Label from "@/Components/Label.vue";
import TextInput from "@/Components/Elements/TextInput.vue";
import ColorPicker from "../Elements/ColorPicker.vue";
const props = defineProps({
  setting: Object,
});

const form = useForm({
  navbar_bg_color: props?.setting?.navbar_bg_color ?? "#ffffff",
  navbar_text_color: props?.setting?.navbar_text_color ?? "#1e293b",
  navbar_border_color: props?.setting?.navbar_border_color ?? "#1e293b",
  navbar_height: props?.setting?.navbar_height ?? "70",
});

const save = () => {
  form.post(route("theme.setting.update"));
};
</script>
<template>
  <form @submit.prevent="save">
    <div class="bg-white rounded-2xl shadow-sm border p-6">
      <Label class="font-semibold mb-4">Navbar</Label>

      <div class="grid grid-cols-4 gap-3">
        <div>
          <ColorPicker
            v-model="form.navbar_bg_color"
            type="color"
            text="Navbar Background"
            placeholder="Enter Navbar Background"
            required
            autofocus
          />
        </div>
        <div>
          <ColorPicker
            v-model="form.navbar_border_color"
            type="color"
            text="Navbar Border Color"
            placeholder="Enter Navbar Border Color"
            required
            autofocus
          />
        </div>

        <div>
          <ColorPicker
            v-model="form.navbar_text_color"
            type="color"
            text="Navbar Text Color"
            placeholder="Enter Navbar Text Color"
            required
            autofocus
          />
        </div>

        <div>
          <TextInput
            v-model="form.navbar_height"
            type="number"
            text="Height"
            placeholder="Enter Height"
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
