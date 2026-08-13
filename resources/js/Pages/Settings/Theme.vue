<script setup>
import { useForm } from "@inertiajs/vue3";
import AppLayout from "@/Layouts/AppLayout.vue";
import Label from "@/Components/Label.vue";
import ThemeColor from "@/Components/Settings/ThemeColor.vue";
import SidebarSetting from "@/Components/Settings/SidebarSetting.vue";
import NavbarSetting from "@/Components/Settings/NavbarSetting.vue";
const props = defineProps({
  setting: Object,
});

const form = useForm({
  primary_color: props.setting?.primary_color ?? "#3d98aa",
  secondary_color: props?.setting?.secondary_color ?? "#2f7f8f",
  accent_color: props?.setting?.accent_color ?? "#4fb6c8",
  success_color: props?.setting?.success_color ?? "#22c55e",
  warning_color: props?.setting?.warning_color ?? "#f59e0b",
  danger_color: props?.setting?.danger_color ?? "#ef4444",
  info_color: props?.setting?.info_color ?? "#06b6d4",

  background_color: props?.setting?.background_color ?? "#ffffff",
  text_color: props?.setting?.text_color ?? "#1e293b",

  sidebar_bg_color: props.setting?.sidebar_bg_color ?? "#0f172a",
  sidebar_text_color: props?.setting?.sidebar_text_color ?? "#ffffff",
  sidebar_hover_color: props?.setting?.sidebar_hover_color ?? "#1e293b",
  sidebar_hover_text_color: props?.setting?.sidebar_hover_text_color ?? "#1e293b",
  sidebar_active_color: props?.setting?.sidebar_active_color ?? "#3d98aa",
  sidebar_width: props?.setting?.sidebar_width ?? "280",
  sidebar_position: props?.setting?.sidebar_position ?? "left",

  navbar_bg_color: props?.setting?.navbar_bg_color ?? "#ffffff",
  navbar_text_color: props?.setting?.navbar_text_color ?? "#1e293b",
  navbar_border_color: props?.setting?.navbar_border_color ?? "#1e293b",
  navbar_height: props?.setting?.navbar_height ?? "70",

  theme_mode: props?.setting.theme_mode ?? "light",
  layout_mode: props?.setting.layout_mode ?? "full",

  card_border_radius: props?.card_border_radius ?? 16,
  button_border_radius: props?.button_border_radius ?? 10,

  font_family: props?.setting?.font_family ?? "Inter",
  font_size: props?.setting?.font_size ?? 14,

  enable_animations: props?.setting?.enable_animations ?? true,
  enable_breadcrumbs: props?.setting?.enable_breadcrumbs ?? true,
  enable_notifications: props?.setting?.enable_notifications ?? true,
});

const save = () => {
  form.post(route("theme.setting.update"));
};
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

      <form @submit.prevent="save">
        <div class="grid grid-cols-12 gap-6">
          <!-- Theme Colors -->

          <div class="col-span-12">
            <ThemeColor :setting="setting" />
          </div>
          <!-- Sidebar -->

          <div class="col-span-12">
            <SidebarSetting :setting="setting" />
          </div>
          <!-- Navbar -->
          <div class="col-span-12">
            <NavbarSetting :setting="setting" />
          </div>

          <!-- Typography -->
          <div class="bg-white rounded-2xl shadow-sm border p-6">
            <Label class="font-semibold mb-4">Typography</Label>

            <div class="space-y-4">
              <input
                v-model="form.font_family"
                placeholder="Font Family"
                class="w-full border rounded-xl px-4 py-2"
              />

              <input
                type="number"
                v-model="form.font_size"
                placeholder="Font Size"
                class="w-full border rounded-xl px-4 py-2"
              />
            </div>
          </div>

          <!-- Features -->
          <div class="bg-white rounded-2xl shadow-sm border p-6">
            <Label class="font-semibold mb-4">Features</Label>

            <div class="space-y-4">
              <label class="flex items-center gap-3">
                <input type="checkbox" v-model="form.enable_animations" />
                Enable Animations
              </label>

              <label class="flex items-center gap-3">
                <input type="checkbox" v-model="form.enable_breadcrumbs" />
                Enable Breadcrumbs
              </label>

              <label class="flex items-center gap-3">
                <input type="checkbox" v-model="form.enable_notifications" />
                Enable Notifications
              </label>
            </div>
          </div>
        </div>

        <div class="mt-6">
          <button type="submit" class="px-6 py-3 rounded-xl bg-primary text-white">
            Save Settings
          </button>
        </div>
      </form>
    </div>
  </AppLayout>
</template>
