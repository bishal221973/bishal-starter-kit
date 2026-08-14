<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import Label from "@/Components/Label.vue";
import PasswordConfig from "@/Components/Configurations/PasswordConfig.vue";
import BackupConfig from "@/Components/Configurations/BackupConfig.vue";
import SettingMenu from "./SettingMenu.vue";
import Card from "@/Components/Elements/Card.vue";
// import Select from "@/Components/Elements/Select.vue";
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
              
              <!-- {{ selectedUsers }} -->
              <!-- {{ theme?.primary }} -->
              <!-- <Select
                v-model="selectedUsers"
                :options="options"
                multiple
                wrapperBg="bg-white"
                wrapperRounded="rounded-xl"
                wrapperClass="border border-primary  shadow-sm transition-all duration-200 ease-in-out outline-none hover:border-slate-300 focus:border-primary focus:ring-4 focus:ring-indigo-500/10 focus:shadow-md"
                inputClass="text-sm focus:ring-0 focus:outline-0"
                :primaryColor="theme?.primary"
              /> -->

              <PasswordConfig :config="config" />
            </div>
            <div>
              <BackupConfig :config="config" />
            </div>
          </div>
        </div>
      </div>
    </div>
  </AppLayout>
</template>
