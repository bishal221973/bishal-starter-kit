<script setup>
import { Link } from "@inertiajs/vue3";
import Card from "@/Components/Elements/Card.vue";
import Label from "@/Components/Label.vue";
import { useTheme } from "@/composables/useTheme";

const { theme } = useTheme();
</script>

<template>
  <Card
    class="block w-[300px] sticky top-0"
    :style="{
      height: `calc(100vh - ${theme.navbar_height + 25}px)`,
    }"
  >
    <template #header>
      <Label class="font-bold text-lg"> Settings </Label>
    </template>

    <div class="space-y-1">
      <Link
        :href="route('theme.setting')"
        class="group flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-primary transition-all duration-200"
      >
        <i class="fas fa-palette w-5 group-hover:text-white"></i>
        <Label class="group-hover:!text-white text-pointer"> Theme Settings </Label>
      </Link>

      <Link
        :href="route('configuration.setting')"
        class="group flex items-center gap-3 px-4 py-3 rounded-xl hover:bg-primary transition-all duration-200"
      >
        <i class="fas fa-cogs w-5 group-hover:text-white"></i>
        <Label class="group-hover:!text-white text-pointer"> Configuration </Label>
      </Link>
    </div>
  </Card>
</template>
