<script setup>
import { onMounted, useTemplateRef } from "vue";
import Label from "../Label.vue";

defineProps({
  text: String,
  required: {
    type: Boolean,
    default: false,
  },
  inputClass: {
    type: [String, Array, Object],
    default: "",
  },
});
// Modern Vue 3.4+ two-way binding
const model = defineModel({ type: String, default: "" });

// Modern Vue 3.5+ template ref utility
const inputRef = useTemplateRef("inputElement");

onMounted(() => {
  if (inputRef.value?.hasAttribute("autofocus")) {
    inputRef.value.focus();
  }
});

defineExpose({ focus: () => inputRef.value?.focus() });
</script>

<template>
  <div class="w-full">
    <Label v-if="text" class="font-medium text-sm mb-[7px] block">
      {{ text }}
      <span v-if="required" class="text-red-500 ml-1"> * </span>
    </Label>
    <div class="relative w-full group">
      <input
        ref="inputElement"
        v-model="model"
        type="text"
        class="w-full px-4 py-[9px] bg-white border border-primary rounded-xl text-slate-900 placeholder-slate-400 text-sm shadow-sm transition-all duration-200 ease-in-out outline-none hover:border-slate-300 focus:border-primary focus:ring-4 focus:ring-indigo-500/10 focus:shadow-md"
        v-bind="$attrs"
        :class="inputClass"
      />
    </div>
  </div>
</template>
