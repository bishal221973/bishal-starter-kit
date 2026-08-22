<script setup>
import {
  computed,
  ref,
  watch,
  onBeforeUnmount,
} from "vue";
import { router } from "@inertiajs/vue3";

/*
|--------------------------------------------------------------------------
| Props
|--------------------------------------------------------------------------
*/

const props = defineProps({
  /**
   * client | server
   */
  mode: {
    type: String,
    default: "client",

    validator: (value) =>
      ["client", "server"].includes(value),
  },

  /**
   * Client:
   * [
   *   { id: 1, name: "John" }
   * ]
   *
   * Server:
   * Laravel paginator object
   *
   * {
   *   data: [],
   *   current_page: 1,
   *   last_page: 10,
   *   per_page: 10,
   *   total: 100,
   *   from: 1,
   *   to: 10
   * }
   */
  data: {
    type: [Array, Object],
    default: () => [],
  },

  /**
   * Table columns.
   *
   * Example:
   *
   * [
   *   {
   *     key: "name",
   *     label: "Name",
   *     sortable: true,
   *     searchable: true,
   *   }
   * ]
   */
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

  searchDebounce: {
    type: Number,
    default: 400,
  },

  defaultSort: {
    type: String,
    default: null,
  },

  defaultDirection: {
    type: String,
    default: "asc",

    validator: (value) =>
      ["asc", "desc"].includes(value),
  },

  /**
   * Laravel route used for server-side requests.
   */
  route: {
    type: String,
    default: null,
  },

  /**
   * Additional parameters always sent
   * to Laravel.
   */
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
  "page-change",
  "page-size-change",
  "query-change",
]);

/*
|--------------------------------------------------------------------------
| State
|--------------------------------------------------------------------------
*/

const search = ref("");

const currentPage = ref(
  props.mode === "server"
    ? Number(props.data?.current_page ?? 1)
    : 1
);

const currentPageSize = ref(
  props.mode === "server"
    ? Number(
        props.data?.per_page ??
          props.pageSize
      )
    : props.pageSize
);

const sortBy = ref(
  props.defaultSort
);

const sortDirection = ref(
  props.defaultDirection
);

const selectedRows = ref([]);

let searchTimer = null;

/*
|--------------------------------------------------------------------------
| Safe columns
|--------------------------------------------------------------------------
*/

const tableColumns = computed(() => {
  return Array.isArray(props.columns)
    ? props.columns
    : [];
});

/*
|--------------------------------------------------------------------------
| Visible columns
|--------------------------------------------------------------------------
*/

const visibleColumns = computed(() => {
  return tableColumns.value.filter(
    (column) =>
      column &&
      column.hidden !== true
  );
});

/*
|--------------------------------------------------------------------------
| Get nested value
|--------------------------------------------------------------------------
|
| Example:
|
| getValue(row, "user.name")
|
|--------------------------------------------------------------------------
*/

function getValue(row, key) {
  if (!row || !key) {
    return null;
  }

  return String(key)
    .split(".")
    .reduce(
      (value, part) =>
        value?.[part],
      row
    );
}

/*
|--------------------------------------------------------------------------
| Normalize rows
|--------------------------------------------------------------------------
*/

const rows = computed(() => {
  /*
  |--------------------------------------------------------------------------
  | Server-side
  |--------------------------------------------------------------------------
  */

  if (props.mode === "server") {
    if (
      Array.isArray(
        props.data?.data
      )
    ) {
      return props.data.data;
    }

    return [];
  }

  /*
  |--------------------------------------------------------------------------
  | Client-side
  |--------------------------------------------------------------------------
  */

  if (Array.isArray(props.data)) {
    return props.data;
  }

  /*
  |--------------------------------------------------------------------------
  | Fallback
  |--------------------------------------------------------------------------
  */

  return [];
});

/*
|--------------------------------------------------------------------------
| Client-side filtering + sorting
|--------------------------------------------------------------------------
*/

