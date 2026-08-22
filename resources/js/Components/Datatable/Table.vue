<script setup>
import { computed, ref, watch, onBeforeUnmount } from "vue";
import { router } from "@inertiajs/vue3";
import Toolbar from "./Toolbar.vue";
import TableHeader from "./TableHeader.vue";
import TableBody from "./TableBody.vue";
import TablePagination from "./TablePagination.vue";

const props = defineProps({
  mode: {
    type: String,
    default: "client",
    validator: (value) => ["client", "server"].includes(value),
  },

  data: {
    type: [Array, Object],
    default: () => [],
  },

  columns: {
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

  searchDebounce: {
    type: Number,
    default: 400,
  },

  sortable: {
    type: Boolean,
    default: true,
  },

  defaultSort: {
    type: String,
    default: null,
  },

  defaultDirection: {
    type: String,
    default: "asc",
    validator: (value) => ["asc", "desc"].includes(value),
  },

  filterable: {
    type: Boolean,
    default: true,
  },

  filters: {
    type: Object,
    default: () => ({}),
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

  route: {
    type: String,
    default: null,
  },

  queryParams: {
    type: Object,
    default: () => ({}),
  },

  preserveState: {
    type: Boolean,
    default: true,
  },

  preserveScroll: {
    type: Boolean,
    default: true,
  },
  primaryColor:{
    type:String,
    default:"#3D98AB"
  }
});

const emit = defineEmits([
  "row-click",
  "selection-change",
  "search",
  "sort",
  "filter-change",
  "page-change",
  "page-size-change",
  "query-change",
]);

const search = ref("");

const currentPage = ref(
  props.mode === "server" ? Number(props.data?.current_page ?? 1) : 1
);

const currentPageSize = ref(
  props.mode === "server"
    ? Number(props.data?.per_page ?? props.pageSize)
    : props.pageSize
);

const sortBy = ref(props.defaultSort);

const sortDirection = ref(props.defaultDirection);

const selectedRows = ref([]);

const activeFilters = ref({
  ...props.filters,
});

let searchTimer = null;

const tableColumns = computed(() => {
  return Array.isArray(props.columns)
    ? props.columns.filter((column) => column && typeof column === "object" && column.key)
    : [];
});

const visibleColumns = computed(() => {
  return tableColumns.value.filter((column) => column.hidden !== true);
});

function getValue(row, key) {
  if (!row || !key) {
    return null;
  }

  return String(key)
    .split(".")
    .reduce((value, part) => value?.[part], row);
}

const rows = computed(() => {
  if (props.mode === "server") {
    return Array.isArray(props.data?.data) ? props.data.data : [];
  }

  return Array.isArray(props.data) ? props.data : [];
});

const processedClientRows = computed(() => {
  if (props.mode !== "client") {
    return rows.value;
  }

  let result = [...rows.value];

  const query = search.value.trim().toLowerCase();

  if (query) {
    result = result.filter((row) =>
      tableColumns.value.some((column) => {
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

  Object.entries(activeFilters.value).forEach(([key, filterValue]) => {
    if (filterValue === "" || filterValue === null || filterValue === undefined) {
      return;
    }

    result = result.filter((row) => {
      const value = getValue(row, key);

      if (Array.isArray(filterValue)) {
        return filterValue.map(String).includes(String(value));
      }
      return String(value ?? "") === String(filterValue);
    });
  });

  if (props.sortable && sortBy.value) {
    result.sort((a, b) => {
      const aValue = getValue(a, sortBy.value);

      const bValue = getValue(b, sortBy.value);

      if (aValue === null || aValue === undefined) {
        return 1;
      }

      if (bValue === null || bValue === undefined) {
        return -1;
      }

      if (aValue === bValue) {
        return 0;
      }

      if (typeof aValue === "number" && typeof bValue === "number") {
        return sortDirection.value === "asc" ? aValue - bValue : bValue - aValue;
      }

      const comparison = String(aValue).localeCompare(String(bValue), undefined, {
        numeric: true,
        sensitivity: "base",
      });

      return sortDirection.value === "asc" ? comparison : -comparison;
    });
  }

  return result;
});

const total = computed(() => {
  if (props.mode === "server") {
    return Math.max(0, Number(props.data?.total ?? 0));
  }

  return processedClientRows.value.length;
});

const totalPages = computed(() => {
  if (!props.pagination) {
    return 1;
  }

  if (props.mode === "server") {
    return Math.max(1, Number(props.data?.last_page ?? 1));
  }

  return Math.max(1, Math.ceil(processedClientRows.value.length / currentPageSize.value));
});

const displayRows = computed(() => {
  if (props.mode === "server") {
    return rows.value;
  }

  if (!props.pagination) {
    return processedClientRows.value;
  }

  const start = (currentPage.value - 1) * currentPageSize.value;

  return processedClientRows.value.slice(start, start + currentPageSize.value);
});

const showingFrom = computed(() => {
  if (!total.value) {
    return 0;
  }

  if (props.mode === "server") {
    return Number(props.data?.from ?? 1);
  }

  if (!props.pagination) {
    return 1;
  }

  return (currentPage.value - 1) * currentPageSize.value + 1;
});

const showingTo = computed(() => {
  if (!total.value) {
    return 0;
  }

  if (props.mode === "server") {
    return Number(props.data?.to ?? total.value);
  }

  if (!props.pagination) {
    return total.value;
  }

  return Math.min(currentPage.value * currentPageSize.value, total.value);
});

function buildServerParams() {
  const params = {
    ...props.queryParams,

    search: search.value.trim() || undefined,

    page: currentPage.value,

    per_page: currentPageSize.value,

    sort: sortBy.value || undefined,

    direction: sortBy.value ? sortDirection.value : undefined,
  };

  Object.entries(activeFilters.value).forEach(([key, value]) => {
    if (value === null || value === undefined || value === "") {
      return;
    }

    if (Array.isArray(value)) {
      value.forEach((item, index) => {
        params[`filters[${key}][${index}]`] = item;
      });

      return;
    }

    params[`filters[${key}]`] = value;
  });

  Object.keys(params).forEach((key) => {
    if (params[key] === undefined || params[key] === null || params[key] === "") {
      delete params[key];
    }
  });

  return params;
}

function loadServerData() {
  if (props.mode !== "server") {
    return;
  }

  if (!props.route) {
    console.warn("DataTable: `route` prop is required in server mode.");

    return;
  }

  const params = buildServerParams();

  emit("query-change", params);

  router.get(props.route, params, {
    preserveState: props.preserveState,

    preserveScroll: props.preserveScroll,

    replace: true,

    showProgress: false,
  });
}

watch(search, (value) => {
  if (props.mode === "client") {
    currentPage.value = 1;

    emit("search", value);

    return;
  }

  clearTimeout(searchTimer);

  searchTimer = setTimeout(() => {
    currentPage.value = 1;

    loadServerData();
  }, props.searchDebounce);
});

function sort(column) {
  if (!props.sortable || !column || column.sortable === false) {
    return;
  }

  if (sortBy.value === column.key) {
    sortDirection.value = sortDirection.value === "asc" ? "desc" : "asc";
  } else {
    sortBy.value = column.key;

    sortDirection.value = "asc";
  }

  emit("sort", {
    column: sortBy.value,

    direction: sortDirection.value,
  });

  if (props.mode === "server") {
    currentPage.value = 1;

    loadServerData();
  }
}

function updateFilter(key, value) {
  if (value === "" || value === null || value === undefined) {
    delete activeFilters.value[key];
  } else {
    activeFilters.value[key] = value;
  }

  currentPage.value = 1;

  emit("filter-change", {
    ...activeFilters.value,
  });

  if (props.mode === "server") {
    loadServerData();
  }
}

function clearFilters() {
  activeFilters.value = {};

  currentPage.value = 1;

  emit("filter-change", {});

  if (props.mode === "server") {
    loadServerData();
  }
}

const hasActiveFilters = computed(() => {
  return Object.values(activeFilters.value).some(
    (value) => value !== "" && value !== null && value !== undefined
  );
});

function changePage(page) {
  const nextPage = Number(page);

  if (!Number.isFinite(nextPage)) {
    return;
  }

  if (nextPage < 1 || nextPage > totalPages.value) {
    return;
  }

  currentPage.value = nextPage;

  emit("page-change", nextPage);

  if (props.mode === "server") {
    loadServerData();
  }
}

function changePageSize(size) {
  const nextSize = Number(size);

  if (!Number.isFinite(nextSize) || nextSize <= 0) {
    return;
  }

  currentPageSize.value = nextSize;

  currentPage.value = 1;

  emit("page-size-change", nextSize);

  if (props.mode === "server") {
    loadServerData();
  }
}

const paginationPages = computed(() => {
  const last = totalPages.value;

  const current = currentPage.value;

  if (last <= 7) {
    return Array.from(
      {
        length: last,
      },
      (_, index) => index + 1
    );
  }

  const pages = [];

  pages.push(1);

  if (current > 4) {
    pages.push("...");
  }

  const start = Math.max(2, current - 1);

  const end = Math.min(last - 1, current + 1);

  for (let page = start; page <= end; page++) {
    pages.push(page);
  }

  if (current < last - 3) {
    pages.push("...");
  }

  pages.push(last);

  return pages;
});

const allSelected = computed(() => {
  if (!displayRows.value.length) {
    return false;
  }

  return displayRows.value.every((row) =>
    selectedRows.value.includes(row?.[props.rowKey])
  );
});

function toggleSelectAll() {
  const ids = displayRows.value
    .map((row) => row?.[props.rowKey])
    .filter((id) => id !== null && id !== undefined);

  if (allSelected.value) {
    selectedRows.value = selectedRows.value.filter((id) => !ids.includes(id));
  } else {
    selectedRows.value = [...new Set([...selectedRows.value, ...ids])];
  }

  emit("selection-change", selectedRows.value);
}

function toggleRow(row) {
  if (!row) {
    return;
  }

  const id = row[props.rowKey];

  if (id === null || id === undefined) {
    return;
  }

  if (selectedRows.value.includes(id)) {
    selectedRows.value = selectedRows.value.filter((selectedId) => selectedId !== id);
  } else {
    selectedRows.value.push(id);
  }

  emit("selection-change", selectedRows.value);
}

function isSelected(row) {
  return !!row && selectedRows.value.includes(row[props.rowKey]);
}

function clearSearch() {
  search.value = "";

  if (props.mode === "server") {
    currentPage.value = 1;

    loadServerData();
  }
}

function clearSelection() {
  selectedRows.value = [];

  emit("selection-change", []);
}

watch(
  () => props.data?.current_page,
  (page) => {
    if (props.mode === "server" && page !== undefined && page !== null) {
      currentPage.value = Number(page);
    }
  },
  {
    immediate: true,
  }
);

watch(
  () => props.data?.per_page,
  (perPage) => {
    if (props.mode === "server" && perPage !== undefined && perPage !== null) {
      currentPageSize.value = Number(perPage);
    }
  },
  {
    immediate: true,
  }
);
watch(
  () => props.filters,
  (value) => {
    activeFilters.value = {
      ...value,
    };
  },
  {
    deep: true,
  }
);

onBeforeUnmount(() => {
  clearTimeout(searchTimer);
});

defineExpose({
  clearSearch,
  clearFilters,
  clearSelection,
  loadServerData,
});
</script>

<template>
  <div class="overflow-hidden">
    <Toolbar
      v-model:search="search"
      :searchable="searchable"
      :search-placeholder="searchPlaceholder"
      :filterable="filterable"
      :has-active-filters="hasActiveFilters"
      :selectable="selectable"
      :selected-count="selectedRows.length"
      @clear-filters="clearFilters"
      :primaryColor="primaryColor"
    >
      <template #toolbar>
        <slot name="toolbar" :selected="selectedRows" />
      </template>

      <template #toolbar-right>
        <slot name="toolbar-right" :selected="selectedRows" />
      </template>

      
    </Toolbar>

    <div class="overflow-x-auto">
      <table class="w-full min-w-full text-left text-sm">
        <TableHeader
          :columns="visibleColumns"
          :selectable="selectable"
          :sortable="sortable"
          :sort-by="sortBy"
          :sort-direction="sortDirection"
          :filterable="filterable"
          :active-filters="activeFilters"
          :all-selected="allSelected"
          :has-rows="displayRows.length > 0"
          :actions="!!$slots.actions"
          @sort="sort"
          @filter-change="updateFilter"
          @toggle-select-all="toggleSelectAll"
        />

        <TableBody
          :rows="displayRows"
          :columns="visibleColumns"
          :loading="loading"
          :selectable="selectable"
          :row-key="rowKey"
          :selected-rows="selectedRows"
          :empty-text="emptyText"
          :search="search"
          :actions="!!$slots.actions"
          @row-click="emit('row-click', $event)"
          @toggle-row="toggleRow"
          @clear-search="clearSearch"
        >
          <template
            v-for="column in visibleColumns"
            :key="column.key"
            #[`cell-${column.key}`]="slotProps"
          >
            <slot :name="`cell-${column.key}`" v-bind="slotProps" />
          </template>

          <template #actions="slotProps">
            <slot name="actions" v-bind="slotProps" />
          </template>
        </TableBody>
        <tfoot>
          <slot name="footer" />
        </tfoot>
      </table>
    </div>
    <TablePagination
      v-if="pagination"
      :current-page="currentPage"
      :total-pages="totalPages"
      :current-page-size="currentPageSize"
      :page-size-options="pageSizeOptions"
      :showing-from="showingFrom"
      :showing-to="showingTo"
      :total="total"
      @page-change="changePage"
      @page-size-change="changePageSize"
      :selected-count="selectedRows.length"
    />
  </div>
</template>
