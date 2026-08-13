<script setup>
import { ref } from "vue";
import { Link } from "@inertiajs/vue3";

defineProps({
  sidebarOpen: {
    type: Boolean,
    default: false,
  },
  pt:String
});

const activeMenu = ref(null);

const toggleMenu = (menu) => {
  activeMenu.value = activeMenu.value === menu ? null : menu;
};
</script>

<template>
  <aside
    :class="[
      sidebarOpen ? 'w-sidebar_width' : 'w-20',
      ' left-0 bottom-0 z-40  bg-sidebar border-r border-slate-200 transition-all duration-300 overflow-y-auto overflow-x-hidden',
    ]"
  >
  
    <div class="p-4 space-y-2" :style="{paddingTop:pt}">
      <!-- Dashboard -->
      <Link
        href="/dashboard"
        class="flex items-center text-sidebar_text gap-3 px-4 py-3 rounded-xl hover:bg-sidebar_hover_color hover:text-sidebar_hover_text_color transition-all duration-200"
      >
        <span class="text-lg ">
          <i class="fas fa-home"></i>
        </span>

        <span v-show="sidebarOpen" class="font-medium"> Dashboard </span>
      </Link>

      <!-- Users -->
      <div>
        <button
          @click="toggleMenu('users')"
          class="w-full flex text-sidebar_text items-center justify-between px-4 py-3 rounded-xl hover:bg-sidebar_hover_color hover:text-sidebar_hover_text_color transition-all duration-200"
        >
          <div class="flex items-center gap-3">
            <span class="text-lg"><i class="fa fa-users"></i></span>

            <span v-show="sidebarOpen" class="font-medium"> Users </span>
          </div>

          <svg
            v-show="sidebarOpen"
            class="w-4 h-4 transition-transform duration-300"
            :class="{ 'rotate-180': activeMenu === 'users' }"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M19 9l-7 7-7-7"
            />
          </svg>
        </button>

        <Transition name="submenu">
          <div
            v-show="activeMenu === 'users' && sidebarOpen"
            class="ml-6 mt-1 space-y-1 overflow-hidden"
          >
            <Link href="/users" class="text-sidebar_text block px-4 py-2 rounded-lg hover:bg-sidebar_hover_color hover:text-sidebar_hover_text_color">
              All Users
            </Link>

            <Link href="/roles" class="text-sidebar_text block px-4 py-2 rounded-lg hover:bg-sidebar_hover_color hover:text-sidebar_hover_text_color">
              Roles
            </Link>

            <Link
              href="/permissions"
              class="text-sidebar_text block px-4 py-2 rounded-lg hover:bg-sidebar_hover_color hover:text-sidebar_hover_text_color"
            >
              Permissions
            </Link>
          </div>
        </Transition>
      </div>

      <!-- Settings -->
      <div>
        <button
          @click="toggleMenu('settings')"
          class="w-full flex items-center text-sidebar_text justify-between px-4 py-3 rounded-xl hover:bg-sidebar_hover_color hover:text-sidebar_hover_text_color transition-all duration-200"
        >
          <div class="flex items-center gap-3">
            <span class="text-lg"><i class="fa fa-gear"></i></span>

            <span v-show="sidebarOpen" class="font-medium"> Settings </span>
          </div>

          <svg
            v-show="sidebarOpen"
            class="w-4 h-4 transition-transform duration-300"
            :class="{ 'rotate-180': activeMenu === 'settings' }"
            fill="none"
            stroke="currentColor"
            viewBox="0 0 24 24"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="M19 9l-7 7-7-7"
            />
          </svg>
        </button>

        <Transition name="submenu">
          <div
            v-show="activeMenu === 'settings' && sidebarOpen"
            class="ml-6 mt-1 space-y-1 overflow-hidden"
          >
            <Link
              :href="route('theme.setting')"
              class="text-sidebar_text block px-4 py-2 rounded-lg hover:bg-sidebar_hover_color hover:text-sidebar_hover_text_color"
            >
              Theme Setting
            </Link>

            <Link href="/profile" class="text-sidebar_text block px-4 py-2 rounded-lg hover:bg-sidebar_hover_color hover:text-sidebar_hover_text_color">
              Profile
            </Link>
          </div>
        </Transition>
      </div>
    </div>
  </aside>
</template>

<style scoped>
.submenu-enter-active,
.submenu-leave-active {
  transition: all 0.3s ease;
  overflow: hidden;
}

.submenu-enter-from,
.submenu-leave-to {
  max-height: 0;
  opacity: 0;
}

.submenu-enter-to,
.submenu-leave-from {
  max-height: 300px;
  opacity: 1;
}
</style>
