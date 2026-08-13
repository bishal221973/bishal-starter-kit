<script setup>
import { ref } from "vue";
import { Head, Link } from "@inertiajs/vue3";
import Sidebar from "@/Components/Sidebar.vue";
import Navbar from "@/Components/Navbar.vue";
import { useTheme } from '@/composables/useTheme'

const sidebarOpen = ref(true);
const { theme } = useTheme()

// Modernized menu items with active route checking placeholder
const menuItems = [
  {
    title: "Dashboard",
    icon: `<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" /></svg>`,
    route: "dashboard",
    active: true, // Emulating dynamic route active checking
  },
  {
    title: "Users",
    icon: `<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" /></svg>`,
    route: "#",
    active: false,
  },
  {
    title: "Projects",
    icon: `<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z" /></svg>`,
    route: "#",
    active: false,
  },
  {
    title: "Reports",
    icon: `<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10a2 2 0 01-2 2h-2a2 2 0 01-2-2zm9 0v-4a2 2 0 00-2-2h-2a2 2 0 00-2 2v4a2 2 0 002 2h2a2 2 0 002-2z" /></svg>`,
    route: "#",
    active: false,
  },
  {
    title: "Settings",
    icon: `<svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /></svg>`,
    route: "#",
    active: false,
  },
];
const toggleSidebar = () => {
  sidebarOpen.value = !sidebarOpen.value;
};
</script>

<template>
  <Head title="Dashboard" />

  <div class="min-h-screen bg-slate-50 text-slate-900 font-sans antialiased">
    <!-- Top Navbar -->
    <Navbar @toggle-sidebar="toggleSidebar" :sidebarOpen="sidebarOpen" />

    <!-- Sidebar layout fix: Transition padding wrapper instead of content margins -->
    <div class="flex h-[100vh] overflow-hidden">
      <Sidebar v-if="theme?.sidebar_position =='left'" :sidebarOpen="sidebarOpen" :pt="`${theme?.navbar_height+25}px`"/>

      <!-- Main Content Area: Padding based control eliminates layout jump glitches completely -->
      <main :style="{paddingTop:theme?.navbar_height+'px'}" :class="['min-h-screen transition-all w-full duration-300 overflow-y-auto']">
        <div class="p-6 w-full mx-auto space-y-6">
          <!-- Elegant Page Header Card -->
          <slot />
        </div>
      </main>
      <Sidebar v-if="theme?.sidebar_position =='right'" :sidebarOpen="sidebarOpen" :pt="`${theme?.navbar_height+25}px`"/>
    </div>
  </div>
</template>
