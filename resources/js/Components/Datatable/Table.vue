<script setup>
import { computed, ref, watch } from "vue";

const props = defineProps({
  columns: {
    type: Array,
    required: true,
  },

  data: {
    type: Array,
    default: () => [],
  },

  loading: {
    type: Boolean,
    default: false,
  },

  searchable: {
    type: Boolean,
    default: true,
  },

  searchPlaceholder: {
    type: String,
    default: "Search...",
  },

  sortable: {
    type: Boolean,
    default: true,
  },

  pagination: {
    type: Boolean,
    default: true,
  },

  pageSize: {
    type: Number,
    default: 10,
  },

  pageSizeOptions: {
    type: Array,
    default: () => [10, 25, 50, 100],
  },

  selectable: {
    type: Boolean,
    default: false,
  },

  rowKey: {
    type: String,
    default: "id",
  },

  emptyText: {
    type: String,
    default: "No records found.",
  },
});

const emit = defineEmits([
  "row-click",
  "selection-change",
  "sort",
  "search",
  "page-change",
]);

const search = ref("");
const currentPage = ref(1);
const currentPageSize = ref(props.pageSize);

const sortBy = ref(null);
const sortDirection = ref("asc");

const selectedRows = ref([]);

const visibleColumns = ref(props.columns.map((column) => column.key));

const filteredData = computed(() => {
  let result = [...props.data];

  // Search
  if (search.value.trim()) {
    const query = search.value.toLowerCase();

    result = result.filter((row) =>
      props.columns.some((column) => {
        if (column.searchable === false) {
          return false;
        }

        const value = getValue(row, column.key);

        return String(value ?? "")
          .toLowerCase()
          .includes(query);
      })
    );
  }

  // Sorting
  if (sortBy.value) {
    result.sort((a, b) => {
      const aValue = getValue(a, sortBy.value);
      const bValue = getValue(b, sortBy.value);

      if (aValue == null) return 1;
      if (bValue == null) return -1;

      const comparison = String(aValue).localeCompare(String(bValue), undefined, {
        numeric: true,
        sensitivity: "base",
      });

      return sortDirection.value === "asc" ? comparison : -comparison;
    });
  }

  return result;
});

const totalPages = computed(() => {
  if (!props.pagination) {
    return 1;
  }

  return Math.max(1, Math.ceil(filteredData.value.length / currentPageSize.value));
});

const paginatedData = computed(() => {
  if (!props.pagination) {
    return filteredData.value;
  }

  const start = (currentPage.value - 1) * currentPageSize.value;

  return filteredData.value.slice(start, start + currentPageSize.value);
});

const visibleColumnList = computed(() =>
  props.columns.filter((column) => visibleColumns.value.includes(column.key))
);

const allSelected = computed(() => {
  if (!paginatedData.value.length) {
    return false;
  }

  return paginatedData.value.every((row) =>
    selectedRows.value.includes(row[props.rowKey])
  );
});

const showingFrom = computed(() => {
  if (!filteredData.value.length) {
    return 0;
  }

  return props.pagination ? (currentPage.value - 1) * currentPageSize.value + 1 : 1;
});

const showingTo = computed(() => {
  if (!filteredData.value.length) {
    return 0;
  }

  return props.pagination
    ? Math.min(currentPage.value * currentPageSize.value, filteredData.value.length)
    : filteredData.value.length;
});

function getValue(row, key) {
  return key.split(".").reduce((value, key) => value?.[key], row);
}

function sort(column) {
  if (!props.sortable || column.sortable === false) {
    return;
  }

  if (sortBy.value === column.key) {
    sortDirection.value = sortDirection.value === "asc" ? "desc" : "asc";
  } else {
    sortBy.value = column.key;
    sortDirection.value = "asc";
  }

  emit("sort", {
    column: column.key,
    direction: sortDirection.value,
  });
}

function toggleSelectAll() {
  if (allSelected.value) {
    selectedRows.value = selectedRows.value.filter(
      (id) => !paginatedData.value.some((row) => row[props.rowKey] === id)
    );
  } else {
    const ids = paginatedData.value.map((row) => row[props.rowKey]);

    selectedRows.value = [...new Set([...selectedRows.value, ...ids])];
  }

  emit("selection-change", selectedRows.value);
}