const processedClientRows =
  computed(() => {
    /*
    |--------------------------------------------------------------------------
    | Server mode
    |--------------------------------------------------------------------------
    */

    if (props.mode !== "client") {
      return rows.value;
    }

    /*
    |--------------------------------------------------------------------------
    | Clone original rows
    |--------------------------------------------------------------------------
    */

    let result = [
      ...rows.value,
    ];

    /*
    |--------------------------------------------------------------------------
    | Search
    |--------------------------------------------------------------------------
    */

    const query =
      search.value
        .trim()
        .toLowerCase();

    if (query) {
      result = result.filter(
        (row) => {
          return tableColumns.value.some(
            (column) => {
              if (
                !column ||
                column.searchable ===
                  false
              ) {
                return false;
              }

              const value =
                getValue(
                  row,
                  column.key
                );

              return String(
                value ?? ""
              )
                .toLowerCase()
                .includes(query);
            }
          );
        }
      );
    }

    /*
    |--------------------------------------------------------------------------
    | Sorting
    |--------------------------------------------------------------------------
    */

    if (
      props.sortable &&
      sortBy.value
    ) {
      result.sort(
        (a, b) => {
          const aValue =
            getValue(
              a,
              sortBy.value
            );

          const bValue =
            getValue(
              b,
              sortBy.value
            );

          /*
          |--------------------------------------------------------------------------
          | Null values
          |--------------------------------------------------------------------------
          */

          if (
            aValue ===
              null ||
            aValue ===
              undefined
          ) {
            return 1;
          }

          if (
            bValue ===
              null ||
            bValue ===
              undefined
          ) {
            return -1;
          }

          /*
          |--------------------------------------------------------------------------
          | Same value
          |--------------------------------------------------------------------------
          */

          if (
            aValue ===
            bValue
          ) {
            return 0;
          }

          /*
          |--------------------------------------------------------------------------
          | Numeric comparison
          |--------------------------------------------------------------------------
          */

          if (
            typeof aValue ===
              "number" &&
            typeof bValue ===
              "number"
          ) {
            return sortDirection.value ===
              "asc"
              ? aValue -
                  bValue
              : bValue -
                  aValue;
          }

          /*
          |--------------------------------------------------------------------------
          | Date / string comparison
          |--------------------------------------------------------------------------
          */

          const comparison =
            String(
              aValue
            ).localeCompare(
              String(
                bValue
              ),
              undefined,
              {
                numeric: true,
                sensitivity:
                  "base",
              }
            );

          return sortDirection.value ===
            "asc"
            ? comparison
            : -comparison;
        }
      );
    }

    return result;
  });

/*
|--------------------------------------------------------------------------
| Total records
|--------------------------------------------------------------------------
*/

const total = computed(() => {
  /*
  |--------------------------------------------------------------------------
  | Server-side
  |--------------------------------------------------------------------------
  */

  if (props.mode === "server") {
    return Math.max(
      0,
      Number(
        props.data?.total ?? 0
      )
    );
  }

  /*
  |--------------------------------------------------------------------------
  | Client-side
  |--------------------------------------------------------------------------
  */

  return processedClientRows.value
    .length;
});

/*
|--------------------------------------------------------------------------
| Total pages
|--------------------------------------------------------------------------
*/

const totalPages = computed(() => {
  /*
  |--------------------------------------------------------------------------
  | Pagination disabled
  |--------------------------------------------------------------------------
  */

  if (!props.pagination) {
    return 1;
  }

  /*
  |--------------------------------------------------------------------------
  | Server-side
  |--------------------------------------------------------------------------
  */

  if (props.mode === "server") {
    return Math.max(
      1,
      Number(
        props.data?.last_page ?? 1
      )
    );
  }

  /*
  |--------------------------------------------------------------------------
  | Client-side
  |--------------------------------------------------------------------------
  */

  const size =
    Math.max(
      1,
      Number(
        currentPageSize.value
      )
    );

  return Math.max(
    1,
    Math.ceil(
      processedClientRows.value
        .length / size
    )
  );
});

/*
|--------------------------------------------------------------------------
| Display rows
|--------------------------------------------------------------------------
*/

