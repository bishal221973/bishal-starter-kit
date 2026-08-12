<script setup>
import { computed } from "vue";

const props = defineProps({
  submit: {
    type: Boolean,
    default: false,
  },

  disabled: {
    type: Boolean,
    default: false,
  },

  type: {
    type: String,
    default: "primary", // primary, secondary, accent, text
  },
});
</script>

<template>
  <button v-if="type == 'primary'" class="bg-primary">
    <slot />
  </button>
  <button v-else-if="type == 'secondary'" class="bg-secondary">
    <slot />
  </button>
  <button v-else-if="type == 'accent'" class="bg-accent">
    <slot />
  </button>
  <button v-else-if="type == 'success'" class="bg-success">
    <slot />
  </button>
  <button v-else-if="type == 'warning'" class="bg-warning">
    <slot />
  </button>
  <button v-else-if="type == 'danger'" class="bg-danger">
    <slot />
  </button>
  <button v-else class="bg-info">
    <slot />
  </button>
</template>