function toggleRow(row) {
  const id = row[props.rowKey];

  if (selectedRows.value.includes(id)) {
    selectedRows.value = selectedRows.value.filter((selectedId) => selectedId !== id);
  } else {
    selectedRows.value.push(id);
  }

  emit("selection-change", selectedRows.value);
}

function isSelected(row) {
  return selectedRows.value.includes(row[props.rowKey]);
}

function changePage(page) {
  if (page < 1 || page > totalPages.value) {
    return;
  }

  currentPage.value = page;

  emit("page-change", page);
}

function changePageSize(size) {
  currentPageSize.value = Number(size);
  currentPage.value = 1;
}

function clearSelection() {
  selectedRows.value = [];
  emit("selection-change", []);
}

function clearSearch() {
  search.value = "";
}

watch(search, () => {
  currentPage.value = 1;

  emit("search", search.value);
});

watch(
  () => props.data,
  () => {
    currentPage.value = Math.min(currentPage.value, totalPages.value);
  }
);

defineExpose({
  clearSelection,
  clearSearch,
});
</script>

<template>
  <div class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm">
    <!-- Toolbar -->
    <div
      class="flex flex-col gap-3 border-b border-slate-200 p-4 sm:flex-row sm:items-center sm:justify-between"
    >
      <div class="flex items-center gap-2">
        <!-- Search -->
        <div v-if="searchable" class="relative">
          <svg
            class="absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"
            fill="none"
            viewBox="0 0 24 24"
            stroke="currentColor"
          >
            <path
              stroke-linecap="round"
              stroke-linejoin="round"
              stroke-width="2"
              d="m21 21-4.35-4.35m0 0A7.5 7.5 0 1 0 6.04 6.04a7.5 7.5 0 0 0 10.61 10.61Z"
            />
          </svg>

          <input
            v-model="search"
            type="text"
            :placeholder="searchPlaceholder"
            class="w-64 rounded-xl border border-slate-200 py-2 pl-9 pr-3 text-sm outline-none transition focus:border-primary focus:ring-2 focus:ring-primary/10"
          />
        </div>

        <!-- Selection -->
        <span
          v-if="selectable && selectedRows.length"
          class="rounded-lg bg-primary/10 px-3 py-2 text-xs font-medium text-primary"
        >
          {{ selectedRows.length }} selected
        </span>
      </div>

      <div class="flex items-center gap-2">

        <slot name="toolbar" />
      </div>
    </div>

    <!-- Table -->
    <div class="overflow-x-auto">
      <table class="w-full text-left text-sm">
        <thead class="bg-slate-50">
          <tr>
            <!-- Select -->
            <th v-if="selectable" class="w-12 px-4 py-3">
              <input
                type="checkbox"
                :checked="allSelected"
                class="rounded border-slate-300 text-primary focus:ring-primary"
                @change="toggleSelectAll"
              />
            </th>

            <th
              v-for="column in visibleColumnList"
              :key="column.key"
              class="whitespace-nowrap px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500"
              :class="{
                'cursor-pointer select-none hover:text-slate-700':
                  sortable && column.sortable !== false,
              }"
              @click="sort(column)"
            >
              <div class="flex items-center gap-1">
                {{ column.label }}

                <span v-if="sortBy === column.key" class="text-primary">
                  {{ sortDirection === "asc" ? "↑" : "↓" }}
                </span>
              </div>
            </th>

            <th v-if="$slots.actions" class="w-24 px-4 py-3 text-right">Actions</th>
          </tr>
        </thead>

        <tbody v-if="!loading && paginatedData.length" class="divide-y divide-slate-100">
          <tr
            v-for="row in paginatedData"
            :key="row[rowKey]"
            class="transition hover:bg-slate-50"
            @click="emit('row-click', row)"
          >
            <td v-if="selectable" class="px-4 py-3" @click.stop>
              <input
                type="checkbox"
                :checked="isSelected(row)"
                class="rounded border-slate-300 text-primary focus:ring-primary"
                @change="toggleRow(row)"
              />
            </td>

            <td
              v-for="column in visibleColumnList"
              :key="column.key"
              class="px-4 py-3 text-slate-700"
            >
              <slot
                :name="`cell-${column.key}`"
                :row="row"
                :value="getValue(row, column.key)"
              >
                {{ getValue(row, column.key) ?? "—" }}
              </slot>
            </td>

            <td v-if="$slots.actions" class="px-4 py-3 text-right" @click.stop>
              <slot name="actions" :row="row" />
            </td>
          </tr>
        </tbody>

        <!-- Loading -->
        <tbody v-if="loading">
          <tr>
            <td
              :colspan="
                visibleColumnList.length + (selectable ? 1 : 0) + ($slots.actions ? 1 : 0)
              "
              class="px-4 py-16 text-center"
            >
              <div class="inline-flex items-center gap-2 text-sm text-slate-500">
                <svg class="h-5 w-5 animate-spin" fill="none" viewBox="0 0 24 24">
                  <circle
                    class="opacity-25"
                    cx="12"
                    cy="12"
                    r="10"
                    stroke="currentColor"
                    stroke-width="4"
                  />
                  <path
                    class="opacity-75"
                    fill="currentColor"
                    d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4Z"
                  />
                </svg>

                Loading...
              </div>
            </td>
          </tr>
        </tbody>

        <!-- Empty -->
        <tbody v-if="!loading && !paginatedData.length">
          <tr>
            <td
              :colspan="
                visibleColumnList.length + (selectable ? 1 : 0) + ($slots.actions ? 1 : 0)
              "
              class="px-4 py-16 text-center"
            >
              <div class="flex flex-col items-center justify-center">
                <div
                  class="mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-slate-100"
                >
                  <svg
                    class="h-6 w-6 text-slate-400"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                  >
                    <path
                      stroke-linecap="round"
                      stroke-linejoin="round"
                      stroke-width="2"
                      d="M20 13V7a2 2 0 0 0-2-2h-3l-2-2H9L7 5H4a2 2 0 0 0-2 2v6m18 0v4a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2v-4m18 0H2"
                    />
                  </svg>
                </div>

                <p class="text-sm font-medium text-slate-600">
                  {{ emptyText }}
                </p>

                <button
                  v-if="search"
                  type="button"
                  class="mt-2 text-xs font-medium text-primary hover:underline"
                  @click="clearSearch"
                >
                  Clear search
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- Pagination -->
    <div
      v-if="pagination"
      class="flex flex-col gap-3 border-t border-slate-200 px-4 py-3 sm:flex-row sm:items-center sm:justify-between"
    >
      <div class="flex items-center gap-2 text-xs text-slate-500">
        <span>Rows per page</span>

        <select
          v-model="currentPageSize"
          class="rounded-lg border border-slate-200 px-2 py-1.5 text-xs outline-none focus:border-primary"
          @change="changePageSize($event.target.value)"
        >
          <option v-for="size in pageSizeOptions" :key="size" :value="size">
            {{ size }}
          </option>
        </select>

        <span>
          Showing {{ showingFrom }}–{{ showingTo }} of {{ filteredData.length }}
        </span>
      </div>

      <div v-if="totalPages > 1" class="flex items-center gap-1">
        <button
          type="button"
          class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs disabled:cursor-not-allowed disabled:opacity-40"
          :disabled="currentPage === 1"
          @click="changePage(currentPage - 1)"
        >
          Previous
        </button>

        <button
          v-for="page in totalPages"
          :key="page"
          type="button"
          class="h-8 min-w-8 rounded-lg px-2 text-xs font-medium transition"
          :class="
            currentPage === page
              ? 'bg-primary text-white'
              : 'text-slate-600 hover:bg-slate-100'
          "
          @click="changePage(page)"
        >
          {{ page }}
        </button>

        <button
          type="button"
          class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs disabled:cursor-not-allowed disabled:opacity-40"
          :disabled="currentPage === totalPages"
          @click="changePage(currentPage + 1)"
        >
          Next
        </button>
      </div>
    </div>
  </div>
</template>
