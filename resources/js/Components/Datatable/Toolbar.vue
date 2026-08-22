<script setup>
import { computed } from "vue";

const props = defineProps({
  search: {
    type: String,
    default: "",
  },

  searchable: {
    type: Boolean,
    default: true,
  },

  searchPlaceholder: {
    type: String,
    default: "Search...",
  },

  filterable: {
    type: Boolean,
    default: true,
  },

  hasActiveFilters: {
    type: Boolean,
    default: false,
  },

  selectable: {
    type: Boolean,
    default: false,
  },

  selectedCount: {
    type: Number,
    default: 0,
  },
  primaryColor: String,
});

const emit = defineEmits(["update:search", "clear-search", "clear-filters", "filter"]);

const searchValue = computed({
  get: () => props.search,
  set: (value) => emit("update:search", value),
});
</script>

<template>
  <div class="border-b border-slate-200 bg-white">
    <div class="flex min-h-[64px] items-center justify-between gap-4 py-3 sm:px-5">
      <!-- LEFT -->
      <div class="flex min-w-0 flex-1 items-center gap-2">
        <!-- Search -->
        <div v-if="searchable" class="relative w-[300px]">
          <input
            v-model="searchValue"
            type="search"
            :placeholder="searchPlaceholder"
            :style="{
              '--toolbar-primary': primaryColor,
            }"
            class="toolbar-search h-10 w-full shadow-md rounded-lg border border-slate-200 bg-slate-50/70 pl-10 pr-10 text-sm text-slate-700 outline-none transition-all placeholder:text-slate-400"
          />

          <!-- Clear search -->
          <button
            v-if="search"
            type="button"
            class="absolute right-2 top-1/2 flex h-6 w-6 -translate-y-1/2 items-center justify-center rounded-md text-slate-400 transition hover:bg-slate-100 hover:text-slate-600"
            @click="
              emit('update:search', '');
              emit('clear-search');
            "
          >
            <svg
              class="h-3.5 w-3.5"
              fill="none"
              viewBox="0 0 24 24"
              stroke="currentColor"
              stroke-width="2"
            >
              <path
                stroke-linecap="round"
                stroke-linejoin="round"
                d="M6 18 18 6M6 6l12 12"
              />
            </svg>
          </button>
        </div>

        <!-- Selection -->

        <!-- Clear -->
        <button
          v-if="filterable && hasActiveFilters"
          type="button"
          class="hidden h-10 shrink-0 shadow-md items-center rounded-lg px-2.5 text-sm font-medium bg-red-100 text-red-500 transition hover:text-slate-700 sm:flex"
          @click="emit('clear-filters')"
        >
          Clear Filter
        </button>
        <slot name="toolbar" />
      </div>

      <!-- RIGHT -->
      <div class="flex shrink-0 items-center gap-2">
        <slot name="toolbar-right" />
      </div>
    </div>
  </div>
</template>
<style scoped>
.toolbar-search {
  /* border-color: var(--toolbar-primary); */
  box-shadow: 2px 2px 0 2px #fff;
}

.toolbar-search:focus {
  border-color: var(--toolbar-primary);
  background-color: white;
  box-shadow: 0px 0px 0 1px var(--toolbar-primary);
  /* box-shadow: 2px 2px 0 2px var(--toolbar-primary); */
}
</style>