const displayRows = computed(() => {
  /*
  |--------------------------------------------------------------------------
  | Server-side
  |--------------------------------------------------------------------------
  */

  if (props.mode === "server") {
    return rows.value;
  }

  /*
  |--------------------------------------------------------------------------
  | Pagination disabled
  |--------------------------------------------------------------------------
  */

  if (!props.pagination) {
    return processedClientRows.value;
  }

  /*
  |--------------------------------------------------------------------------
  | Client-side pagination
  |--------------------------------------------------------------------------
  */

  const size =
    Math.max(
      1,
      Number(
        currentPageSize.value
      )
    );

  const start =
    (currentPage.value - 1) *
    size;

  return processedClientRows.value.slice(
    start,
    start + size
  );
});

/*
|--------------------------------------------------------------------------
| Showing from
|--------------------------------------------------------------------------
*/

const showingFrom = computed(() => {
  if (!total.value) {
    return 0;
  }

  /*
  |--------------------------------------------------------------------------
  | Server-side
  |--------------------------------------------------------------------------
  */

  if (props.mode === "server") {
    return Number(
      props.data?.from ??
        (
          (
            currentPage.value -
              1
          ) *
            currentPageSize.value +
          1
        )
    );
  }

  /*
  |--------------------------------------------------------------------------
  | Client-side without pagination
  |--------------------------------------------------------------------------
  */

  if (!props.pagination) {
    return 1;
  }

  return (
    (
      currentPage.value -
      1
    ) *
      currentPageSize.value +
    1
  );
});

/*
|--------------------------------------------------------------------------
| Showing to
|--------------------------------------------------------------------------
*/

const showingTo = computed(() => {
  if (!total.value) {
    return 0;
  }

  /*
  |--------------------------------------------------------------------------
  | Server-side
  |--------------------------------------------------------------------------
  */

  if (props.mode === "server") {
    return Number(
      props.data?.to ??
        Math.min(
          currentPage.value *
            currentPageSize.value,
          total.value
        )
    );
  }

  /*
  |--------------------------------------------------------------------------
  | Client-side without pagination
  |--------------------------------------------------------------------------
  */

  if (!props.pagination) {
    return total.value;
  }

  return Math.min(
    currentPage.value *
      currentPageSize.value,
    total.value
  );
});

/*
|--------------------------------------------------------------------------
| Server-side request
|--------------------------------------------------------------------------
*/

function loadServerData() {
  if (props.mode !== "server") {
    return;
  }

  if (!props.route) {
    console.warn(
      "DataTable: `route` prop is required in server mode."
    );

    return;
  }

  /*
  |--------------------------------------------------------------------------
  | Build parameters
  |--------------------------------------------------------------------------
  */

  const params = {
    ...props.queryParams,

    search:
      search.value.trim() ||
      undefined,

    page: currentPage.value,

    per_page:
      currentPageSize.value,

    sort:
      sortBy.value ||
      undefined,

    direction:
      sortBy.value
        ? sortDirection.value
        : undefined,
  };

  /*
  |--------------------------------------------------------------------------
  | Remove undefined / null values
  |--------------------------------------------------------------------------
  */

  Object.keys(params).forEach(
    (key) => {
      if (
        params[key] ===
          undefined ||
        params[key] ===
          null ||
        params[key] ===
          ""
      ) {
        delete params[key];
      }
    }
  );

  /*
  |--------------------------------------------------------------------------
  | Emit query
  |--------------------------------------------------------------------------
  */

  emit(
    "query-change",
    params
  );

  /*
  |--------------------------------------------------------------------------
  | Inertia request
  |--------------------------------------------------------------------------
  */

  router.get(
    props.route,
    params,
    {
      preserveState:
        props.preserveState,

      preserveScroll:
        props.preserveScroll,

      replace: true,
    }
  );
}

/*
|--------------------------------------------------------------------------
| Search watcher
|--------------------------------------------------------------------------
*/

watch(
  search,
  (value) => {
    /*
    |--------------------------------------------------------------------------
    | Client-side
    |--------------------------------------------------------------------------
    */

    if (
      props.mode ===
      "client"
    ) {
      currentPage.value = 1;

      emit(
        "search",
        value
      );

      return;
    }

    /*
    |--------------------------------------------------------------------------
    | Server-side
    |--------------------------------------------------------------------------
    */

    clearTimeout(
      searchTimer
    );

    searchTimer =
      setTimeout(() => {
        currentPage.value = 1;

        loadServerData();
      }, props.searchDebounce);
  }
);

