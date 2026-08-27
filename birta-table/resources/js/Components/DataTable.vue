<script setup>
import { computed, ref, watch, onMounted, onBeforeUnmount } from "vue";

import { router } from "@inertiajs/vue3";
import { useTableTheme } from "../composables/useTableTheme";

const theme1 = useTableTheme();

//  composer require birta/birta-table:@dev
//  php artisan vendor:publish --tag=birta-table-config
/*
|--------------------------------------------------------------------------
| Props
|--------------------------------------------------------------------------
*/

const props = defineProps({
  /*
    |--------------------------------------------------------------------------
    | Data
    |--------------------------------------------------------------------------
    */

  mode: {
    type: String,
    default: "client",
    validator: (value) => ["client", "server"].includes(value),
  },

  data: {
    type: [Array, Object],
    default: () => [],
  },

  /*
    |--------------------------------------------------------------------------
    | Columns
    |--------------------------------------------------------------------------
    */

  columns: {
    type: Array,
    default: () => [],
  },

  /*
    |--------------------------------------------------------------------------
    | Filters
    |--------------------------------------------------------------------------
    */

  filters: {
    type: Array,
    default: () => [],
  },

  /*
    |--------------------------------------------------------------------------
    | General
    |--------------------------------------------------------------------------
    */

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
  },

  filterable: {
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

 
  /*
    |--------------------------------------------------------------------------
    | Selection
    |--------------------------------------------------------------------------
    */

  selectable: {
    type: Boolean,
    default: false,
  },

  rowKey: {
    type: String,
    default: "id",
  },

  /*
    |--------------------------------------------------------------------------
    | Server
    |--------------------------------------------------------------------------
    */

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

  /*
    |--------------------------------------------------------------------------
    | View
    |--------------------------------------------------------------------------
    */

  defaultView: {
    type: String,
    default: "table",
  },

  showViewSwitcher: {
    type: Boolean,
    default: true,
  },

  showColumnManager: {
    type: Boolean,
    default: true,
  },

  /*
    |--------------------------------------------------------------------------
    | Export
    |--------------------------------------------------------------------------
    */

  exportable: {
    type: Boolean,
    default: true,
  },

  exportFilename: {
    type: String,
    default: "data-export",
  },

  exportOptions: {
    type: Array,
    default: () => ["csv", "excel", "json", "print", "copy"],
  },
  theme: {
    type: Object,
    default: () => ({}),
  },
  showSerialNo: {
    type: Boolean,
    default: true,
  },
});

/*
|--------------------------------------------------------------------------
| Emits
|--------------------------------------------------------------------------
*/

const emit = defineEmits([
  "row-click",
  "selection-change",
  "search",
  "sort",
  "filter-change",
  "page-change",
  "page-size-change",
  "query-change",
  "export",
  "export-success",
  "export-error",
  "filter-open",
  "filter-close",
]);

const dtTheme = computed(() => {
  const theme = props.theme || {};

  return {
    container: {
      class: theme.container?.class || "",
      style: theme.container?.style || {},
    },

    toolbar: {
      class: theme.toolbar?.class || "",
      style: theme.toolbar?.style || {},
    },

    search: {
      wrapperClass: theme.search?.wrapperClass || "",
      inputClass: theme.search?.inputClass || "",
      iconClass: theme.search?.iconClass || "",
    },

    buttons: {
      base: theme.buttons?.base || "",
      default: theme.buttons?.default || "",
      primary: theme.buttons?.primary || "",
      danger: theme.buttons?.danger || "",
      filter: theme.buttons?.filter || "",
      export: theme.buttons?.export || "",
      column: theme.buttons?.column || "",
    },

    filter: {
      badge: theme.filter?.badge || "",
      modalOverlay: theme.filter?.modalOverlay || "",
      modal: theme.filter?.modal || "",
      header: theme.filter?.header || "",
      body: theme.filter?.body || "",
      footer: theme.filter?.footer || "",
      input: theme.filter?.input || "",
      select: theme.filter?.select || "",
    },

    table: {
      class: theme.table?.class || "",
      style: theme.table?.style || {},
    },

    thead: {
      class: theme.thead?.class || "",
      style: theme.thead?.style || {},
      rowClass: theme.thead?.rowClass || "",
      cellClass: theme.thead?.cellClass || "",
    },

    tbody: {
      class: theme.tbody?.class || "",
      style: theme.tbody?.style || {},
    },

    row: {
      class: theme.row?.class || "",
      style: theme.row?.style || {},
    },

    cell: {
      class: theme.cell?.class || "",
      style: theme.cell?.style || {},
    },

    selection: {
      checkboxClass: theme.selection?.checkboxClass || "",
    },

    pagination: {
      class: theme.pagination?.class || "",
      infoClass: theme.pagination?.infoClass || "",
      selectClass: theme.pagination?.selectClass || "",
      buttonClass: theme.pagination?.buttonClass || "",
      activeClass: theme.pagination?.activeClass || "",
      disabledClass: theme.pagination?.disabledClass || "",
    },

    export: {
      button: theme.export?.button || "",
      menu: theme.export?.menu || "",
      item: theme.export?.item || "",
      scope: theme.export?.scope || "",
    },

    columnManager: {
      button: theme.columnManager?.button || "",
      menu: theme.columnManager?.menu || "",
    },

    loading: {
      spinner: theme.loading?.spinner || "",
      text: theme.loading?.text || "",
    },

    empty: {
      icon: theme.empty?.icon || "",
      iconClass: theme.empty?.iconClass || "",
      text: theme.empty?.text || "",
      action: theme.empty?.action || "",
    },
    export: {
      menuBackground: "#ffffff",
      menuBorderColor: "#e2e8f0",

      itemColor: "#334155",
      itemHoverBackground: "#f8fafc",

      scopeBackground: "#ffffff",
      scopeBorderColor: "#e2e8f0",

      scopeItemColor: "#334155",
      scopeItemHoverBackground: "#f8fafc",

      scopeItemPadding: "8px",

      radioColor: "#3D98AB",

      selectedCountColor: "#94a3b8",

      disabledColor: "#cbd5e1",
    },
    columnManager: {
      button:
        "border-slate-200 bg-white px-3 text-sm font-medium text-[#041124] shadow-sm hover:border-[#628891] hover:bg-[#628891]/5",
      menu:
        "",
      menuHeader:
        "",
      title: "",
      resetButton:
        "",
      list: "",
      item:
        "",
      itemActive: "",
      label: "",
      checkbox:
        "",
    },
  };
});
/*
|--------------------------------------------------------------------------
| State
|--------------------------------------------------------------------------
*/

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

const pendingFilters = ref({});

const columnVisibility = ref({});

const showFilterModal = ref(false);

