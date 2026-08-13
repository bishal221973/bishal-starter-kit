<script setup>
import { ref } from "vue";
import { Head, Link } from "@inertiajs/vue3";
import Sidebar from "@/Components/Sidebar.vue";
import Navbar from "@/Components/Navbar.vue";
const sidebarOpen = ref(true);

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
    <Navbar @toggle-sidebar="toggleSidebar" :sidebarOpen="sidebarOpen"/>

    <!-- Sidebar layout fix: Transition padding wrapper instead of content margins -->
    <Sidebar :sidebarOpen="sidebarOpen" />

    <!-- Main Content Area: Padding based control eliminates layout jump glitches completely -->
    <main
      :class="[
        sidebarOpen ? 'pl-64' : 'pl-20',
        'pt-16 min-h-screen transition-all duration-300',
      ]"
    >
      <div class="p-6 max-w-[1600px] mx-auto space-y-6">
        <!-- Elegant Page Header Card -->
        <div
          class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/60 flex flex-col sm:flex-row justify-between sm:items-center gap-4"
        >
          <div>
            <h2 class="text-2xl font-bold tracking-tight text-slate-900">
              Dashboard Workspace
            </h2>
            <p class="text-sm text-slate-500 mt-1">
              Analyze performance, metrics, and manage your operational systems.
            </p>
          </div>
          <div class="flex items-center gap-2">
            <div class="flex items-center gap-2">
              <button
                class="px-4 py-2 text-sm font-semibold bg-white border border-slate-200 rounded-xl text-slate-700 hover:bg-slate-50 transition active:scale-98"
              >
                Export
              </button>
              <button
                class="px-4 py-2 text-sm font-semibold bg-indigo-600 text-white rounded-xl hover:bg-indigo-500 transition shadow-md shadow-indigo-100 active:scale-98"
              >
                Add Widget
              </button>
            </div>
          </div>

          <!-- Stats Grid -->
          <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6">
            <div
              class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/60 flex items-center justify-between group hover:border-indigo-200 transition-all duration-200"
            >
              <div>
                <h4 class="text-slate-400 font-semibold text-xs uppercase tracking-wider">
                  Total Users
                </h4>
                <p class="text-3xl font-bold tracking-tight mt-2 text-slate-900">1,245</p>
                <span
                  class="text-xs font-semibold text-emerald-600 inline-flex items-center gap-0.5 mt-2 bg-emerald-50 px-2 py-0.5 rounded-full"
                >
                  ↑ 12%
                </span>
              </div>
              <div
                class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center group-hover:bg-indigo-600 group-hover:text-white transition-all duration-300"
              >
                <svg
                  class="w-6 h-6"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                  stroke-width="2"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"
                  />
                </svg>
              </div>
            </div>

            <div
              class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/60 flex items-center justify-between group hover:border-indigo-200 transition-all duration-200"
            >
              <div>
                <h4 class="text-slate-400 font-semibold text-xs uppercase tracking-wider">
                  Active Projects
                </h4>
                <p class="text-3xl font-bold tracking-tight mt-2 text-slate-900">87</p>
                <span
                  class="text-xs font-semibold text-emerald-600 inline-flex items-center gap-0.5 mt-2 bg-emerald-50 px-2 py-0.5 rounded-full"
                >
                  ↑ 4%
                </span>
              </div>
              <div
                class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center group-hover:bg-indigo-600 group-hover:text-white transition-all duration-300"
              >
                <svg
                  class="w-6 h-6"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                  stroke-width="2"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M3 7v10a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-6l-2-2H5a2 2 0 00-2 2z"
                  />
                </svg>
              </div>
            </div>

            <div
              class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/60 flex items-center justify-between group hover:border-indigo-200 transition-all duration-200"
            >
              <div>
                <h4 class="text-slate-400 font-semibold text-xs uppercase tracking-wider">
                  Total Revenue
                </h4>
                <p class="text-3xl font-bold tracking-tight mt-2 text-slate-900">
                  $24,200
                </p>
                <span
                  class="text-xs font-semibold text-rose-600 inline-flex items-center gap-0.5 mt-2 bg-rose-50 px-2 py-0.5 rounded-full"
                >
                  ↓ 1.5%
                </span>
              </div>
              <div
                class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center group-hover:bg-indigo-600 group-hover:text-white transition-all duration-300"
              >
                <svg
                  class="w-6 h-6"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                  stroke-width="2"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"
                  />
                </svg>
              </div>
            </div>

            <div
              class="bg-white rounded-2xl p-6 shadow-sm border border-slate-200/60 flex items-center justify-between group hover:border-indigo-200 transition-all duration-200"
            >
              <div>
                <h4 class="text-slate-400 font-semibold text-xs uppercase tracking-wider">
                  System Reports
                </h4>
                <p class="text-3xl font-bold tracking-tight mt-2 text-slate-900">152</p>
                <span
                  class="text-xs font-semibold text-slate-500 inline-flex items-center gap-0.5 mt-2 bg-slate-100 px-2 py-0.5 rounded-full"
                >
                  Stable
                </span>
              </div>
              <div
                class="w-12 h-12 rounded-xl bg-indigo-50 text-indigo-600 flex items-center justify-center group-hover:bg-indigo-600 group-hover:text-white transition-all duration-300"
              >
                <svg
                  class="w-6 h-6"
                  fill="none"
                  viewBox="0 0 24 24"
                  stroke="currentColor"
                  stroke-width="2"
                >
                  <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M9 17v-2m3 2v-4m3 4v-6m2 10H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"
                  />
                </svg>
              </div>
            </div>
          </div>

          <!-- Main Content Slot Area -->
          <div
            class="bg-white rounded-2xl shadow-sm border border-slate-200/60 min-h-[500px] overflow-hidden"
          >
            <div
              class="border-b border-slate-100 p-5 flex items-center justify-between bg-slate-50/50"
            >
              <span class="text-sm font-bold text-slate-800 tracking-tight"
                >Main Segment</span
              >
              <div class="w-2.5 h-2.5 rounded-full bg-emerald-500 animate-pulse"></div>
            </div>
            <div class="p-6">
              <slot />
            </div>
          </div>
        </div>
      </div>
    </main>
  </div>
</template>