/*
|--------------------------------------------------------------------------
| Sort
|--------------------------------------------------------------------------
*/

function sort(column) {
  if (
    !props.sortable ||
    !column ||
    column.sortable ===
      false
  ) {
    return;
  }

  /*
  |--------------------------------------------------------------------------
  | Same column
  |--------------------------------------------------------------------------
  */

  if (
    sortBy.value ===
    column.key
  ) {
    sortDirection.value =
      sortDirection.value ===
      "asc"
        ? "desc"
        : "asc";
  } else {
    /*
    |--------------------------------------------------------------------------
    | New column
    |--------------------------------------------------------------------------
    */

    sortBy.value =
      column.key;

    sortDirection.value =
      "asc";
  }

  /*
  |--------------------------------------------------------------------------
  | Emit
  |--------------------------------------------------------------------------
  */

  emit("sort", {
    column:
      sortBy.value,

    direction:
      sortDirection.value,
  });

  /*
  |--------------------------------------------------------------------------
  | Server-side
  |--------------------------------------------------------------------------
  */

  if (
    props.mode ===
    "server"
  ) {
    currentPage.value = 1;

    loadServerData();
  }
}

/*
|--------------------------------------------------------------------------
| Change page
|--------------------------------------------------------------------------
*/

function changePage(page) {
  const nextPage =
    Number(page);

  if (
    !Number.isFinite(
      nextPage
    )
  ) {
    return;
  }

  if (
    nextPage < 1 ||
    nextPage >
      totalPages.value
  ) {
    return;
  }

  currentPage.value =
    nextPage;

  emit(
    "page-change",
    nextPage
  );

  /*
  |--------------------------------------------------------------------------
  | Server-side
  |--------------------------------------------------------------------------
  */

  if (
    props.mode ===
    "server"
  ) {
    loadServerData();
  }
}

/*
|--------------------------------------------------------------------------
| Change page size
|--------------------------------------------------------------------------
*/

function changePageSize(size) {
  const nextSize =
    Number(size);

  if (
    !Number.isFinite(
      nextSize
    ) ||
    nextSize <= 0
  ) {
    return;
  }

  currentPageSize.value =
    nextSize;

  currentPage.value = 1;

  emit(
    "page-size-change",
    nextSize
  );

  /*
  |--------------------------------------------------------------------------
  | Server-side
  |--------------------------------------------------------------------------
  */

  if (
    props.mode ===
    "server"
  ) {
    loadServerData();
  }
}

/*
|--------------------------------------------------------------------------
| Pagination pages
|--------------------------------------------------------------------------
*/

const paginationPages =
  computed(() => {
    const last =
      totalPages.value;

    const current =
      currentPage.value;

    /*
    |--------------------------------------------------------------------------
    | Few pages
    |--------------------------------------------------------------------------
    */

    if (last <= 7) {
      return Array.from(
        {
          length: last,
        },
        (_, index) =>
          index + 1
      );
    }

    const pages = [];

    /*
    |--------------------------------------------------------------------------
    | First page
    |--------------------------------------------------------------------------
    */

    pages.push(1);

    /*
    |--------------------------------------------------------------------------
    | Left ellipsis
    |--------------------------------------------------------------------------
    */

    if (current > 4) {
      pages.push("...");
    }

    /*
    |--------------------------------------------------------------------------
    | Middle pages
    |--------------------------------------------------------------------------
    */

    const start =
      Math.max(
        2,
        current - 1
      );

    const end =
      Math.min(
        last - 1,
        current + 1
      );

    for (
      let page = start;
      page <= end;
      page++
    ) {
      pages.push(page);
    }

    /*
    |--------------------------------------------------------------------------
    | Right ellipsis
    |--------------------------------------------------------------------------
    */

    if (
      current <
      last - 3
    ) {
      pages.push("...");
    }

    /*
    |--------------------------------------------------------------------------
    | Last page
    |--------------------------------------------------------------------------
    */

    pages.push(last);

    return pages;
  });

/*
|--------------------------------------------------------------------------
| Selection
|--------------------------------------------------------------------------
*/

