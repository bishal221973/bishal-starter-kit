<script setup>
import { useTheme } from "@/composables/useTheme";
import Button from "../Elements/Button.vue";
import OutlineButton from "../Elements/OutlineButton.vue";
const { theme } = useTheme();

defineProps({
  icon: {
    type: String,
    default: "🚀",
  },

  title: {
    type: String,
    required: true,
  },

  description: {
    type: String,
    required: true,
  },

  buttonText: {
    type: String,
    default: "Copy Command",
  },

  copiedText: {
    type: String,
    default: "Copied! ✓",
  },

  isCopied: {
    type: Boolean,
    default: false,
  },
  type: {
    type: String,
    default: 'primary',
  },
  outline: {
        type: Boolean,
        default: false,
    },
});

const emit = defineEmits(["action"]);
</script>

<template>
    <!-- {{ outline }} -->
  <div
    class="bg-white border border-slate-200 rounded-2xl p-5 shadow-sm hover:shadow-md transition-all duration-300"
  >
    <div class="flex flex-col lg:flex-row lg:items-center lg:justify-between gap-5">
      <div class="flex items-start gap-4">
        <div
          class="flex h-12 w-12 items-center justify-center rounded-xl text-2xl shrink-0"
          :style="{
            backgroundColor: `${theme.primary}15`,
            border: `1px solid ${theme.primary}30`,
            color: theme.primary,
          }"
        >
          {{ icon }}
        </div>

        <div>
          <h3 class="text-lg font-bold text-slate-800">
            {{ title }}
          </h3>
          <p class="mt-2 text-sm leading-6 text-slate-600" v-html="description" />
        </div>
      </div>

      <!-- <button
        @click="emit('action')"
        class="inline-flex items-center justify-center whitespace-nowrap rounded-xl px-5 py-3 text-sm font-semibold text-white shadow-sm transition-all duration-200 hover:scale-105"
        :style="{ backgroundColor: theme.primary }"
      >
        {{ isCopied ? copiedText : buttonText }}
      </button> -->
      
      <OutlineButton :type="type" v-if="outline == true">
        <div class="inline-flex whitespace-nowrap">
          {{ isCopied ? copiedText : buttonText }}
        </div>
        <!-- {{ type }} dfds -->
      </OutlineButton >
      <Button :type="type" v-else>
        <div class="inline-flex whitespace-nowrap">
          {{ isCopied ? copiedText : buttonText }}
        </div>
        <!-- {{ type }} dfds -->
      </Button >

    </div>
  </div>
</template>
