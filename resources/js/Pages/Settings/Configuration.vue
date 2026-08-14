<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import Label from "@/Components/Label.vue";
import PasswordConfig from "@/Components/Configurations/PasswordConfig.vue";
import BackupConfig from "@/Components/Configurations/BackupConfig.vue";
import SettingMenu from "./SettingMenu.vue";
import ScreenSaverConfig from "@/Components/Configurations/ScreenSaverConfig.vue";
import { ref } from "vue";
import { useTheme } from "@/composables/useTheme.js";
const props = defineProps({
  config: Object,
});

const selectedUsers = ref([]);

const { theme } = useTheme();
// console.log(RisingSelect)
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
          <div class="grid grid-cols-3 gap-4">
            <div>
              <PasswordConfig :config="config" />
            </div>
            <div>
              <BackupConfig :config="config" />
            </div>
            <div>
              <ScreenSaverConfig :config="config" />
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