const allSelected =
  computed(() => {
    if (
      !displayRows.value
        .length
    ) {
      return false;
    }

    return displayRows.value.every(
      (row) =>
        selectedRows.value.includes(
          row?.[
            props.rowKey
          ]
        )
    );
  });

/*
|--------------------------------------------------------------------------
| Select all
|--------------------------------------------------------------------------
*/

function toggleSelectAll() {
  const ids =
    displayRows.value
      .map(
        (row) =>
          row?.[
            props.rowKey
          ]
      )
      .filter(
        (id) =>
          id !==
            null &&
          id !==
            undefined
      );

  if (
    allSelected.value
  ) {
    selectedRows.value =
      selectedRows.value.filter(
        (id) =>
          !ids.includes(id)
      );
  } else {
    selectedRows.value = [
      ...new Set([
        ...selectedRows.value,
        ...ids,
      ]),
    ];
  }

  emit(
    "selection-change",
    selectedRows.value
  );
}

/*
|--------------------------------------------------------------------------
| Toggle row
|--------------------------------------------------------------------------
*/

function toggleRow(row) {
  if (!row) {
    return;
  }

  const id =
    row[
      props.rowKey
    ];

  if (
    id ===
      null ||
    id ===
      undefined
  ) {
    return;
  }

  if (
    selectedRows.value.includes(
      id
    )
  ) {
    selectedRows.value =
      selectedRows.value.filter(
        (selectedId) =>
          selectedId !== id
      );
  } else {
    selectedRows.value.push(
      id
    );
  }

  emit(
    "selection-change",
    selectedRows.value
  );
}

/*
|--------------------------------------------------------------------------
| Is selected
|--------------------------------------------------------------------------
*/

function isSelected(row) {
  if (!row) {
    return false;
  }

  return selectedRows.value.includes(
    row[
      props.rowKey
    ]
  );
}

/*
|--------------------------------------------------------------------------
| Clear search
|--------------------------------------------------------------------------
*/

function clearSearch() {
  search.value = "";
}

/*
|--------------------------------------------------------------------------
| Clear selection
|--------------------------------------------------------------------------
*/

function clearSelection() {
  selectedRows.value =
    [];

  emit(
    "selection-change",
    []
  );
}

/*
|--------------------------------------------------------------------------
| Sync Laravel current page
|--------------------------------------------------------------------------
*/

watch(
  () =>
    props.data?.current_page,

  (page) => {
    if (
      props.mode ===
        "server" &&
      page !==
        undefined &&
      page !== null
    ) {
      currentPage.value =
        Number(page);
    }
  },

  {
    immediate: true,
  }
);

/*
|--------------------------------------------------------------------------
| Sync Laravel page size
|--------------------------------------------------------------------------
*/

watch(
  () =>
    props.data?.per_page,

  (perPage) => {
    if (
      props.mode ===
        "server" &&
      perPage !==
        undefined &&
      perPage !== null
    ) {
      currentPageSize.value =
        Number(
          perPage
        );
    }
  },

  {
    immediate: true,
  }
);

/*
|--------------------------------------------------------------------------
| Sync server-side sort/search from query params
|--------------------------------------------------------------------------
|
| Useful when using Inertia browser navigation/back button.
|--------------------------------------------------------------------------
*/

watch(
  () =>
    props.queryParams,

  () => {
    if (
      props.mode !==
      "server"
    ) {
      return;
    }

    /*
     * Parent-controlled query params
     * are intentionally not copied into
     * local search/sort state here.
     */
  },

  {
    deep: true,
  }
);

/*
|--------------------------------------------------------------------------
| Cleanup
|--------------------------------------------------------------------------
*/

onBeforeUnmount(() => {
  clearTimeout(
    searchTimer
  );
});

/*
|--------------------------------------------------------------------------
| Expose
|--------------------------------------------------------------------------
*/

defineExpose({
  clearSearch,
  clearSelection,
  loadServerData,
});
</script>

