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
  sidebar_bg_color: props.setting?.sidebar_bg_color ?? "#0f172a",
  sidebar_text_color: props?.setting?.sidebar_text_color ?? "#ffffff",
  sidebar_hover_color: props?.setting?.sidebar_hover_color ?? "#1e293b",
  sidebar_hover_text_color: props?.setting?.sidebar_hover_text_color ?? "#1e293b",
  sidebar_active_color: props?.setting?.sidebar_active_color ?? "#3d98aa",
  sidebar_width: props?.setting?.sidebar_width ?? "280",
  sidebar_position: props?.setting?.sidebar_position ?? "left",
});

const save = () => {
  form.post(route("theme.setting.update"));
};
</script>
<template>
  <Card>
  <template #header>
      <Label class="font-bold mb-4 text-[19px]">Sidebar Settings</Label>
    </template>
    <form @submit.prevent="save">
      <div class="bg-white rounded-2xl shadow-sm border p-6">
       
        <div class="grid grid-cols-3 gap-4">
          <div>
            <ColorPicker
              v-model="form.sidebar_bg_color"
              type="color"
              text="Background Color"
              placeholder="Enter Background Color"
              required
            />
          </div>

          <div>
            <ColorPicker
              v-model="form.sidebar_text_color"
              type="color"
              text="Text Color"
              placeholder="Enter Text Color"
              required
            />
          </div>
          <div>
            <ColorPicker
              v-model="form.sidebar_hover_color"
              type="color"
              text="Hover Background Color"
              placeholder="Enter Hover Background Color"
              required
            />
          </div>
          <div>
            <ColorPicker
              v-model="form.sidebar_hover_text_color"
              type="color"
              text="Hover Text Color"
              placeholder="Enter Hover Text Color"
              required
            />
          </div>
          <div>
            <label>Hover Text Color</label>

            <select name="" id="" class="w-full h-12" v-model="form.sidebar_position">
              <option value="left">Left</option>
              <option value="right">Right</option>
              <option value="top">Top</option>
            </select>
          </div>

          <div>
            <TextInput
              v-model="form.sidebar_width"
              type="number"
              text="Width"
              placeholder="Enter Width"
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
