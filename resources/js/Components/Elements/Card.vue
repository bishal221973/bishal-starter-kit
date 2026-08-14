<script setup>
import { useTheme } from "@/composables/useTheme";
const { theme } = useTheme();
</script>
<template>
  <div
    class="bg-card_bg_color border shadow-md border-slate-200 overflow-hidden"
    :style="{
      borderRadius: theme.card_border_radius + 'px',
      ...(theme.card_shadow ? { boxShadow: 'rgba(0, 0, 0, 0.35) 0px 5px 15px;' } : {}),
    }"
  >
    <!-- Header -->
    <div
      v-if="$slots.header"
      class="px-6 py-2 border-b border-slate-100 bg-card_header_color"
    >
      <slot name="header" />
    </div>

    <!-- Body -->
    <div class="p-6">
      <slot />
    </div>

    <!-- Footer -->
    <div
      v-if="$slots.footer"
      class="px-6 py-4 border-t border-slate-100 bg-card_footer_color"
    >
      <slot name="footer" />
    </div>
  </div>
</template>
