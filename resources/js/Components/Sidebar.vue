<script setup>
import { ref, computed } from "vue";
import { Link, usePage } from "@inertiajs/vue3";

const props = defineProps({
  sidebarOpen: {
    type: Boolean,
    default: false,
  },

  pt: {
    type: [String, Number],
    default: 0,
  },
});

const page = usePage();

const activeMenu = ref(null);

const toggleMenu = (menu) => {
  activeMenu.value = activeMenu.value === menu ? null : menu;
};

const isActive = (href) => {
  if (!href) return false;

  return page.url === href || page.url.startsWith(`${href}/`);
};

const isRouteActive = (routeName) => {
  try {
    return route().current(routeName);
  } catch {
    return false;
  }
};

const usersActive = computed(() => {
  return isActive("/users") || isActive("/roles") || isActive("/permissions");
});

const settingsActive = computed(() => {
  return (
    isRouteActive("theme.setting") ||
    isRouteActive("configuration.setting") ||
    isActive("/profile")
  );
});
</script>

<template>
  <aside
    :class="[
      sidebarOpen ? 'w-sidebar_width' : 'w-20',
      ' left-0 bottom-0 z-40',
      'bg-sidebar border-r border-slate-200',
      'transition-all duration-300 ease-in-out',
      'overflow-y-auto overflow-x-hidden',
      'shadow-sm h-[100vh]',
    ]"
    :style="{
      paddingTop: `${pt}px`,
    }"
  >
    <div class="px-3 py-4" :style="{ paddingTop: pt }">
      <!-- =====================================================
           MAIN
      ====================================================== -->

      <div
        v-if="sidebarOpen"
        class="px-3 mb-0 text-[10px] font-bold uppercase tracking-wider text-sidebar_text opacity-80"
      >
        Main Menu
      </div>

      <!-- =====================================================
           DASHBOARD
      ====================================================== -->

      <Link
        href="/dashboard"
        :class="[
          'group relative flex items-center gap-3',
          'px-3 py-2 mb-0 rounded-xl',
          'transition-all duration-200',
          isActive('/dashboard')
            ? 'bg-primary text-white shadow-sm'
            : 'text-sidebar_text hover:bg-sidebar_hover_color hover:text-sidebar_hover_text_color',
        ]"
      >
        <!-- Active indicator -->

        <span
          v-if="isActive('/dashboard')"
          class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 rounded-r-full bg-white"
        ></span>

        <!-- Icon -->

        <span
          class="flex items-center justify-center w-8 h-8 shrink-0 rounded-lg transition-colors"
          :class="isActive('/dashboard') ? 'bg-white/15' : 'group-hover:bg-white/10'"
        >
          <i class="fas fa-home text-sm"></i>
        </span>

        <!-- Label -->

        <span v-show="sidebarOpen" class="font-medium text-sm whitespace-nowrap">
          Dashboard
        </span>

        <!-- Tooltip -->

        <span
          v-if="!sidebarOpen"
          class="pointer-events-none absolute left-16 z-50 whitespace-nowrap rounded-lg bg-slate-900 px-3 py-2 text-xs text-white opacity-0 shadow-lg transition-opacity group-hover:opacity-100"
        >
          Dashboard
        </span>
      </Link>

      <!-- =====================================================
           MANAGEMENT
      ====================================================== -->

      <div
        v-if="sidebarOpen"
        class="px-3 mt-2 mb-0 text-[10px] font-bold uppercase tracking-wider text-sidebar_text opacity-80"
      >
        Management
      </div>

      <!-- =====================================================
           USERS
      ====================================================== -->

      <div class="mb-1">
        <button
          type="button"
          @click="toggleMenu('users')"
          :class="[
            'group relative w-full flex items-center justify-between',
            'px-3 py-2 rounded-xl',
            'transition-all duration-200',
            usersActive
              ? 'bg-sidebar_hover_color text-sidebar_hover_text_color'
              : 'text-sidebar_text hover:bg-sidebar_hover_color hover:text-sidebar_hover_text_color',
          ]"
        >
          <div class="flex items-center gap-3 min-w-0">
            <span
              class="flex items-center justify-center w-8 h-8 shrink-0 rounded-lg"
              :class="
                usersActive ? 'bg-primary/10 text-primary' : 'group-hover:bg-white/10'
              "
            >
              <i class="fas fa-users text-sm"></i>
            </span>

            <span v-show="sidebarOpen" class="font-medium text-sm whitespace-nowrap">
              Users
            </span>
          </div>

          <i
            v-show="sidebarOpen"
            class="fas fa-chevron-down text-[10px] transition-transform duration-300"
            :class="{
              'rotate-180': activeMenu === 'users' || usersActive,
            }"
          ></i>

          <!-- Tooltip -->

          <span
            v-if="!sidebarOpen"
            class="pointer-events-none absolute left-16 z-50 whitespace-nowrap rounded-lg bg-slate-900 px-3 py-2 text-xs text-white opacity-0 shadow-lg transition-opacity group-hover:opacity-100"
          >
            Users
          </span>
        </button>

        <!-- Users submenu -->

        <Transition name="submenu">
          <div
            v-if="sidebarOpen && (activeMenu === 'users' || usersActive)"
            class="ml-7 mt-1 pl-3 border-l border-slate-200 space-y-1"
          >
            <Link
              href="/users"
              :class="['submenu-link', isActive('/users') ? 'submenu-active' : '']"
            >
              <span class="submenu-dot"></span>
              All Users
            </Link>

            <Link
              href="/roles"
              :class="['submenu-link', isActive('/roles') ? 'submenu-active' : '']"
            >
              <span class="submenu-dot"></span>
              Roles
            </Link>

            <Link
              href="/permissions"
              :class="['submenu-link', isActive('/permissions') ? 'submenu-active' : '']"
            >
              <span class="submenu-dot"></span>
              Permissions
            </Link>
          </div>
        </Transition>
        <Link
          v-if="page?.props?.registration?.multiple_branch"
          :href="route('branches.index')"
          :class="[
            'group relative flex items-center gap-3',
            'px-3 py-2 mb-0 rounded-xl',
            'transition-all duration-200',
            isActive('/dashboard')
              ? 'bg-primary text-white shadow-sm'
              : 'text-sidebar_text hover:bg-sidebar_hover_color hover:text-sidebar_hover_text_color',
          ]"
        >
          <!-- Active indicator -->

          <span
            v-if="isActive('/dashboard')"
            class="absolute left-0 top-1/2 -translate-y-1/2 w-1 h-6 rounded-r-full bg-white"
          ></span>

          <!-- Icon -->

          <span
            class="flex items-center justify-center w-8 h-8 shrink-0 rounded-lg transition-colors"
            :class="isActive('/dashboard') ? 'bg-white/15' : 'group-hover:bg-white/10'"
          >
            <i class="fas fa-boxes text-sm"></i>
          </span>

          <!-- Label -->

          <span v-show="sidebarOpen" class="font-medium text-sm whitespace-nowrap">
            Branch
          </span>

          <!-- Tooltip -->

          <span
            v-if="!sidebarOpen"
            class="pointer-events-none absolute left-16 z-50 whitespace-nowrap rounded-lg bg-slate-900 px-3 py-2 text-xs text-white opacity-0 shadow-lg transition-opacity group-hover:opacity-100"
          >
            Branch
          </span>
        </Link>
      </div>

      <!-- =====================================================
           SETTINGS
      ====================================================== -->

      <div
        v-if="sidebarOpen"
        class="px-3 mt-2 mb-0 text-[10px] font-bold uppercase tracking-wider text-sidebar_text opacity-80"
      >
        System
      </div>

      <div class="mb-1">
        <button
          type="button"
          @click="toggleMenu('settings')"
          :class="[
            'group relative w-full flex items-center justify-between',
            'px-3 py-2 rounded-xl',
            'transition-all duration-200',
            settingsActive
              ? 'bg-sidebar_hover_color text-sidebar_hover_text_color'
              : 'text-sidebar_text hover:bg-sidebar_hover_color hover:text-sidebar_hover_text_color',
          ]"
        >
          <div class="flex items-center gap-3 min-w-0">
            <span
              class="flex items-center justify-center w-8 h-8 shrink-0 rounded-lg"
              :class="
                settingsActive ? 'bg-primary/10 text-primary' : 'group-hover:bg-white/10'
              "
            >
              <i class="fas fa-gear text-sm"></i>
            </span>

            <span v-show="sidebarOpen" class="font-medium text-sm whitespace-nowrap">
              Settings
            </span>
          </div>

          <i
            v-show="sidebarOpen"
            class="fas fa-chevron-down text-[10px] transition-transform duration-300"
            :class="{
              'rotate-180': activeMenu === 'settings' || settingsActive,
            }"
          ></i>

          <!-- Tooltip -->

          <span
            v-if="!sidebarOpen"
            class="pointer-events-none absolute left-16 z-50 whitespace-nowrap rounded-lg bg-slate-900 px-3 py-2 text-xs text-white opacity-0 shadow-lg transition-opacity group-hover:opacity-100"
          >
            Settings
          </span>
        </button>

        <!-- Settings submenu -->

        <Transition name="submenu">
          <div
            v-if="sidebarOpen && (activeMenu === 'settings' || settingsActive)"
            class="ml-7 mt-1 pl-3 border-l border-slate-200 space-y-1"
          >
            <Link
              :href="route('theme.setting')"
              :class="[
                'submenu-link',
                isRouteActive('theme.setting') ? 'submenu-active' : '',
              ]"
            >
              <span class="submenu-dot"></span>

              <i class="fas fa-palette w-4 text-[11px]"></i>

              Theme Settings
            </Link>

            <Link
              :href="route('configuration.setting')"
              :class="[
                'submenu-link',
                isRouteActive('configuration.setting') ? 'submenu-active' : '',
              ]"
            >
              <span class="submenu-dot"></span>

              <i class="fas fa-sliders w-4 text-[11px]"></i>

              Configuration
            </Link>

            <Link
              href="/profile"
              :class="['submenu-link', isActive('/profile') ? 'submenu-active' : '']"
            >
              <span class="submenu-dot"></span>

              <i class="fas fa-user w-4 text-[11px]"></i>

              Profile
            </Link>
          </div>
        </Transition>
      </div>
    </div>
  </aside>
