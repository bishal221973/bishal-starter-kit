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
  card_border_radius: props?.setting?.card_border_radius ?? 16,
  card_shadow: props?.setting?.card_shadow ?? true,
  card_header_color: props?.setting?.card_header_color ?? "#f2f2f2",
  card_footer_color: props?.setting?.card_footer_color ?? "#f2f2f2",
  card_bg_color: props?.setting?.card_bg_color ?? "#fff",
});

const save = () => {
  form.post(route("theme.setting.update"));
};
</script>
<template>
  <Card>
    <template #header>
      <Label class="font-bold mb-4 text-[19px]">Card Settings</Label>
    </template>
    <form @submit.prevent="save">
      <div class="bg-white rounded-2xl shadow-sm border p-6">
        <div class="grid grid-cols-4 gap-3">
          <div>
            <ColorPicker
              v-model="form.card_bg_color"
              type="color"
              text="Card Background"
              placeholder="Enter Card Background"
              required
            />
          </div>
          <div>
            <ColorPicker
              v-model="form.card_header_color"
              type="color"
              text="Card Header Color"
              placeholder="Enter Card Header Color"
              required
            />
          </div>

          <div>
            <ColorPicker
              v-model="form.card_footer_color"
              type="color"
              text="Card Footer Color"
              placeholder="Enter Card Footer Color"
              required
            />
          </div>

          <div>
            <TextInput
              v-model="form.card_border_radius"
              type="number"
              text="Card Border Radius"
              placeholder="Enter Card Border Radius"
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
