<script setup>
import { ref, onMounted, onUnmounted } from "vue";
import { useTheme } from "@/composables/useTheme";

const emit = defineEmits(["toggle-sidebar"]);

defineProps({
  sidebarOpen: Boolean,
});

const { theme } = useTheme();

const zoomLevel = ref(100);

const applyZoom = () => {
  document.body.style.zoom = `${zoomLevel.value}%`;
};


</script>

<template>
  <header
    class="fixed top-0 left-0 right-0 z-50 h-navbar_height backdrop-blur-xl border-b shadow-[0_1px_20px_rgba(0,0,0,0.04)] transition-all duration-300"
    :style="{
      backgroundColor: theme.navbar + 'E8',
      borderColor: theme.navbar_border || 'rgba(148,163,184,0.15)',
    }"
  >
    <div class="h-full px-4 sm:px-6 flex items-center justify-between">
      <!-- =====================================================
           LEFT SIDE
      ====================================================== -->
      <div class="flex items-center gap-3 min-w-0">
        <!-- Sidebar Toggle -->
        <button
          type="button"
          @click="emit('toggle-sidebar')"
          class="group relative w-10 h-10 flex items-center justify-center rounded-xl border transition-all duration-200 active:scale-95 hover:shadow-sm"
          :style="{
            color: theme.navbar_text,
            borderColor: theme.navbar_border || 'rgba(148,163,184,0.18)',
          }"
          title="Toggle Sidebar"
        >
          <span
            class="absolute inset-0 rounded-xl opacity-0 group-hover:opacity-100 transition-opacity duration-200"
            :style="{
              backgroundColor: theme.primary + '12',
            }"
          ></span>

          <svg
            class="relative w-[19px] h-[19px] transition-transform duration-300"
            :class="{ 'rotate-180': !sidebarOpen }"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
            stroke-width="1.8"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              d="M4 6h16M4 12h10M4 18h16"
            />
          </svg>
        </button>

        <!-- Brand -->
        <div class="flex items-center gap-3 min-w-0">
          <!-- Logo -->
          <div
            class="hidden sm:flex w-9 h-9 rounded-xl items-center justify-center text-white font-bold text-sm shadow-lg shrink-0"
            :style="{
              background: `linear-gradient(135deg, ${theme.primary}, ${theme.secondary})`,
              boxShadow: `0 8px 20px ${theme.primary}30`,
            }"
          >
            B
          </div>

          <div class="min-w-0">
            <h1
              class="font-bold text-[15px] sm:text-[16px] tracking-tight truncate"
              :style="{
                color: theme.navbar_text,
              }"
            >
              Bishal Starter Kit
            </h1>

            <p
              class="hidden sm:block text-[10px] font-medium mt-0.5 tracking-wide uppercase opacity-50"
              :style="{
                color: theme.navbar_text,
              }"
            >
              Admin Dashboard
            </p>
          </div>
        </div>
      </div>

      <!-- =====================================================
           RIGHT SIDE
      ====================================================== -->
      <div class="flex items-center gap-1 sm:gap-2">
        
        

        <!-- =================================================
             NOTIFICATION
        ================================================== -->
        <button
          type="button"
          class="group relative w-10 h-10 flex items-center justify-center rounded-xl border transition-all duration-200 active:scale-95"
          :style="{
            color: theme.navbar_text,
            borderColor: theme.navbar_border || 'rgba(148,163,184,0.18)',
          }"
          title="Notifications"
        >
          <svg
            class="w-[19px] h-[19px] transition-transform duration-200 group-hover:scale-105"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
            stroke-width="1.8"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"
            />
          </svg>

          <!-- Notification Dot -->
          <span
            class="absolute top-[8px] right-[8px] w-[7px] h-[7px] rounded-full ring-2"
            :style="{
              backgroundColor: theme.primary,
              '--tw-ring-color': theme.navbar,
            }"
          ></span>
        </button>

        <!-- Divider -->
        <div
          class="hidden sm:block h-7 w-px mx-1"
          :style="{
            backgroundColor: theme.navbar_text + '15',
          }"
        ></div>

        <!-- =================================================
             USER PROFILE
        ================================================== -->
        <button
          type="button"
          class="group flex items-center gap-2.5 pl-1 pr-1.5 py-1 rounded-xl transition-all duration-200 active:scale-[0.98]"
        >
          <!-- Avatar -->
          <div
            class="relative w-9 h-9 sm:w-10 sm:h-10 rounded-xl flex items-center justify-center text-white font-bold text-sm shadow-md shrink-0 transition-all duration-200 group-hover:scale-[1.03]"
            :style="{
              background: `linear-gradient(135deg, ${theme.primary}, ${theme.secondary})`,
              boxShadow: `0 6px 16px ${theme.primary}30`,
            }"
          >
            B

            <!-- Online -->
            <span
              class="absolute right-[-1px] bottom-[-1px] w-2.5 h-2.5 rounded-full border-2"
              :style="{
                backgroundColor: '#22c55e',
                borderColor: theme.navbar,
              }"
            ></span>
          </div>

          <!-- User Info -->
          <div class="hidden sm:block text-left leading-none">
            <p
              class="text-xs font-semibold"
              :style="{
                color: theme.navbar_text,
              }"
            >
              Bishal Codes
            </p>

            <p
              class="text-[10px] font-medium mt-1.5 opacity-50"
              :style="{
                color: theme.navbar_text,
              }"
            >
              Super Admin
            </p>
          </div>

          <!-- Dropdown -->
          <svg
            class="hidden sm:block w-3.5 h-3.5 opacity-40 transition-transform duration-200 group-hover:translate-y-0.5"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
            stroke-width="2"
            :style="{
              color: theme.navbar_text,
            }"
          >
            <path stroke-linecap="round" stroke-linejoin="round" d="m6 9 6 6 6-6" />
          </svg>
        </button>
      </div>
    </div>
  </header>
</template>
