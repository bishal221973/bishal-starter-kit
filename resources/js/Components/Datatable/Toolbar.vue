<script setup>
import { computed, ref } from "vue";

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

  primaryColor: {
    type: String,
    default: "#3D98AB",
  },
});

const emit = defineEmits([
  "update:search",
  "clear-search",
  "clear-filters",
  "filter",
]);

const searchValue = computed({
  get: () => props.search,
  set: (value) => emit("update:search", value),
});

const showFilterModal = ref(false);

function openFilter() {
  showFilterModal.value = true;
  emit("filter");
}

function closeFilter() {
  showFilterModal.value = false;
}
</script>

<template>
  <div class="border-b border-slate-200 bg-white">
    <div
      class="flex min-h-[64px] items-center justify-between gap-4 py-3 sm:px-5"
    >
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
            class="toolbar-search h-10 w-full rounded-lg border border-slate-200 bg-slate-50/70 pl-10 pr-10 text-sm text-slate-700 shadow-md outline-none transition-all placeholder:text-slate-400"
          />

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

        <!-- FILTER BUTTON -->
        <button
          v-if="filterable"
          type="button"
          class="relative flex h-10 shrink-0 items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 text-sm font-medium text-slate-600 shadow-md transition hover:bg-slate-50"
          @click="openFilter"
        >
          <!-- Filter icon -->
          <svg
            class="h-4 w-4"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
            stroke-width="2"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              d="M3 4h18M6 12h12M10 20h4"
            />
          </svg>

          <span>Filter</span>

          <!-- Active filter count -->
          <span
            v-if="hasActiveFilters"
            class="flex h-5 min-w-5 items-center justify-center rounded-full px-1.5 text-[11px] font-semibold text-white"
            :style="{ backgroundColor: primaryColor }"
          >
            !
          </span>
        </button>

        <!-- Clear filters -->
        <button
          v-if="filterable && hasActiveFilters"
          type="button"
          class="hidden h-10 shrink-0 items-center rounded-lg bg-red-100 px-2.5 text-sm font-medium text-red-500 shadow-md transition hover:text-red-700 sm:flex"
          @click="emit('clear-filters')"
        >
          Clear Filter
        </button>

        <!-- Custom toolbar -->
        <slot name="toolbar" />
      </div>

      <!-- RIGHT -->
      <div class="flex shrink-0 items-center gap-2">
        <slot name="toolbar-right" />
      </div>
    </div>

    <!-- FILTER MODAL -->
    <Teleport to="body">
      <div
        v-if="showFilterModal"
        class="fixed inset-0 z-[999] flex items-center justify-center p-4"
      >
        <!-- Overlay -->
        <div
          class="absolute inset-0 bg-black/40 backdrop-blur-sm"
          @click="closeFilter"
        />

        <!-- Modal -->
        <div
          class="relative z-10 w-full max-w-lg overflow-hidden rounded-xl bg-white shadow-2xl"
        >
          <!-- Header -->
          <div class="flex items-center justify-between border-b border-slate-200 px-5 py-4">
            <div>
              <h3 class="text-base font-semibold text-slate-800">
                Filter
              </h3>

              <p class="mt-0.5 text-xs text-slate-500">
                Apply filters to narrow down the results.
              </p>
            </div>

            <button
              type="button"
              class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-600"
              @click="closeFilter"
            >
              <svg
                class="h-5 w-5"
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

          <!-- Filter Content -->
          <div class="max-h-[60vh] overflow-y-auto p-5">
            <slot
              name="filters"
              :close="closeFilter"
            />
          </div>

          <!-- Footer -->
          <div
            class="flex items-center justify-between border-t border-slate-200 bg-slate-50 px-5 py-3"
          >
            <button
              type="button"
              class="rounded-lg px-3 py-2 text-sm font-medium text-red-500 hover:bg-red-50"
              @click="emit('clear-filters')"
            >
              Clear All
            </button>

            <button
              type="button"
              class="rounded-lg px-4 py-2 text-sm font-medium text-white transition hover:opacity-90"
              :style="{ backgroundColor: primaryColor }"
              @click="closeFilter"
            >
              Apply Filters
            </button>
          </div>
        </div>
      </div>
    </Teleport>
  </div>
</template>

<style scoped>
.toolbar-search {
  box-shadow: 2px 2px 0 2px #fff;
}

.toolbar-search:focus {
  border-color: var(--toolbar-primary);
  background-color: white;
  box-shadow: 0 0 0 1px var(--toolbar-primary);
}
</style>