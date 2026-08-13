<script setup>
import { useForm } from "@inertiajs/vue3";
import Label from "@/Components/Label.vue";
import TextInput from "@/Components/Elements/TextInput.vue";
import ColorPicker from "../Elements/ColorPicker.vue";
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
  <form @submit.prevent="save">
    <div class="bg-white rounded-2xl shadow-sm border p-6">
      <Label class="font-semibold mb-4">Sidebar</Label>

      <div class="grid grid-cols-3 gap-4">
        <div>
          <ColorPicker
            v-model="form.sidebar_bg_color"
            type="color"
            text="Background Color"
            placeholder="Enter Background Color"
            required
            autofocus
          />
        </div>

        <div>
          <ColorPicker
            v-model="form.sidebar_text_color"
            type="color"
            text="Text Color"
            placeholder="Enter Text Color"
            required
            autofocus
          />
        </div>
        <div>
          <ColorPicker
            v-model="form.sidebar_hover_color"
            type="color"
            text="Hover Background Color"
            placeholder="Enter Hover Background Color"
            required
            autofocus
          />
        </div>
        <div>
          <ColorPicker
            v-model="form.sidebar_hover_text_color"
            type="color"
            text="Hover Text Color"
            placeholder="Enter Hover Text Color"
            required
            autofocus
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
