<script setup>
import { Link, usePage } from "@inertiajs/vue3";
import Card from "@/Components/Elements/Card.vue";
import Label from "@/Components/Label.vue";
import { useTheme } from "@/composables/useTheme";

const { theme } = useTheme();
const page = usePage();

const menuItems = [
  {
    label: "Theme Settings",
    description: "Customize appearance",
    icon: "fa-palette",
    route: "theme.setting",
  },
  {
    label: "Configuration",
    description: "Manage application settings",
    icon: "fa-sliders",
    route: "configuration.setting",
  },
];

const isActive = (routeName) => {
  return route().current(routeName);
};
</script>

<template>
  <Card
    class="w-[300px] shrink-0 sticky top-0 overflow-hidden border border-slate-200 shadow-sm"
    :style="{
      height: `calc(100vh - ${theme.navbar_height + 25}px)`,
    }"
  >
    <!-- =====================================================
         HEADER
    ====================================================== -->

    <template #header>
      <div class="px-1 py-1">

        <div class="flex items-center gap-3">

          <!-- Icon -->

          <div
            class="flex h-10 w-10 items-center justify-center rounded-xl bg-primary/10 text-primary"
          >
            <i class="fas fa-sliders text-sm"></i>
          </div>

          <!-- Title -->

          <div class="min-w-0">

            <Label class="block font-bold text-lg text-slate-800">
              Settings
            </Label>

            <Label class="block text-xs text-slate-500 mt-0.5">
              Manage application preferences
            </Label>

          </div>

        </div>

      </div>
    </template>


    <!-- =====================================================
         NAVIGATION
    ====================================================== -->

    <div class="flex h-full flex-col">

      <div class="space-y-2">

        <!-- Section title -->

        <div class="px-2 pt-1 pb-2">

          <Label
            class="text-[11px] font-semibold uppercase tracking-wider text-slate-400"
          >
            General
          </Label>

        </div>


        <!-- Menu -->

        <Link
          v-for="item in menuItems"
          :key="item.route"
          :href="route(item.route)"
          class="group relative flex items-center gap-3 rounded-xl px-3 py-3 transition-all duration-200"
          :class="
            isActive(item.route)
              ? 'bg-primary text-white shadow-sm'
              : 'text-slate-600 hover:bg-slate-50 hover:text-primary'
          "
        >

          <!-- Active indicator -->

          <span
            v-if="isActive(item.route)"
            class="absolute left-0 top-1/2 h-6 w-1 -translate-y-1/2 rounded-r-full bg-white"
          ></span>


          <!-- Icon -->

          <div
            class="flex h-10 w-10 shrink-0 items-center justify-center rounded-xl transition-all duration-200"
            :class="
              isActive(item.route)
                ? 'bg-white/15 text-white'
                : 'bg-slate-100 text-slate-500 group-hover:bg-primary/10 group-hover:text-primary'
            "
          >
            <i
              class="fas text-sm"
              :class="item.icon"
            ></i>
          </div>


          <!-- Text -->

          <div class="min-w-0 flex-1">

            <Label
              class="block truncate font-medium"
              :class="
                isActive(item.route)
                  ? '!text-white'
                  : 'text-slate-700 group-hover:!text-primary'
              "
            >
              {{ item.label }}
            </Label>

            <Label
              class="block truncate text-xs mt-0.5"
              :class="
                isActive(item.route)
                  ? '!text-white/70'
                  : 'text-slate-400'
              "
            >
              {{ item.description }}
            </Label>

          </div>


          <!-- Arrow -->

          <i
            class="fas fa-chevron-right text-[10px] transition-all duration-200"
            :class="
              isActive(item.route)
                ? 'text-white'
                : 'text-slate-300 group-hover:text-primary group-hover:translate-x-0.5'
            "
          ></i>

        </Link>

      </div>


      <!-- ===================================================
           BOTTOM INFORMATION
      ==================================================== -->

      <div class="mt-auto pt-5">

        <div
          class="rounded-xl border border-slate-200 bg-slate-50 p-4"
        >

          <div class="flex items-start gap-3">

            <div
              class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg bg-primary/10 text-primary"
            >
              <i class="fas fa-circle-info text-sm"></i>
            </div>

            <div class="min-w-0">

              <Label class="block font-semibold text-sm text-slate-700">
                Settings
              </Label>

              <Label
                class="block text-xs text-slate-500 leading-5 mt-1"
              >
                Configure your application's appearance,
                behavior and system preferences.
              </Label>

            </div>

          </div>

        </div>


        <!-- Version -->

        <div class="flex items-center justify-between px-2 pt-4">

          <Label class="text-[11px] text-slate-400">
            Application Settings
          </Label>

          <div class="flex items-center gap-1.5">

            <span
              class="h-1.5 w-1.5 rounded-full bg-emerald-500"
            ></span>

            <Label class="text-[11px] text-slate-400">
              Active
            </Label>

          </div>

        </div>

      </div>

    </div>
  </Card>
</template>