</template>

<style scoped>
/* ============================================================
   SUBMENU ANIMATION
============================================================ */

.submenu-enter-active,
.submenu-leave-active {
  transition: max-height 0.25s ease, opacity 0.2s ease, transform 0.25s ease;
  overflow: hidden;
}

.submenu-enter-from,
.submenu-leave-to {
  max-height: 0;
  opacity: 0;
  transform: translateY(-5px);
}

.submenu-enter-to,
.submenu-leave-from {
  max-height: 500px;
  opacity: 1;
  transform: translateY(0);
}

/* ============================================================
   SUBMENU LINKS
============================================================ */

.submenu-link {
  position: relative;

  display: flex;
  align-items: center;
  gap: 8px;

  width: 100%;

  padding: 6px 10px;

  border-radius: 8px;

  font-size: 13px;

  transition: background-color 0.2s ease, color 0.2s ease, transform 0.2s ease;
}

.submenu-link {
  @apply text-sidebar_text opacity-70;
}

.submenu-link:hover {
  background-color: var(--sidebar-hover-color);
  color: var(--sidebar-hover-text-color);

  transform: translateX(2px);
}

.submenu-link:hover {
  @apply text-sidebar_hover_text_color opacity-100;
}
/* ============================================================
   ACTIVE SUBMENU
============================================================ */

.submenu-active {
  background-color: var(--sidebar-hover-color);
  color: var(--sidebar-hover-text-color);

  font-weight: 600;
}
.submenu-active {
  @apply text-sidebar_hover_text_color opacity-100;
}

/* ============================================================
   SUBMENU DOT
============================================================ */

.submenu-dot {
  width: 5px;
  height: 5px;

  flex-shrink: 0;

  border-radius: 999px;

  background-color: currentColor;

  opacity: 0.35;
}

.submenu-active .submenu-dot {
  opacity: 1;
}

/* ============================================================
   SCROLLBAR
============================================================ */

aside::-webkit-scrollbar {
  width: 4px;
}

aside::-webkit-scrollbar-track {
  background: transparent;
}

aside::-webkit-scrollbar-thumb {
  background: rgba(100, 116, 139, 0.2);
  border-radius: 999px;
}

aside::-webkit-scrollbar-thumb:hover {
  background: rgba(100, 116, 139, 0.4);
}
</style>
