<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import Label from "@/Components/Label.vue";
import PasswordConfig from "@/Components/Configurations/PasswordConfig.vue";
import SettingMenu from "./SettingMenu.vue";
const props = defineProps({
  config: Object,
});
</script>
<template>
  <AppLayout>
    <div class="mx-auto">
      <div class="mb-6">
        <Label class="text-2xl font-bold">Configuration</Label>
        <Label class="text-slate-500 block" opacity="80">
          Manage your application's settings and customization options.
        </Label>
      </div>

      <div class="flex gap-3">
        <SettingMenu />
        <div class="w-full">
            <PasswordConfig :config="config"/>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
