<script setup>
import { useForm } from "@inertiajs/vue3";
import Label from "@/Components/Label.vue";
import TextInput from "@/Components/Elements/TextInput.vue";
import ColorPicker from "../Elements/ColorPicker.vue";
import Card from "../Elements/Card.vue";
import Button from "../Elements/Button.vue";
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
  <Card class="mb-5">
    <template #header>
      <Label class="font-bold mb-4 text-[19px]">Navbar Settings</Label>
    </template>
    <form @submit.prevent="save">
      <div>
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
