<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import Label from "@/Components/Label.vue";
import ThemeColor from "@/Components/Settings/ThemeColor.vue";
import SidebarSetting from "@/Components/Settings/SidebarSetting.vue";
import NavbarSetting from "@/Components/Settings/NavbarSetting.vue";
import CardSetting from "@/Components/Settings/CardSetting.vue";
import SettingMenu from "./SettingMenu.vue";
import PageHeader from "@/Components/PageHeader.vue";
const props = defineProps({
  setting: Object,
});
</script>

<template>
  <AppLayout>
    <div class="mx-auto">
      <PageHeader
        description="Customize the appearance of your application."
        :breadcrumbs="[
          {
            label: 'Settings',
            href: route('theme.setting'),
          },
          {
            label: 'Theme Settings',
          },
        ]"
      />

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
