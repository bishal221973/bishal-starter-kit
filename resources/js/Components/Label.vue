<script setup>
const emit = defineEmits(["toggle-sidebar"]);
import { useTheme } from "@/composables/useTheme";

const { theme } = useTheme();
defineProps({
  opacity: {
    type: String,
    default: "",
  },
  color: {
    type: Boolean,
    default: true,
  },
});
</script>
<template>
  <label
    :style="
      color
        ? { color: theme.text_color + opacity, cursor: 'inherit' }
        : { cursor: 'inherit' }
    "
    class="text-current"
  >
    <slot />
  </label>
</template>