<template>
  <div
    class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
  >
    <!-- ============================================================= -->
    <!-- TOOLBAR -->
    <!-- ============================================================= -->

    <div
      class="flex flex-col gap-3 border-b border-slate-200 p-4 sm:flex-row sm:items-center sm:justify-between"
    >
      <!-- Left toolbar -->
      <div
        class="flex flex-wrap items-center gap-2"
      >
        <!-- Search -->
        <div
          v-if="searchable"
          class="relative"
        >
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
            type="search"
            :placeholder="
              searchPlaceholder
            "
            class="w-64 rounded-xl border border-slate-200 bg-white py-2 pl-9 pr-3 text-sm text-slate-700 outline-none transition placeholder:text-slate-400 focus:border-primary focus:ring-2 focus:ring-primary/10"
          />
        </div>

        <!-- Selected count -->
        <span
          v-if="
            selectable &&
            selectedRows.length
          "
          class="rounded-xl bg-primary/10 px-3 py-2 text-xs font-semibold text-primary"
        >
          {{
            selectedRows.length
          }}
          selected
        </span>

        <!-- Custom toolbar -->
        <slot
          name="toolbar"
          :selected="
            selectedRows
          "
        />
      </div>

      <!-- Right toolbar -->
      <div
        class="flex items-center gap-2"
      >
        <slot
          name="toolbar-right"
          :selected="
            selectedRows
          "
        />
      </div>
    </div>

    <!-- ============================================================= -->
    <!-- TABLE -->
    <!-- ============================================================= -->

    <div
      class="overflow-x-auto"
    >
      <table
        class="w-full min-w-full text-left text-sm"
      >
        <!-- ========================================================= -->
        <!-- HEADER -->
        <!-- ========================================================= -->

        <thead
          class="bg-slate-50"
        >
          <tr>
            <!-- Select all -->
            <th
              v-if="selectable"
              class="w-12 px-4 py-3"
            >
              <input
                type="checkbox"
                :checked="
                  allSelected
                "
                :disabled="
                  !displayRows.length
                "
                class="rounded border-slate-300 text-primary focus:ring-primary"
                @change="
                  toggleSelectAll
                "
              />
            </th>

            <!-- Columns -->
            <th
              v-for="column in visibleColumns"
              :key="
                column.key
              "
              class="whitespace-nowrap px-4 py-3 text-xs font-semibold uppercase tracking-wide text-slate-500"
              :class="{
                'cursor-pointer select-none hover:text-slate-700':
                  sortable &&
                  column.sortable !==
                    false,
              }"
              @click="
                sort(column)
              "
            >
              <div
                class="flex items-center gap-1.5"
              >
                <span>
                  {{
                    column.label
                  }}
                </span>

                <!-- Sort indicator -->
                <span
                  v-if="
                    sortBy ===
                    column.key
                  "
                  class="font-bold text-primary"
                >
                  {{
                    sortDirection ===
                    "asc"
                      ? "↑"
                      : "↓"
                  }}
                </span>
              </div>
            </th>

            <!-- Actions -->
            <th
              v-if="
                $slots.actions
              "
              class="w-24 whitespace-nowrap px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide text-slate-500"
            >
              Actions
            </th>
          </tr>
        </thead>

        <!-- ========================================================= -->
        <!-- LOADING -->
        <!-- ========================================================= -->

        <tbody v-if="loading">
          <tr>
            <td
              :colspan="
                visibleColumns.length +
                (selectable
                  ? 1
                  : 0) +
                ($slots.actions
                  ? 1
                  : 0)
              "
              class="px-4 py-16 text-center"
            >
              <div
                class="flex items-center justify-center gap-2 text-sm text-slate-500"
              >
                <svg
                  class="h-5 w-5 animate-spin"
                  fill="none"
                  viewBox="0 0 24 24"
                >
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

        <!-- ========================================================= -->
        <!-- DATA -->
        <!-- ========================================================= -->

        <tbody
          v-else-if="
            displayRows.length
          "
          class="divide-y divide-slate-100"
        >
          <tr
            v-for="row in displayRows"
            :key="
              row?.[
                rowKey
              ]
            "
            class="transition hover:bg-slate-50"
            @click="
              emit(
                'row-click',
                row
              )
            "
          >
            <!-- Checkbox -->
            <td
              v-if="selectable"
              class="px-4 py-3"
              @click.stop
            >
              <input
                type="checkbox"
                :checked="
                  isSelected(
                    row
                  )
                "
                class="rounded border-slate-300 text-primary focus:ring-primary"
                @change="
                  toggleRow(
                    row
                  )
                "
              />
            </td>

            <!-- Cells -->
            <td
              v-for="column in visibleColumns"
              :key="
                column.key
              "
              class="px-4 py-3 text-slate-700"
            >
              <slot
                :name="`cell-${column.key}`"
                :row="row"
                :value="
                  getValue(
                    row,
                    column.key
                  )
                "
              >
                {{
                  getValue(
                    row,
                    column.key
                  ) ??
                  "—"
                }}
              </slot>
            </td>

            <!-- Actions -->
            <td
              v-if="
                $slots.actions
              "
              class="px-4 py-3 text-right"
              @click.stop
            >
              <slot
                name="actions"
                :row="row"
              />
            </td>
          </tr>
        </tbody>

        <!-- ========================================================= -->
        <!-- EMPTY -->
        <!-- ========================================================= -->

        <tbody v-else>
          <tr>
            <td
              :colspan="
                visibleColumns.length +
                (selectable
                  ? 1
                  : 0) +
                ($slots.actions
                  ? 1
                  : 0)
              "
              class="px-4 py-16 text-center"
            >
              <div
                class="flex flex-col items-center justify-center"
              >
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
                      d="M20 13V7a2 2 0 00-2-2h-3l-2-2H9L7 5H4a2 2 0 00-2 2v6m18 0v4a2 2 0 01-2 2H4a2 2 0 01-2-2v-4m18 0H2"
                    />
                  </svg>
                </div>

                <p
                  class="text-sm font-medium text-slate-600"
                >
                  {{
                    emptyText
                  }}
                </p>

                <button
                  v-if="
                    search
                  "
                  type="button"
                  class="mt-2 text-xs font-semibold text-primary hover:underline"
                  @click="
                    clearSearch
                  "
                >
                  Clear search
                </button>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>

    <!-- ============================================================= -->
    <!-- FOOTER -->
    <!-- ============================================================= -->

    <div
      v-if="pagination"
      class="flex flex-col gap-3 border-t border-slate-200 px-4 py-3 sm:flex-row sm:items-center sm:justify-between"
    >
      <!-- Information -->
      <div
        class="flex flex-wrap items-center gap-2 text-xs text-slate-500"
      >
        <span>
          Rows per page
        </span>

        <select
          :value="
            currentPageSize
          "
          class="rounded-lg border border-slate-200 bg-white px-2 py-1.5 text-xs outline-none focus:border-primary focus:ring-1 focus:ring-primary"
          @change="
            changePageSize(
              $event.target.value
            )
          "
        >
          <option
            v-for="size in pageSizeOptions"
            :key="size"
            :value="size"
          >
            {{ size }}
          </option>
        </select>

        <span>
          Showing
          {{
            showingFrom
          }}–{{
            showingTo
          }}
          of
          {{
            total
          }}
        </span>
      </div>

      <!-- Pagination -->
      <div
        v-if="
          totalPages > 1
        "
        class="flex flex-wrap items-center gap-1"
      >
        <!-- Previous -->
        <button
          type="button"
          class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-600 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-40"
          :disabled="
            currentPage ===
            1
          "
          @click="
            changePage(
              currentPage -
                1
            )
          "
        >
          Previous
        </button>

        <!-- Pages -->
        <template
          v-for="(
            page, index
          ) in paginationPages"
          :key="
            `${page}-${index}`
          "
        >
          <!-- Ellipsis -->
          <span
            v-if="
              page ===
              '...'
            "
            class="px-2 text-xs text-slate-400"
          >
            ...
          </span>

          <!-- Page -->
          <button
            v-else
            type="button"
            class="h-8 min-w-8 rounded-lg px-2 text-xs font-medium transition"
            :class="
              currentPage ===
              page
                ? 'bg-primary text-white'
                : 'text-slate-600 hover:bg-slate-100'
            "
            @click="
              changePage(
                page
              )
            "
          >
            {{ page }}
          </button>
        </template>

        <!-- Next -->
        <button
          type="button"
          class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-600 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-40"
          :disabled="
            currentPage ===
            totalPages
          "
          @click="
            changePage(
              currentPage +
                1
            )
          "
        >
          Next
        </button>
      </div>
    </div>
  </div>
</template>