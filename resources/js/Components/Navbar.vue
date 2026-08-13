<script setup>
const emit = defineEmits(["toggle-sidebar"]);
import { useTheme } from '@/composables/useTheme'

defineProps({
  sidebarOpen: Boolean,
});

const { theme } = useTheme()
</script>
<template>
  <header
    class="fixed top-0 left-0 right-0 z-50 h-navbar_height  backdrop-blur-md border-b border-navbar_border shadow-sm transition-all duration-300"
    :style="{
        backgroundColor:theme.navbar+'90'
    }"
  >
    <div class="h-full px-6 flex items-center justify-between">
      <div class="flex items-center gap-4">
        <button
          class="w-10 h-10 flex items-center justify-center rounded-xl hover:bg-slate-100 border border-transparent hover:border-slate-200 text-slate-600 transition-all duration-200 active:scale-95"
          @click="emit('toggle-sidebar')"
        >
          <svg
            class="w-5 h-5 transition-transform duration-300"
            :class="{ 'rotate-180': !sidebarOpen }"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
            stroke-width="2"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              d="M4 6h16M4 12h12M4 18h16"
            />
          </svg>
        </button>

        <div>
          <h1
            class="font-bold text-lg tracking-tight bg-gradient-to-r from-primary via-warning to-info bg-clip-text text-transparent"
          >
            Bishal Starter Kit
          </h1>
        </div>
      </div>

      <div class="flex items-center gap-3">
        <button
          class="relative w-10 h-10 flex items-center justify-center rounded-xl hover:bg-slate-100 text-slate-600 border border-transparent hover:border-slate-200 transition-all"
        >
          <svg
            class="w-5 h-5"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
            stroke-width="2"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9"
            />
          </svg>
          <span
            class="absolute top-2 right-2.5 w-2 h-2 bg-indigo-600 rounded-full ring-2 ring-white"
          ></span>
        </button>

        <div class="h-8 w-px bg-slate-200 mx-1"></div>

        <div class="flex items-center gap-3 pl-1">
          <div
            class="w-10 h-10 rounded-xl bg-gradient-to-br from-indigo-500 to-violet-600 text-white flex items-center justify-center font-semibold shadow-md shadow-indigo-100"
          >
            B
          </div>
          <div class="hidden sm:block text-left transition-all duration-300">
            <p class="text-xs font-semibold text-slate-800 leading-none" 
            :style="{
                color:theme.navbar_text
            }">Bishal Codes</p>
            <p class="text-[10px] text-slate-400 font-medium mt-1" :style="{
                color:theme.navbar_text+'80'
            }">Super Admin</p>
          </div>
        </div>
      </div>
    </div>
  </header>
</template>
