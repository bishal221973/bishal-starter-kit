<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import Label from "@/Components/Label.vue";
import ThemeColor from "@/Components/Settings/ThemeColor.vue";
import SidebarSetting from "@/Components/Settings/SidebarSetting.vue";
import NavbarSetting from "@/Components/Settings/NavbarSetting.vue";
import CardSetting from "@/Components/Settings/CardSetting.vue";
import SettingMenu from "./SettingMenu.vue";
const props = defineProps({
  setting: Object,
});
</script>

<template>
  <AppLayout>
    <div class="mx-auto">
      <div class="mb-6">
        <Label class="text-2xl font-bold">Theme Settings</Label>
        <Label class="text-slate-500 block" opacity="80"
          >Customize the appearance of your application.</Label
        >
      </div>

      <div class="flex gap-3">
        <SettingMenu />
        <div class="w-full">
          <ThemeColor :setting="setting" />
          <SidebarSetting :setting="setting" />
          <NavbarSetting :setting="setting" />
          <CardSetting :setting="setting" />
        </div>
      </div>
    </div>
  </AppLayout>
</template>