const showColumnModal = ref(false);

const exportMenuOpen = ref(false);

const exportScope = ref("current");

const exporting = ref(false);

const viewMode = ref(props.defaultView);

let searchTimer = null;

/*
|--------------------------------------------------------------------------
| Columns
|--------------------------------------------------------------------------
*/

const tableColumns = computed(() => {
  if (!Array.isArray(props.columns)) {
    return [];
  }

  return props.columns.filter(
    (column) => column && typeof column === "object" && column.key
  );
});

const visibleColumns = computed(() => {
  return tableColumns.value.filter(
    (column) => column.hidden !== true && columnVisibility.value[column.key] !== false
  );
});

function initializeColumns() {
  const result = {};

  tableColumns.value.forEach((column) => {
    result[column.key] = column.hidden !== true;
  });

  columnVisibility.value = result;
}

initializeColumns();

watch(() => props.columns, initializeColumns, {
  deep: true,
});

/*
|--------------------------------------------------------------------------
| Rows
|--------------------------------------------------------------------------
*/

const rows = computed(() => {
  if (props.mode === "server") {
    return Array.isArray(props.data?.data) ? props.data.data : [];
  }

  return Array.isArray(props.data) ? props.data : [];
});

/*
|--------------------------------------------------------------------------
| Get nested value
|--------------------------------------------------------------------------
*/

function getValue(row, key) {
  if (!row || !key) {
    return null;
  }

  return String(key)
    .split(".")
    .reduce((value, part) => value?.[part], row);
}

/*
|--------------------------------------------------------------------------
| Filters
|--------------------------------------------------------------------------
*/

const filterDefinitions = computed(() => {
  return Array.isArray(props.filters)
    ? props.filters.filter((filter) => filter && typeof filter === "object" && filter.key)
    : [];
});

const activeFilterCount = computed(() => {
  return Object.entries(pendingFilters.value).filter(([, value]) => {
    if (value === null || value === undefined || value === "") {
      return false;
    }

    if (typeof value === "object" && !Array.isArray(value)) {
      return Object.values(value).some((v) => v !== "" && v !== null && v !== undefined);
    }

    if (Array.isArray(value)) {
      return value.length > 0;
    }

    return true;
  }).length;
});

/*
|--------------------------------------------------------------------------
| Client processing
|--------------------------------------------------------------------------
*/

const processedClientRows = computed(() => {
  if (props.mode !== "client") {
    return rows.value;
  }

  let result = [...rows.value];

  /*
    |--------------------------------------------------------------------------
    | Search
    |--------------------------------------------------------------------------
    */

  const query = search.value.trim().toLowerCase();

  if (query) {
    result = result.filter((row) => {
      return tableColumns.value.some((column) => {
        if (column.searchable === false) {
          return false;
        }

        const value = getValue(row, column.key);

        return String(value ?? "")
          .toLowerCase()
          .includes(query);
      });
    });
  }

  /*
    |--------------------------------------------------------------------------
    | Filters
    |--------------------------------------------------------------------------
    */

  Object.entries(pendingFilters.value).forEach(([key, filterValue]) => {
    if (filterValue === null || filterValue === undefined || filterValue === "") {
      return;
    }

    const definition = filterDefinitions.value.find((filter) => filter.key === key);

    result = result.filter((row) => {
      const value = getValue(row, key);

      /*
            |--------------------------------------------------------------------------
            | Text
            |--------------------------------------------------------------------------
            */

      if (definition?.type === "text") {
        return String(value ?? "")
          .toLowerCase()
          .includes(String(filterValue).toLowerCase());
      }

      /*
            |--------------------------------------------------------------------------
            | Select
            |--------------------------------------------------------------------------
            */

      if (definition?.type === "select" || definition?.type === "multiselect") {
        if (Array.isArray(filterValue)) {
          return filterValue.map(String).includes(String(value));
        }

        return String(value) === String(filterValue);
      }

      /*
            |--------------------------------------------------------------------------
            | Boolean
            |--------------------------------------------------------------------------
            */

      if (definition?.type === "boolean") {
        return String(value) === String(filterValue);
      }

      /*
            |--------------------------------------------------------------------------
            | Number Range
            |--------------------------------------------------------------------------
            */

      if (definition?.type === "number-range") {
        const number = Number(value);

        if (
          filterValue.min !== "" &&
          filterValue.min !== null &&
          filterValue.min !== undefined
        ) {
          if (number < Number(filterValue.min)) {
            return false;
          }
        }

        if (
          filterValue.max !== "" &&
          filterValue.max !== null &&
          filterValue.max !== undefined
        ) {
          if (number > Number(filterValue.max)) {
            return false;
          }
        }

        return true;
      }

      /*
            |--------------------------------------------------------------------------
            | Date Range
            |--------------------------------------------------------------------------
            */

      if (definition?.type === "date-range") {
        if (!value) {
          return false;
        }

        const date = new Date(value);

        if (filterValue.from) {
          const from = new Date(filterValue.from);

          if (date < from) {
            return false;
          }
        }

        if (filterValue.to) {
          const to = new Date(filterValue.to);

          to.setHours(23, 59, 59, 999);

          if (date > to) {
            return false;
          }
        }

        return true;
      }

      /*
            |--------------------------------------------------------------------------
            | Default
            |--------------------------------------------------------------------------
            */

      return String(value ?? "") === String(filterValue);
    });
  });

  /*
    |--------------------------------------------------------------------------
    | Sort
    |--------------------------------------------------------------------------
    */

  if (props.sortable && sortBy.value) {
    const direction = sortDirection.value === "asc" ? 1 : -1;

    result = result
      .map((row, index) => ({
        row,
        index,
      }))
      .sort((a, b) => {
        const aValue = getValue(a.row, sortBy.value);

        const bValue = getValue(b.row, sortBy.value);

        if (aValue === null || aValue === undefined) {
          return 1;
        }

        if (bValue === null || bValue === undefined) {
          return -1;
        }

        if (aValue === bValue) {
          return a.index - b.index;
        }

        const aNumber = Number(aValue);

        const bNumber = Number(bValue);

        if (!Number.isNaN(aNumber) && !Number.isNaN(bNumber)) {
          return (aNumber - bNumber) * direction;
        }

        return (
          String(aValue).localeCompare(String(bValue), undefined, {
            numeric: true,
            sensitivity: "base",
          }) * direction
        );
      })
      .map((item) => item.row);
  }

  return result;
});

/*
|--------------------------------------------------------------------------
| Pagination
|--------------------------------------------------------------------------
*/

