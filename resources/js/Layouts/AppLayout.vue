<script setup>
import { onMounted, ref, watch } from "vue";
import { Head, Link, usePage } from "@inertiajs/vue3";

import Sidebar from "@/Components/Sidebar.vue";
import Navbar from "@/Components/Navbar.vue";
import { useTheme } from "@/composables/useTheme";
import ScreenSaver from "@/Components/ScreenSaver.vue";
import AutoLogout from "@/Components/AutoLogout.vue";

import { toast } from "vue3-toastify";
import "vue3-toastify/dist/index.css";

const sidebarOpen = ref(true);

const { theme } = useTheme();

const page = usePage();

const toggleSidebar = () => {
  sidebarOpen.value = !sidebarOpen.value;
};


const toBoolean = (value, fallback = true) => {
  if (value === undefined || value === null) {
    return fallback;
  }

  if (typeof value === "boolean") {
    return value;
  }

  return Number(value) === 1;
};

const notify = (message, type = "success") => {
  const config = page.props.toast ?? {};

  const options = {
    autoClose: Number(config.timeout ?? 10),

    position: config.position ?? "top-right",

    pauseOnHover: toBoolean(
      config.pause_on_hover,
      true
    ),

    closeOnClick: toBoolean(
      config.close_on_click,
      true
    ),

    pauseOnFocusLoss: toBoolean(
      config.pause_on_focus_loss,
      true
    ),

    closeButton: toBoolean(
      config.close_button,
      true
    ),

    hideProgressBar: toBoolean(
      config.hide_progress_bar,
      true
    ),

    rtl: toBoolean(
      config.rtl,
      true
    ),

    icon: config.icon === "false" ? false : undefined,
  };

  toast[type](message, options);
};

watch(
  () => page.props.flash?.success,
  (message) => {
    if (message) {
      notify(message, "success");
    }
  }
);

watch(
  () => page.props.flash?.error,
  (message) => {
    if (message) {
      notify(message, "error");
    }
  }
);

watch(
  () => page.props.flash?.warning,
  (message) => {
    if (message) {
      notify(message, "warning");
    }
  }
);

watch(
  () => page.props.flash?.info,
  (message) => {
    if (message) {
      notify(message, "info");
    }
  }
);
</script>

<template>
  <Head title="Dashboard" />

  <div class="min-h-screen bg-background text-slate-900 font-sans antialiased">
    <!-- Top Navbar -->
    <Navbar @toggle-sidebar="toggleSidebar" :sidebarOpen="sidebarOpen" />

    <!-- Sidebar layout fix: Transition padding wrapper instead of content margins -->
    <div class="flex h-[100vh] overflow-hidden">
      <Sidebar
        v-if="theme?.sidebar_position == 'left'"
        :sidebarOpen="sidebarOpen"
        :pt="`${theme?.navbar_height + 25}px`"
      />

      <!-- Main Content Area: Padding based control eliminates layout jump glitches completely -->
      <!-- <main
        :style="{ paddingTop: theme?.navbar_height + 'px' }"
        :class="['min-h-screen transition-all w-full duration-300 overflow-y-auto']"
      >
        <div class="p-6 w-full mx-auto space-y-6">
         
          <slot />
        </div>
      </main> -->
      <main
        :style="{ paddingTop: theme?.navbar_height + 'px' }"
        :class="[
          'min-h-screen transition-all w-full duration-300 overflow-y-auto flex flex-col',
        ]"
      >
        <div class="p-6 w-full mx-auto space-y-6 flex-1">
          <slot />
        </div>

        <!-- Footer -->
        <footer class="border-t border-slate-200 bg-background px-6 py-4">
          <div
            class="flex flex-col sm:flex-row items-center justify-between gap-2 text-xs text-slate-500"
          >
            <p v-if="page?.props?.footer?.text">{{ page?.props?.footer?.text }}</p>
            <p v-else>
              © {{ new Date().getFullYear() }} Rising Tech Nepal. All rights reserved.
            </p>

            <div class="flex items-center gap-4">
              <Link href="#" class="transition hover:text-primary"> Privacy Policy </Link>

              <Link href="#" class="transition hover:text-primary">
                Terms & Conditions
              </Link>

              <span> v1.0.0 </span>
            </div>
          </div>
        </footer>
      </main>
      <Sidebar
        v-if="theme?.sidebar_position == 'right'"
        :sidebarOpen="sidebarOpen"
        :pt="`${theme?.navbar_height + 25}px`"
      />
    </div>
  </div>

  <ScreenSaver
    :enabled="page?.props?.screenSaver?.enabled"
    :timeout="page?.props?.screenSaver?.timeout * 60"
    type="slider"
    :images="page?.props?.screenSaver?.images"
    :show-clock="page?.props?.screenSaver?.show_clock"
    :show-date="page?.props?.screenSaver?.show_date"
  />

  <AutoLogout
    :enabled="page.props.autoLogout?.enabled ?? false"
    :timeout="page.props.autoLogout?.timeout ?? 30"
    :show-warning="page.props.autoLogout?.show_warning ?? true"
    :warning-time="page.props.autoLogout?.warning_time ?? 1"
  />
</template>
