<script setup>
import { usePage } from "@inertiajs/vue3";
import { useTheme } from "@/composables/useTheme";

const page = usePage();
const auth = page.props.auth;

const { theme } = useTheme();
</script>

<template>
  <div
    class="rounded-3xl p-6 sm:p-10 text-white shadow-xl mb-10 relative overflow-hidden"
    :style="{
      background: `linear-gradient(135deg, ${theme.primary}, ${theme.secondary})`,
    }"
  >
    <!-- Decorative Blurs -->
    <div
      class="absolute -right-10 -bottom-10 w-40 h-40 rounded-full blur-3xl opacity-20"
      :style="{ backgroundColor: theme.accent }"
    ></div>

    <div
      class="absolute -left-10 -top-10 w-40 h-40 rounded-full blur-3xl opacity-20"
      :style="{ backgroundColor: theme.primary }"
    ></div>

    <div class="relative z-10 max-w-2xl">
      <span
        class="inline-flex items-center px-3 py-1 rounded-full text-xs font-medium border mb-4"
        :style="{
          backgroundColor: `${theme.accent}20`,
          borderColor: `${theme.accent}50`,
          color: '#ffffff',
        }"
      >
        ⚡ Setup Complete
      </span>

      <h1 class="text-3xl sm:text-4xl font-extrabold tracking-tight mb-3">
        Welcome to the Bishal Starter Kit!
      </h1>

      <p class="text-white/90 text-base sm:text-lg leading-relaxed">
        Hello {{ auth?.user?.name?.split(" ")[0] || "Developer" }}! 👋 We're thrilled to
        have you here. Let's get your perfectly optimized, built, and launched.
      </p>
    </div>
  </div>
</template>
