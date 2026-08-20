<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import PasswordConfig from "@/Components/Configurations/PasswordConfig.vue";
import BackupConfig from "@/Components/Configurations/BackupConfig.vue";
import SettingMenu from "./SettingMenu.vue";
import ScreenSaverConfig from "@/Components/Configurations/ScreenSaverConfig.vue";
import DateTimeConfig from "@/Components/Configurations/DateTimeConfig.vue";
import { ref } from "vue";
import { useTheme } from "@/composables/useTheme.js";
import LoginLogoutConfig from "@/Components/Configurations/LoginLogoutConfig.vue";
import IpConfig from "@/Components/Configurations/IpConfig.vue";
import AppConfig from "@/Components/Configurations/AppConfig.vue";
import FooterConfig from "@/Components/Configurations/FooterConfig.vue";
import UserConfig from "@/Components/Configurations/UserConfig.vue";
import LicenceConfig from "@/Components/Configurations/LicenceConfig.vue";
import PageHeader from "@/Components/PageHeader.vue";
import ZoomControl from "@/Components/Configurations/ZoomControl.vue";
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
      <PageHeader
        description="Manage your application's settings and customization options."
        :breadcrumbs="[
          {
            label: 'Configuration',
            href: route('configuration.setting'),
          },
          {
            label: 'Configuration',
          },
        ]"
      />

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
            <div>
              <DateTimeConfig :config="config" />
            </div>
            <div>
              <LoginLogoutConfig :config="config" />
            </div>
            <div>
              <IpConfig :config="config" />
            </div>

            <div>
              <AppConfig :config="config" />
            </div>
            <div>
              <FooterConfig :config="config" />
            </div>
            <div>
              <UserConfig :config="config" />
            </div>
            <div>
              <LicenceConfig :config="config" />
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