const total = computed(() => {
  if (props.mode === "server") {
    return Number(props.data?.total ?? 0);
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

/*
|--------------------------------------------------------------------------
| Display rows
|--------------------------------------------------------------------------
*/

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

/*
|--------------------------------------------------------------------------
| Pagination info
|--------------------------------------------------------------------------
*/

const showingFrom = computed(() => {
  if (!total.value) {
    return 0;
  }

  if (props.mode === "server") {
    return Number(props.data?.from ?? 1);
  }

  return props.pagination ? (currentPage.value - 1) * currentPageSize.value + 1 : 1;
});

const showingTo = computed(() => {
  if (!total.value) {
    return 0;
  }

  if (props.mode === "server") {
    return Number(props.data?.to ?? total.value);
  }

  return props.pagination
    ? Math.min(currentPage.value * currentPageSize.value, total.value)
    : total.value;
});

/*
|--------------------------------------------------------------------------
| Pagination buttons
|--------------------------------------------------------------------------
*/

const paginationPages = computed(() => {
  const last = totalPages.value;

  const current = currentPage.value;

  if (last <= 7) {
    return Array.from({ length: last }, (_, index) => index + 1);
  }

  const pages = [1];

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

/*
|--------------------------------------------------------------------------
| Server query
|--------------------------------------------------------------------------
*/

function buildServerParams(extra = {}) {
  const params = {
    ...props.queryParams,

    search: search.value.trim() || undefined,

    page: currentPage.value,

    per_page: currentPageSize.value,

    sort: sortBy.value || undefined,

    direction: sortBy.value ? sortDirection.value : undefined,

    ...extra,
  };

  /*
    |--------------------------------------------------------------------------
    | Filters
    |--------------------------------------------------------------------------
    */

  Object.entries(pendingFilters.value).forEach(([key, value]) => {
    if (value === null || value === undefined || value === "") {
      return;
    }

    if (Array.isArray(value)) {
      value.forEach((item, index) => {
        params[`filters[${key}][${index}]`] = item;
      });

      return;
    }

    if (typeof value === "object" && value !== null) {
      Object.entries(value).forEach(([rangeKey, rangeValue]) => {
        if (rangeValue !== null && rangeValue !== undefined && rangeValue !== "") {
          params[`filters[${key}][${rangeKey}]`] = rangeValue;
        }
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

/*
|--------------------------------------------------------------------------
| Server loading
|--------------------------------------------------------------------------
*/

function loadServerData() {
  if (props.mode !== "server") {
    return;
  }

  if (!props.route) {
    console.warn("DataTable: route is required in server mode.");

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

/*
|--------------------------------------------------------------------------
| Search
|--------------------------------------------------------------------------
*/

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

/*
|--------------------------------------------------------------------------
| Sort
|--------------------------------------------------------------------------
*/

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

  currentPage.value = 1;

  emit("sort", {
    column: sortBy.value,

    direction: sortDirection.value,
  });

  if (props.mode === "server") {
    loadServerData();
  }
}

/*
|--------------------------------------------------------------------------
| Filter helpers
|--------------------------------------------------------------------------
*/

function createEmptyFilter(filter) {
  if (filter.type === "number-range") {
    return {
      min: "",
      max: "",
    };
  }

  if (filter.type === "date-range") {
    return {
      from: "",
      to: "",
    };
  }

  return "";
}

function openFilters() {
  const copy = {};

  filterDefinitions.value.forEach((filter) => {
    if (Object.prototype.hasOwnProperty.call(pendingFilters.value, filter.key)) {
      const value = pendingFilters.value[filter.key];

      if (Array.isArray(value)) {
        copy[filter.key] = [...value];
      } else if (typeof value === "object" && value !== null) {
        copy[filter.key] = { ...value };
      } else {
        copy[filter.key] = value;
      }
    } else {
      copy[filter.key] = createEmptyFilter(filter);
    }
  });

  pendingFilters.value = copy;

  showFilterModal.value = true;

  emit("filter-open", {
    filters: copy,
  });
}

function closeFilters() {
  showFilterModal.value = false;

  emit("filter-close");
}

function updatePendingFilter(key, value) {
  pendingFilters.value = {
    ...pendingFilters.value,

    [key]: value,
  };
}

function applyFilters() {
  const cleaned = {};

  Object.entries(pendingFilters.value).forEach(([key, value]) => {
    if (value === null || value === undefined || value === "") {
      return;
    }

    if (typeof value === "object" && !Array.isArray(value)) {
      const object = {};

      Object.entries(value).forEach(([objectKey, objectValue]) => {
        if (objectValue !== null && objectValue !== undefined && objectValue !== "") {
          object[objectKey] = objectValue;
        }
      });

      if (Object.keys(object).length) {
        cleaned[key] = object;
      }

      return;
    }

    if (Array.isArray(value) && !value.length) {
      return;
    }

    cleaned[key] = value;
  });

  pendingFilters.value = cleaned;

  currentPage.value = 1;

  emit("filter-change", cleaned);

  showFilterModal.value = false;

  if (props.mode === "server") {
    loadServerData();
  }
}

function clearFilters() {
  pendingFilters.value = {};

  currentPage.value = 1;

  emit("filter-change", {});

  showFilterModal.value = false;

  if (props.mode === "server") {
    loadServerData();
  }
}

function clearSearch() {
  search.value = "";

  currentPage.value = 1;

  if (props.mode === "server") {
    loadServerData();
  }
}

/*
|--------------------------------------------------------------------------
| Pagination
|--------------------------------------------------------------------------
*/

function changePage(page) {
  const next = Number(page);

  if (!Number.isFinite(next)) {
    return;
  }

  if (next < 1 || next > totalPages.value) {
    return;
  }

  currentPage.value = next;

  emit("page-change", next);

  if (props.mode === "server") {
    loadServerData();
  }
}

function changePageSize(size) {
  const next = Number(size);

  if (!Number.isFinite(next) || next <= 0) {
    return;
  }

  currentPageSize.value = next;

  currentPage.value = 1;

  emit("page-size-change", next);

  if (props.mode === "server") {
    loadServerData();
  }
}

/*
|--------------------------------------------------------------------------
| Selection
|--------------------------------------------------------------------------
*/

const allSelected = computed(() => {
  if (!displayRows.value.length) {
    return false;
  }

  return displayRows.value.every((row) =>
    selectedRows.value.includes(row?.[props.rowKey])
  );
});

const someSelected = computed(() => {
  return selectedRows.value.length > 0 && !allSelected.value;
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

function clearSelection() {
  selectedRows.value = [];

  emit("selection-change", []);
}

/*
|--------------------------------------------------------------------------
| Server synchronization
|--------------------------------------------------------------------------
*/

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

/*
|--------------------------------------------------------------------------
| Formatting
|--------------------------------------------------------------------------
*/

function formatDate(value, column) {
  if (!value) {
    return "—";
  }

  try {
    const date = new Date(value);

    if (Number.isNaN(date.getTime())) {
      return value;
    }

    return new Intl.DateTimeFormat(
      column.locale || undefined,
      column.dateOptions || {
        year: "numeric",
        month: "short",
        day: "numeric",
      }
    ).format(date);
  } catch {
    return value;
  }
}

function formatDateTime(value) {
  if (!value) {
    return "—";
  }

  try {
    const date = new Date(value);

    if (Number.isNaN(date.getTime())) {
      return value;
    }

    return new Intl.DateTimeFormat(undefined, {
      year: "numeric",
      month: "short",
      day: "numeric",
      hour: "2-digit",
      minute: "2-digit",
    }).format(date);
  } catch {
    return value;
  }
}

function formatNumber(value, column) {
  if (value === null || value === undefined || value === "") {
    return "—";
  }

  return new Intl.NumberFormat(
    column.locale || undefined,
    column.numberOptions || {}
  ).format(Number(value));
}

function booleanValue(value) {
  return (
    value === true || value === 1 || value === "1" || value === "true" || value === "yes"
  );
}

function getBadgeClass(value, column) {
  if (typeof column.badgeClass === "function") {
    return column.badgeClass(value);
  }

  if (column.badgeClass && typeof column.badgeClass === "object") {
    return column.badgeClass[value] || "bg-slate-100 text-slate-700";
  }

  const normalized = String(value).toLowerCase();

  if (["active", "success", "approved", "completed"].includes(normalized)) {
    return "bg-emerald-100 text-emerald-700";
  }

  if (["inactive", "disabled", "failed", "rejected"].includes(normalized)) {
    return "bg-red-100 text-red-700";
  }

  if (["pending", "processing"].includes(normalized)) {
    return "bg-amber-100 text-amber-700";
  }

  return "bg-slate-100 text-slate-700";
}

/*
|--------------------------------------------------------------------------
| Columns
|--------------------------------------------------------------------------
*/

function toggleColumn(key) {
  columnVisibility.value[key] = !columnVisibility.value[key];
}

function columnLabel(column) {
  return column.label || column.key;
}

function isColumnSortable(column) {
  return props.sortable && column.sortable !== false;
}

/*
|--------------------------------------------------------------------------
| EXPORT
|--------------------------------------------------------------------------
*/

/*
 * Current page
 * Selected
 * All filtered
 */
const exportRows = ref([]);

const exportScopeOptions = [
  {
    value: "current",
    label: "Current Page",
  },
  {
    value: "selected",
    label: "Selected",
  },
  {
    value: "filtered",
    label: "All Filtered",
  },
];

/*
|--------------------------------------------------------------------------
| Export columns
|--------------------------------------------------------------------------
*/

function getExportColumns() {
  const columns = [];

  visibleColumns.value.forEach((column) => {
    if (column.exportable === false) {
      return;
    }

    // Main column
    columns.push({
      key: column.key,
      label: column.exportLabel || column.label || column.key,
    });

    // Additional export fields
    if (Array.isArray(column.items)) {
      column.items.forEach((item) => {
        if (!item || !item.key || item.exportable === false) {
          return;
        }

        // Don't duplicate the main column
        if (item.key === column.key) {
          return;
        }

        columns.push({
          key: item.key,
          label: item.exportLabel || item.label || item.key,
        });
      });
    }
  });

  return columns;
}

/*
|--------------------------------------------------------------------------
| Convert rows to export data
|--------------------------------------------------------------------------
*/

function getExportData(data) {
  const columns = getExportColumns();

  return data.map((row) => {
    const result = {};

    columns.forEach((column) => {
      let value = getValue(row, column.key);

      if (value === null || value === undefined) {
        value = "";
      }

      result[column.label || column.key] = value;
    });

    return result;
  });
}
/*
|--------------------------------------------------------------------------
| Fetch server export data
|--------------------------------------------------------------------------
|
| IMPORTANT:
|
| This uses the SAME route.
|
| Example:
|
| /branches?datatable_export=1
|
| No additional export route is required.
|
*/

async function fetchServerExportRows(scope) {
  if (!props.route) {
    throw new Error("DataTable: route is required for server export.");
  }

  let params = {};

  /*
    |--------------------------------------------------------------------------
    | All filtered
    |--------------------------------------------------------------------------
    */

  if (scope === "filtered") {
    params = buildServerParams({
      datatable_export: 1,
      export_scope: "filtered",
    });

    /*
     * Tell Laravel not to paginate.
     */
    params.page = 1;

    params.per_page = -1;
  }

  /*
    |--------------------------------------------------------------------------
    | Selected
    |--------------------------------------------------------------------------
    */

  if (scope === "selected") {
    params = buildServerParams({
      datatable_export: 1,
      export_scope: "selected",
    });

    params.page = 1;

    params.per_page = -1;

    /*
     * Send selected IDs.
     */

    selectedRows.value.forEach((id, index) => {
      params[`selected_ids[${index}]`] = id;
    });
  }

  /*
    |--------------------------------------------------------------------------
    | Build URL
    |--------------------------------------------------------------------------
    */

  const query = new URLSearchParams();

  Object.entries(params).forEach(([key, value]) => {
    if (value === undefined || value === null) {
      return;
    }

    query.append(key, String(value));
  });

  const url = `${props.route}?${query.toString()}`;

  /*
    |--------------------------------------------------------------------------
    | Fetch
    |--------------------------------------------------------------------------
    */

  const response = await fetch(url, {
    method: "GET",

    headers: {
      Accept: "application/json",

      "X-Requested-With": "XMLHttpRequest",
    },

    credentials: "same-origin",
  });

  /*
    |--------------------------------------------------------------------------
    | Content type
    |--------------------------------------------------------------------------
    */

  const contentType = response.headers.get("content-type") || "";

  /*
    |--------------------------------------------------------------------------
    | Error handling
    |--------------------------------------------------------------------------
    */

  if (!response.ok) {
    const text = await response.text();

    throw new Error(`Export request failed (${response.status}). ${text.slice(0, 300)}`);
  }

  /*
    |--------------------------------------------------------------------------
    | HTML response
    |--------------------------------------------------------------------------
    |
    | This is the exact reason for:
    |
    | Unexpected token '<'
    |
    */

  if (!contentType.includes("application/json")) {
    const text = await response.text();

    if (text.trim().startsWith("<")) {
      throw new Error(
        "The server returned an HTML/Inertia page instead of JSON. Add datatable_export handling to the existing controller route."
      );
    }

    throw new Error(
      `Expected JSON but received ${contentType || "unknown content type"}.`
    );
  }

  const result = await response.json();

  /*
    |--------------------------------------------------------------------------
    | Normalize response
    |--------------------------------------------------------------------------
    */

  if (Array.isArray(result)) {
    return result;
  }

  if (Array.isArray(result.data)) {
    return result.data;
  }

  if (Array.isArray(result.rows)) {
    return result.rows;
  }

  throw new Error("Export response does not contain a valid data array.");
}

/*
|--------------------------------------------------------------------------
| Get export rows
|--------------------------------------------------------------------------
*/

async function getExportRows(scope) {
  /*
    |--------------------------------------------------------------------------
    | Current page
    |--------------------------------------------------------------------------
    */

  if (scope === "current") {
    return displayRows.value;
  }

  /*
    |--------------------------------------------------------------------------
    | Client mode
    |--------------------------------------------------------------------------
    */

  if (props.mode === "client") {
    if (scope === "selected") {
      const selectedSet = new Set(selectedRows.value);

      return processedClientRows.value.filter((row) =>
        selectedSet.has(row?.[props.rowKey])
      );
    }

    return processedClientRows.value;
  }

  /*
    |--------------------------------------------------------------------------
    | Server mode
    |--------------------------------------------------------------------------
    */

  return await fetchServerExportRows(scope);
}

/*
|--------------------------------------------------------------------------
| Run export
|--------------------------------------------------------------------------
*/

async function runExport(type) {
  if (!props.exportOptions.includes(type)) {
    return;
  }

  /*
    |--------------------------------------------------------------------------
    | Validate selected
    |--------------------------------------------------------------------------
    */

  if (exportScope.value === "selected" && !selectedRows.value.length) {
    emit("export-error", {
      type,
      scope: exportScope.value,
      reason: "no-selection",
    });

    return;
  }

  exporting.value = true;

  try {
    /*
        |--------------------------------------------------------------------------
        | Fetch
        |--------------------------------------------------------------------------
        */

    const data = await getExportRows(exportScope.value);

    exportRows.value = data;

    /*
        |--------------------------------------------------------------------------
        | No data
        |--------------------------------------------------------------------------
        */

    if (!data.length) {
      emit("export-error", {
        type,
        scope: exportScope.value,
        reason: "no-data",
      });

      return;
    }

    /*
        |--------------------------------------------------------------------------
        | Event
        |--------------------------------------------------------------------------
        */

    emit("export", {
      type,

      scope: exportScope.value,

      rows: data,

      count: data.length,
    });

    /*
        |--------------------------------------------------------------------------
        | Export
        |--------------------------------------------------------------------------
        */

    switch (type) {
      case "csv":
        exportCSV(data);
        break;

      case "excel":
        await exportExcel(data);
        break;

      case "json":
        exportJSON(data);
        break;

      case "print":
        printData(data);
        break;

      case "copy":
        await copyData(data);
        break;
    }

    /*
        |--------------------------------------------------------------------------
        | Success
        |--------------------------------------------------------------------------
        */

    emit("export-success", {
      type,

      scope: exportScope.value,

      count: data.length,
    });
  } catch (error) {
    console.error("DataTable export error:", error);

    emit("export-error", {
      type,

      scope: exportScope.value,

      error,
    });
  } finally {
    exporting.value = false;

    exportMenuOpen.value = false;
  }
}

/*
|--------------------------------------------------------------------------
| CSV
|--------------------------------------------------------------------------
*/

function csvEscape(value) {
  const string = String(value ?? "");

  if (
    string.includes(",") ||
    string.includes('"') ||
    string.includes("\n") ||
    string.includes("\r")
  ) {
    return `"${string.replace(/"/g, '""')}"`;
  }

  return string;
}

function exportCSV(data) {
  const exportData = getExportData(data);

  if (!exportData.length) {
    return;
  }

  const headers = Object.keys(exportData[0]);

  const csv = [
    headers.map(csvEscape).join(","),

    ...exportData.map((row) => headers.map((header) => csvEscape(row[header])).join(",")),
  ].join("\r\n");

  /*
    |--------------------------------------------------------------------------
    | UTF-8 BOM
    |--------------------------------------------------------------------------
    |
    | Makes Nepali/Unicode work correctly in Excel.
    |
    */

  downloadFile("\uFEFF" + csv, `${props.exportFilename}.csv`, "text/csv;charset=utf-8;");
}

/*
|--------------------------------------------------------------------------
| JSON
|--------------------------------------------------------------------------
*/

function exportJSON(data) {
  const exportData = getExportData(data);

  downloadFile(
    JSON.stringify(exportData, null, 2),
    `${props.exportFilename}.json`,
    "application/json;charset=utf-8;"
  );
}

/*
|--------------------------------------------------------------------------
| Excel
|--------------------------------------------------------------------------
*/

async function exportExcel(data) {
  const XLSX = await import("xlsx");

  const exportData = getExportData(data);

  const worksheet = XLSX.utils.json_to_sheet(exportData);

  const workbook = XLSX.utils.book_new();

  XLSX.utils.book_append_sheet(workbook, worksheet, "Data");

  XLSX.writeFile(workbook, `${props.exportFilename}.xlsx`);
}

/*
|--------------------------------------------------------------------------
| Copy
|--------------------------------------------------------------------------
*/

async function copyData(data) {
  const exportData = getExportData(data);

  if (!exportData.length) {
    return;
  }

  const headers = Object.keys(exportData[0]);

  const text = [
    headers.join("\t"),

    ...exportData.map((row) => headers.map((header) => row[header] ?? "").join("\t")),
  ].join("\n");

  if (navigator.clipboard) {
    await navigator.clipboard.writeText(text);

    return;
  }

  /*
    |--------------------------------------------------------------------------
    | Fallback
    |--------------------------------------------------------------------------
    */

  const textarea = document.createElement("textarea");

  textarea.value = text;

  textarea.style.position = "fixed";

  textarea.style.opacity = "0";

  document.body.appendChild(textarea);

  textarea.select();

  document.execCommand("copy");

  textarea.remove();
}

/*
|--------------------------------------------------------------------------
| Print
|--------------------------------------------------------------------------
*/

function printData(data) {
  const exportData = getExportData(data);

  if (!exportData.length) {
    return;
  }

  const columns = getExportColumns();

  const html = `
        <!DOCTYPE html>
        <html>
        <head>
            <meta charset="UTF-8">

            <title>
                ${escapeHTML(props.exportFilename)}
            </title>

            <style>
                body {
                    font-family:
                        Arial,
                        sans-serif;

                    padding: 30px;
                }

                h2 {
                    margin-bottom:
                        20px;
                }

                table {
                    width: 100%;
                    border-collapse:
                        collapse;
                }

                th,
                td {
                    border:
                        1px solid #ddd;

                    padding:
                        8px;

                    text-align:
                        left;
                }

                th {
                    background:
                        #f8fafc;
                }

                @media print {
                    body {
                        padding: 0;
                    }
                }
            </style>
        </head>

        <body>

            <h2>
                ${escapeHTML(props.exportFilename)}
            </h2>

            <table>

                <thead id="birta-table-head">
                    <tr>
                      
                        ${columns
                          .map(
                            (column) =>
                              `<th>${escapeHTML(column.label || column.key)}</th>`
                          )
                          .join("")}
                    </tr>
                </thead>

                <tbody>

                    ${exportData
                      .map(
                        (row) => `
                                <tr>
                                    ${columns
                                      .map((column) => {
                                        const key = column.label || column.key;

                                        return `
                                                    <td>
                                                        ${escapeHTML(row[key])}
                                                    </td>
                                                `;
                                      })
                                      .join("")}
                                </tr>
                            `
                      )
                      .join("")}

                </tbody>

            </table>

        </body>
        </html>
    `;

  const printWindow = window.open("", "_blank", "width=1200,height=800");

  if (!printWindow) {
    throw new Error("Popup blocked. Please allow popups for printing.");
  }

  printWindow.document.write(html);

  printWindow.document.close();

  printWindow.focus();

  setTimeout(() => {
    printWindow.print();
  }, 250);
}

/*
|--------------------------------------------------------------------------
| HTML escape
|--------------------------------------------------------------------------
*/

function escapeHTML(value) {
  return String(value ?? "")
    .replace(/&/g, "&amp;")
    .replace(/</g, "&lt;")
    .replace(/>/g, "&gt;")
    .replace(/"/g, "&quot;")
    .replace(/'/g, "&#039;");
}

/*
|--------------------------------------------------------------------------
| Download
|--------------------------------------------------------------------------
*/

function downloadFile(content, filename, mimeType) {
  const blob = new Blob([content], {
    type: mimeType,
  });

  const url = URL.createObjectURL(blob);

  const link = document.createElement("a");

  link.href = url;

  link.download = filename;

  document.body.appendChild(link);

  link.click();

  link.remove();

  setTimeout(() => {
    URL.revokeObjectURL(url);
  }, 100);
}

/*
|--------------------------------------------------------------------------
| Escape
|--------------------------------------------------------------------------
*/

function handleEscape(event) {
  if (event.key === "Escape") {
    showFilterModal.value = false;

    showColumnModal.value = false;

    exportMenuOpen.value = false;
  }
}

onMounted(() => {
  window.addEventListener("keydown", handleEscape);
});

onBeforeUnmount(() => {
  clearTimeout(searchTimer);

  window.removeEventListener("keydown", handleEscape);
});

/*
|--------------------------------------------------------------------------
| Expose
|--------------------------------------------------------------------------
*/

defineExpose({
  clearSearch,
  clearFilters,
  clearSelection,
  loadServerData,
  runExport,

  openFilters,
  closeFilters,
  applyFilters,
  updatePendingFilter,

  filterDefinitions,
  pendingFilters,
  activeFilterCount,
  showFilterModal,
});
</script>

<template>
  <div :class="theme1.container?.class">
    <!-- ===================================================== -->
    <!-- TOOLBAR -->
    <!-- ===================================================== -->

    <div
      class="flex min-h-[68px] flex-wrap items-center justify-between gap-3"
      :class="theme1.toolbar.class"
    >
      <!-- LEFT -->

      <div class="flex min-w-0 flex-1 flex-wrap items-center gap-2">
        <!-- SEARCH -->

        <div
          v-if="searchable"
          class="relative w-full sm:w-[300px]"
          :class="theme1.search.wrapperClass"
        >
          <svg
            class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2"
            :class="theme1.search.iconClass"
            viewBox="0 0 24 24"
            fill="none"
            stroke="currentColor"
            stroke-width="2"
          >
            <circle cx="11" cy="11" r="8" />

            <path d="m21 21-4.3-4.3" />
          </svg>

          <input
            v-model="search"
            type="search"
            :placeholder="searchPlaceholder"
            class="w-full"
            :class="theme1.search.inputClass"
          />

          <button
            v-if="search"
            type="button"
            class="absolute right-2 top-1/2 flex h-6 w-6 -translate-y-1/2 items-center justify-center rounded text-slate-400 hover:bg-slate-100"
            :class="[theme1.buttons.base, theme1.buttons.export]"
            @click="clearSearch"
          >
            ×
          </button>
        </div>

        <!-- FILTER -->

        <button
          v-if="filterable && filterDefinitions.length"
          type="button"
          :class="[theme1.buttons.base, theme1.buttons.filter]"
          @click="openFilters"
        >
          <i class="fa fa-filter text-[12px]"></i>

          Filters

          <span v-if="activeFilterCount" :class="dtTheme.filter.badge">
            {{ activeFilterCount }}
          </span>
        </button>

        <!-- CLEAR -->

        <button
          v-if="activeFilterCount"
          type="button"
          class="inline-flex h-10 items-center rounded-lg bg-red-50 px-3 text-sm font-medium text-red-600 hover:bg-red-100"
          @click="clearFilters"
        >
          Clear
        </button>

        <!-- CUSTOM TOOLBAR -->

        <slot name="toolbar" :selected="selectedRows" />
      </div>

      <!-- RIGHT -->

      <div class="flex shrink-0 flex-wrap items-center gap-2">
        <!-- SELECTION -->

        <div v-if="selectable && selectedRows.length" class="text-sm text-slate-500">
          {{ selectedRows.length }}
          selected
        </div>

        <!-- EXPORT -->

        <div v-if="exportable" class="relative">
          <button
            type="button"
            :class="[theme1.buttons.base, theme1.buttons.export]"
            :disabled="exporting"
            @click="exportMenuOpen = !exportMenuOpen"
          >
            <span
              v-if="exporting"
              class="h-4 w-4 animate-spin rounded-full border-2 border-slate-300 border-t-slate-700"
            />

            <span v-else> ↓ </span>

            {{ exporting ? "Exporting..." : "Export" }}

            <span v-if="!exporting"> ▾ </span>
          </button>

          <!-- EXPORT MENU -->

          <div
            v-if="exportMenuOpen"
            class="absolute right-0 mt-2 w-56 overflow-hidden rounded-xl border border-slate-200/80 z-[999999] p-1.5 shadow-xl ring-1 ring-black/5 animate-in fade-in slide-in-from-top-2 duration-200 bg-white"
            :class="theme1.export.menu"
          >
            <!-- SCOPE -->

            <div class="mb-2 border-b border-slate-100 pb-2">
              <p
                class="px-2 py-1 text-[11px] font-semibold uppercase tracking-wide text-slate-400"
              >
                Export scope
              </p>

              <label
                v-for="option in exportScopeOptions"
                :key="option.value"
                class="flex cursor-pointer items-center gap-2 rounded-lg transition"
                :style="{
                  color:
                    option.value === 'selected' && !selectedRows.length
                      ? theme1.export.disabledColor
                      : theme1.export.scopeItemColor,

                  backgroundColor:
                    exportScope === option.value
                      ? theme1.export.scopeItemHoverBackground
                      : 'transparent',

                  padding: theme1.export.scopeItemPadding || '8px',
                }"
                @mouseenter="
                  if (!(option.value === 'selected' && !selectedRows.length)) {
                    $event.currentTarget.style.backgroundColor =
                      theme1.export.scopeItemHoverBackground;
                  }
                "
                @mouseleave="
                  if (exportScope !== option.value) {
                    $event.currentTarget.style.backgroundColor = 'transparent';
                  }
                "
              >
                <input
                  v-model="exportScope"
                  type="radio"
                  :value="option.value"
                  :disabled="option.value === 'selected' && !selectedRows.length"
                  class="h-4 w-4"
                />
                <!-- :style="{ accentColor: theme1.export.radioColor }" -->

                <span>
                  {{ option.label }}
                </span>

                <span
                  v-if="option.value === 'selected'"
                  class="ml-auto text-xs"
                  :style="{
                    color: theme1.export.selectedCountColor,
                  }"
                >
                  {{ selectedRows.length }}
                </span>
              </label>
            </div>

            <!-- FORMATS -->

            <p
              class="px-2 py-1 text-[11px] font-semibold uppercase tracking-wide text-slate-400"
            >
              Format
            </p>

            <button
              v-if="exportOptions.includes('csv')"
              type="button"
              class="flex w-full block items-center gap-2 rounded-lg px-3 py-2 text-left text-sm font-medium text-[#041124] transition-colors duration-150 hover:bg-slate-50 hover:text-[#041124]"
              @click="runExport('csv')"
            >
              <i
                class="fa-solid fa-file-csv"
                :style="{ color: theme1.export.iconColor }"
              ></i>
              CSV
            </button>

            <button
              v-if="exportOptions.includes('excel')"
              type="button"
              class="flex w-full block items-center gap-2 rounded-lg px-3 py-2 text-left text-sm font-medium text-[#041124] transition-colors duration-150 hover:bg-slate-50 hover:text-[#041124]"
              @click="runExport('excel')"
            >
              <i
                class="fa-solid fa-file-excel"
                :style="{ color: theme1.export.iconColor }"
              ></i
              >Excel
            </button>

            <button
              v-if="exportOptions.includes('json')"
              type="button"
              class="flex w-full block items-center gap-2 rounded-lg px-3 py-2 text-left text-sm font-medium text-[#041124] transition-colors duration-150 hover:bg-slate-50 hover:text-[#041124]"
              @click="runExport('json')"
            >
              <i
                class="fa-solid fa-file-code"
                :style="{ color: theme1.export.iconColor }"
              ></i
              >JSON
            </button>

            <button
              v-if="exportOptions.includes('copy')"
              type="button"
              class="flex w-full block items-center gap-2 rounded-lg px-3 py-2 text-left text-sm font-medium text-[#041124] transition-colors duration-150 hover:bg-slate-50 hover:text-[#041124]"
              @click="runExport('copy')"
            >
              <i class="fa-solid fa-copy" :style="{ color: theme1.export.iconColor }"></i>
              Copy
            </button>

            <button
              v-if="exportOptions.includes('print')"
              type="button"
              class="flex w-full block items-center gap-2 rounded-lg px-3 py-2 text-left text-sm font-medium text-[#041124] transition-colors duration-150 hover:bg-slate-50 hover:text-[#041124]"
              @click="runExport('print')"
            >
              <i class="fa-solid fa-print" :style="{ color: theme1.export.iconColor }"></i
              >Print
            </button>
          </div>
        </div>

        <!-- COLUMN MANAGER -->

        <div v-if="showColumnManager" class="relative">
          <!-- BUTTON -->

          <button
            type="button"
            :class="[theme1.buttons.base, theme1.columnManager.button]"
            @click="showColumnModal = !showColumnModal"
            class="items-center"
          >
            <i class="fa fa-columns relative mt-[2px]"></i>
            Columns
          </button>

          <!-- DROPDOWN -->

          <div
            v-if="showColumnModal"
            class="absolute right-0 z-50 mt-2"
            :class="theme1.columnManager.menu"
          >
            <!-- HEADER -->

            <div :class="theme1.columnManager.menuHeader">
              <span :class="theme1.columnManager.title"> Columns </span>

              <button
                type="button"
                :class="theme1.columnManager.resetButton"
                @click="initializeColumns"
              >
                Reset
              </button>
            </div>

            <!-- COLUMNS -->

            <div :class="theme1.columnManager.list">
              <label
                v-for="column in tableColumns"
                :key="column.key"
                :class="theme1.columnManager.item"
              >
                <input
                  type="checkbox"
                  :checked="columnVisibility[column.key]"
                  :class="theme1.columnManager.checkbox"
                  @change="toggleColumn(column.key)"
                />

                <span :class="theme1.columnManager.label">
                  {{ columnLabel(column) }}
                </span>
              </label>
            </div>
          </div>
        </div>
        <!-- CUSTOM -->

        <slot name="toolbar-right" :selected="selectedRows" />
      </div>
    </div>

    <!-- ===================================================== -->
    <!-- TABLE -->
    <!-- ===================================================== -->

    <div class="w-full overflow-x-auto">
      <table
        class="text-left text-sm"
        :class="theme1.table.class"
        :style="theme1.table.style"
      >
        <thead
          :class="theme1.thead.class"
                  >
          <tr :class="['border-b', theme1.thead.rowClass]">
            <!-- SELECT -->

            <th v-if="selectable" class="w-12 px-4 py-3">
              <input
                type="checkbox"
                :checked="allSelected"
                :indeterminate="someSelected"
                :class="theme1.selection.checkboxClass"
                @change="toggleSelectAll"
              />
            </th>

            <th v-if="showSerialNo" :class="[theme1.thead.cellClass]">#</th>

            <!-- COLUMNS -->

            <th
              v-for="column in visibleColumns"
              :key="column.key"
              :class="[
                theme1.thead.cellClass,
                {
                  'cursor-pointer select-none': isColumnSortable(column),
                },
              ]"
              @click="sort(column)"
            >
              <div class="flex items-center gap-1">
                {{ columnLabel(column) }}

                <span v-if="isColumnSortable(column)">
                  {{
                    sortBy === column.key ? (sortDirection === "asc" ? "↑" : "↓") : "↕"
                  }}
                </span>
              </div>
            </th>

            <!-- ACTIONS -->

            <th v-if="$slots.actions" class="px-4 py-3 text-right text-xs uppercase">
              Actions
            </th>
          </tr>
        </thead>

        <tbody :class="theme1.tbody.class">
          <!-- LOADING -->

          <tr v-if="loading">
            <td
              :colspan="
                visibleColumns.length + (selectable ? 1 : 0) + ($slots.actions ? 1 : 0)
              "
              class="px-4 py-14 text-center"
            >
              <div class="flex justify-center">
                <div
                  class="h-7 w-7 animate-spin rounded-full border-2 border-slate-200 border-t-[var(--dt-primary)]"
                />
              </div>
            </td>
          </tr>

          <!-- EMPTY -->

          <tr v-else-if="!displayRows.length">
            <td
              :colspan="
                visibleColumns.length + (selectable ? 1 : 0) + ($slots.actions ? 1 : 0)
              "
              class="px-4 py-14 text-center text-sm text-slate-500"
            >
              {{ emptyText }}
            </td>
          </tr>

          <!-- ROW -->

          <tr
            v-for="(row, index) in displayRows"
            v-else
            :key="row[rowKey]"
            :class="theme1.row.class"
            :style="theme1.row.style"
            @click="emit('row-click', row)"
          >
            <!-- SELECT -->

            <td v-if="selectable" class="px-4 py-3" @click.stop>
              <input
                type="checkbox"
                :checked="allSelected"
                :indeterminate="someSelected"
                :class="theme1.selection.checkboxClass"
                @change="toggleSelectAll"
              />
            </td>

            <!-- CELLS -->
            <td
              :class="[theme1.cell.class, viewMode === 'compact' ? 'py-2' : '']"
              :style="theme1.cell.style"
            >
              {{ index + 1 }}
            </td>
            <td
              v-for="column in visibleColumns"
              :key="column.key"
              :class="[theme1.cell.class, viewMode === 'compact' ? 'py-2' : '']"
              :style="theme1.cell.style"
            >
              <!-- SLOT -->

              <slot
                v-if="$slots[`cell-${column.key}`]"
                :name="`cell-${column.key}`"
                :row="row"
                :value="getValue(row, column.key)"
                :column="column"
              />

              <!-- TEXT -->

              <span
                v-else-if="!column.type || column.type === 'text'"
                :class="column.class"
              >
                {{ getValue(row, column.key) ?? "—" }}
              </span>

              <!-- NUMBER -->

              <span v-else-if="column.type === 'number'">
                {{ formatNumber(getValue(row, column.key), column) }}
              </span>

              <!-- DATE -->

              <span v-else-if="column.type === 'date'">
                {{ formatDate(getValue(row, column.key), column) }}
              </span>

              <!-- DATETIME -->

              <span v-else-if="column.type === 'datetime'">
                {{ formatDateTime(getValue(row, column.key)) }}
              </span>

              <!-- BOOLEAN -->

              <span v-else-if="column.type === 'boolean'">
                <span
                  v-if="booleanValue(getValue(row, column.key))"
                  class="inline-flex rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-medium text-emerald-700"
                >
                  Yes
                </span>

                <span
                  v-else
                  class="inline-flex rounded-full bg-slate-100 px-2.5 py-1 text-xs text-slate-500"
                >
                  No
                </span>
              </span>

              <!-- BADGE -->

              <span
                v-else-if="column.type === 'badge'"
                class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium"
                :class="getBadgeClass(getValue(row, column.key), column)"
              >
                {{ getValue(row, column.key) ?? "—" }}
              </span>

              <!-- DEFAULT -->

              <span v-else>
                {{ getValue(row, column.key) ?? "—" }}
              </span>
            </td>

            <!-- ACTIONS -->

            <td v-if="$slots.actions" class="px-4 py-3 text-right" @click.stop>
              <slot name="actions" :row="row" />
            </td>
          </tr>
        </tbody>
        <tfoot>
          <slot name="footer" />
        </tfoot>
      </table>
    </div>

    <!-- ===================================================== -->
    <!-- PAGINATION -->
    <!-- ===================================================== -->

    <div v-if="pagination" :class="['flex flex-col gap-3', theme1.pagination.class]">
      <div :class="theme1.pagination.infoClass">
        Showing
        <span class="font-medium">
          {{ showingFrom }}
        </span>
        -
        <span class="font-medium">
          {{ showingTo }}
        </span>
        of
        <span class="font-medium">
          {{ total }}
        </span>
        records
      </div>

      <div class="flex flex-wrap items-center gap-2">
        <select
          :value="currentPageSize"
          :class="theme1.pagination.selectClass"
          @change="changePageSize($event.target.value)"
        >
          <option v-for="size in theme1?.pagination_option?.options" :key="size" :value="size">
            {{ size }} / page
          </option>
        </select>

        <button
          type="button"
          class="h-9 rounded-lg border px-3 text-xs disabled:opacity-40"
          :disabled="currentPage <= 1"
          @click="changePage(1)"
        >
          First
        </button>

        <button
          type="button"
          :class="[
            theme1.pagination.buttonClass,
            {
              [theme1.pagination.disabledClass]: currentPage <= 1,
            },
          ]"
          :disabled="currentPage <= 1"
          @click="changePage(currentPage - 1)"
        >
          ‹
        </button>

        <template v-for="(page, index) in paginationPages" :key="`${page}-${index}`">
          <span v-if="page === '...'" class="px-1"> … </span>

          <button
            v-else
            type="button"
            :class="[
              theme1.pagination.buttonClass,
              currentPage === page ? theme1.pagination.activeClass : '',
            ]"
            @click="changePage(page)"
          >
            {{ page }}
          </button>
        </template>

        <button
          type="button"
          class="h-9 w-9 rounded-lg border"
          :disabled="currentPage >= totalPages"
          @click="changePage(currentPage + 1)"
        >
          ›
        </button>

        <button
          type="button"
          class="h-9 rounded-lg border px-3 text-xs disabled:opacity-40"
          :disabled="currentPage >= totalPages"
          @click="changePage(totalPages)"
        >
          Last
        </button>
      </div>
    </div>

    <!-- ===================================================== -->
    <!-- FILTER MODAL -->
    <!-- ===================================================== -->

    <slot
      name="filter-modal"
      :show="showFilterModal"
      :filters="pendingFilters"
      :definitions="filterDefinitions"
      :active-count="activeFilterCount"
      :update="updatePendingFilter"
      :apply="applyFilters"
      :clear="clearFilters"
      :close="closeFilters"
    />
  </div>
</template>

<style scoped>
input[type="search"]::-webkit-search-cancel-button {
  display: none;
}
</style>
