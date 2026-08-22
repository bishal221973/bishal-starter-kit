<script setup>
defineProps({
  columns: {
    type: Array,
    default: () => [],
  },

  selectable: {
    type: Boolean,
    default: false,
  },

  sortable: {
    type: Boolean,
    default: true,
  },

  sortBy: {
    type: String,
    default: null,
  },

  sortDirection: {
    type: String,
    default: "asc",
  },

  filterable: {
    type: Boolean,
    default: true,
  },

  activeFilters: {
    type: Object,
    default: () => ({}),
  },

  allSelected: {
    type: Boolean,
    default: false,
  },

  hasRows: {
    type: Boolean,
    default: false,
  },

  actions: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits([
  "sort",
  "filter-change",
  "toggle-select-all",
]);

function handleFilterChange(column, event) {
  emit(
    "filter-change",
    column.key,
    event.target.value
  );
}
</script>

<template>
  <thead class="bg-slate-50">
    <tr>
      <!-- Selection -->
      <th
        v-if="selectable"
        class="w-12 px-4 py-3"
      >
        <input
          type="checkbox"
          :checked="allSelected"
          :disabled="!hasRows"
          class="rounded border-slate-300 text-primary focus:ring-primary"
          @change="emit('toggle-select-all')"
        />
      </th>

      <!-- Columns -->
      <th
        v-for="column in columns"
        :key="column.key"
        class="whitespace-nowrap px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500"
      >
        <div class="flex items-center gap-2">
          <!-- Sort -->
          <button
            v-if="sortable && column.sortable !== false"
            type="button"
            class="flex items-center gap-1.5 hover:text-slate-700"
            @click="emit('sort', column)"
          >
            <span>
              {{ column.label }}
            </span>

            <span
              v-if="sortBy === column.key"
              class="font-bold text-primary"
            >
              {{ sortDirection === "asc" ? "↑" : "↓" }}
            </span>
          </button>

          <!-- Non sortable -->
          <span v-else>
            {{ column.label }}
          </span>

          <!-- Select filter -->
          <select
            v-if="
              filterable &&
              column.filterable &&
              column.filterType === 'select'
            "
            :value="activeFilters[column.key] ?? ''"
            class="rounded-lg border border-slate-200 bg-white px-2 py-1 text-xs font-normal normal-case tracking-normal text-slate-600 outline-none focus:border-primary focus:ring-1 focus:ring-primary/20"
            @click.stop
            @change="handleFilterChange(column, $event)"
          >
            <option value="">
              All
            </option>

            <option
              v-for="option in column.filterOptions || []"
              :key="option.value"
              :value="option.value"
            >
              {{ option.label }}
            </option>
          </select>
        </div>
      </th>

      <!-- Actions -->
      <th
        v-if="actions"
        class="w-24 whitespace-nowrap px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500"
      >
        Actions
      </th>
    </tr>
  </thead>
</template>