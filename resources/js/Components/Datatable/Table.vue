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
    /*
    |--------------------------------------------------------------------------
    | client | server
    |--------------------------------------------------------------------------
    */
    mode: {
        type: String,
        default: "client",
        validator: (value) =>
            ["client", "server"].includes(value),
    },

    /*
    |--------------------------------------------------------------------------
    | Data
    |--------------------------------------------------------------------------
    |
    | Client:
    | [
    |   { id: 1, name: "John" }
    | ]
    |
    | Server:
    | Laravel paginator
    |
    */
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
    | Loading
    |--------------------------------------------------------------------------
    */
    loading: {
        type: Boolean,
        default: false,
    },

    /*
    |--------------------------------------------------------------------------
    | Search
    |--------------------------------------------------------------------------
    */
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

    /*
    |--------------------------------------------------------------------------
    | Sorting
    |--------------------------------------------------------------------------
    */
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
        validator: (value) =>
            ["asc", "desc"].includes(value),
    },

    /*
    |--------------------------------------------------------------------------
    | Filters
    |--------------------------------------------------------------------------
    */
    filterable: {
        type: Boolean,
        default: true,
    },

    filters: {
        type: Object,
        default: () => ({}),
    },

    /*
    |--------------------------------------------------------------------------
    | Pagination
    |--------------------------------------------------------------------------
    */
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
        default: () => [
            10,
            25,
            50,
            100,
        ],
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
    | Empty
    |--------------------------------------------------------------------------
    */
    emptyText: {
        type: String,
        default: "No records found.",
    },

    /*
    |--------------------------------------------------------------------------
    | Server route
    |--------------------------------------------------------------------------
    */
    route: {
        type: String,
        default: null,
    },

    /*
    |--------------------------------------------------------------------------
    | Additional query parameters
    |--------------------------------------------------------------------------
    */
    queryParams: {
        type: Object,
        default: () => ({}),
    },

    /*
    |--------------------------------------------------------------------------
    | Inertia
    |--------------------------------------------------------------------------
    */
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
    "filter-change",
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
        ? Number(
              props.data?.current_page ?? 1
          )
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

const activeFilters = ref({
    ...props.filters,
});

let searchTimer = null;

/*
|--------------------------------------------------------------------------
| Safe columns
|--------------------------------------------------------------------------
*/

const tableColumns = computed(() => {
    return Array.isArray(props.columns)
        ? props.columns.filter(
              (column) =>
                  column &&
                  typeof column ===
                      "object" &&
                  column.key
          )
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
| Rows
|--------------------------------------------------------------------------
*/

const rows = computed(() => {
    /*
    |--------------------------------------------------------------------------
    | Server
    |--------------------------------------------------------------------------
    */

    if (props.mode === "server") {
        return Array.isArray(
            props.data?.data
        )
            ? props.data.data
            : [];
    }

    /*
    |--------------------------------------------------------------------------
    | Client
    |--------------------------------------------------------------------------
    */

    return Array.isArray(props.data)
        ? props.data
        : [];
});

/*
|--------------------------------------------------------------------------
| Client filtering + sorting
|--------------------------------------------------------------------------
*/

const processedClientRows =
    computed(() => {
        if (
            props.mode !==
            "client"
        ) {
            return rows.value;
        }

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
            result =
                result.filter(
                    (row) =>
                        tableColumns.value.some(
                            (column) => {
                                if (
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
                                    value ??
                                        ""
                                )
                                    .toLowerCase()
                                    .includes(
                                        query
                                    );
                            }
                        )
                );
        }

        /*
        |--------------------------------------------------------------------------
        | Filters
        |--------------------------------------------------------------------------
        */

        Object.entries(
            activeFilters.value
        ).forEach(
            ([
                key,
                filterValue,
            ]) => {
                if (
                    filterValue ===
                        "" ||
                    filterValue ===
                        null ||
                    filterValue ===
                        undefined
                ) {
                    return;
                }

                result =
                    result.filter(
                        (row) => {
                            const value =
                                getValue(
                                    row,
                                    key
                                );

                            /*
                            |--------------------------------------------------------------------------
                            | Multiple values
                            |--------------------------------------------------------------------------
                            */

                            if (
                                Array.isArray(
                                    filterValue
                                )
                            ) {
                                return filterValue
                                    .map(
                                        String
                                    )
                                    .includes(
                                        String(
                                            value
                                        )
                                    );
                            }

                            /*
                            |--------------------------------------------------------------------------
                            | Normal value
                            |--------------------------------------------------------------------------
                            */

                            return (
                                String(
                                    value ??
                                        ""
                                ) ===
                                String(
                                    filterValue
                                )
                            );
                        }
                    );
            }
        );

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

                    if (
                        aValue ===
                        bValue
                    ) {
                        return 0;
                    }

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
| Total
|--------------------------------------------------------------------------
*/

const total = computed(() => {
    if (
        props.mode ===
        "server"
    ) {
        return Math.max(
            0,
            Number(
                props.data?.total ??
                    0
            )
        );
    }

    return processedClientRows.value
        .length;
});

/*
|--------------------------------------------------------------------------
| Total pages
|--------------------------------------------------------------------------
*/

const totalPages = computed(() => {
    if (
        !props.pagination
    ) {
        return 1;
    }

    if (
        props.mode ===
        "server"
    ) {
        return Math.max(
            1,
            Number(
                props.data?.last_page ??
                    1
            )
        );
    }

    return Math.max(
        1,
        Math.ceil(
            processedClientRows.value
                .length /
                currentPageSize.value
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
    | Server
    |--------------------------------------------------------------------------
    */

    if (
        props.mode ===
        "server"
    ) {
        return rows.value;
    }

    /*
    |--------------------------------------------------------------------------
    | No pagination
    |--------------------------------------------------------------------------
    */

    if (
        !props.pagination
    ) {
        return processedClientRows.value;
    }

    /*
    |--------------------------------------------------------------------------
    | Client pagination
    |--------------------------------------------------------------------------
    */

    const start =
        (
            currentPage.value -
                1
        ) *
        currentPageSize.value;

    return processedClientRows.value.slice(
        start,
        start +
            currentPageSize.value
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

    if (
        props.mode ===
        "server"
    ) {
        return Number(
            props.data?.from ??
                1
        );
    }

    if (
        !props.pagination
    ) {
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

    if (
        props.mode ===
        "server"
    ) {
        return Number(
            props.data?.to ??
                total.value
        );
    }

    if (
        !props.pagination
    ) {
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
| Build server parameters
|--------------------------------------------------------------------------
*/

function buildServerParams() {
    const params = {
        ...props.queryParams,

        search:
            search.value.trim() ||
            undefined,

        page:
            currentPage.value,

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
    | Add filters
    |--------------------------------------------------------------------------
    */

    Object.entries(
        activeFilters.value
    ).forEach(
        ([
            key,
            value,
        ]) => {
            if (
                value ===
                    null ||
                value ===
                    undefined ||
                value ===
                    ""
            ) {
                return;
            }

            /*
            |--------------------------------------------------------------------------
            | Array filters
            |--------------------------------------------------------------------------
            */

            if (
                Array.isArray(
                    value
                )
            ) {
                value.forEach(
                    (
                        item,
                        index
                    ) => {
                        params[
                            `filters[${key}][${index}]`
                        ] =
                            item;
                    }
                );

                return;
            }

            params[
                `filters[${key}]`
            ] = value;
        }
    );

    /*
    |--------------------------------------------------------------------------
    | Remove undefined/null/empty
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

    return params;
}

/*
|--------------------------------------------------------------------------
| Server request
|--------------------------------------------------------------------------
*/

function loadServerData() {
    if (
        props.mode !==
        "server"
    ) {
        return;
    }

    if (!props.route) {
        console.warn(
            "DataTable: `route` prop is required in server mode."
        );

        return;
    }

    const params =
        buildServerParams();

    emit(
        "query-change",
        params
    );

    router.get(
        props.route,
        params,
        {
            preserveState:
                props.preserveState,

            preserveScroll:
                props.preserveScroll,

            replace: true,

            /*
            |--------------------------------------------------------------------------
            | Do NOT show Inertia progress
            |--------------------------------------------------------------------------
            |
            | This prevents the top progress bar from appearing
            | every time the DataTable searches/sorts/pages.
            |
            */
            showProgress: false,
        }
    );
}

/*
|--------------------------------------------------------------------------
| Search
|--------------------------------------------------------------------------
*/

watch(
    search,
    (value) => {
        if (
            props.mode ===
            "client"
        ) {
            currentPage.value =
                1;

            emit(
                "search",
                value
            );

            return;
        }

        clearTimeout(
            searchTimer
        );

        searchTimer =
            setTimeout(() => {
                currentPage.value =
                    1;

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
        sortBy.value =
            column.key;

        sortDirection.value =
            "asc";
    }

    emit("sort", {
        column:
            sortBy.value,

        direction:
            sortDirection.value,
    });

    if (
        props.mode ===
        "server"
    ) {
        currentPage.value =
            1;

        loadServerData();
    }
}

/*
|--------------------------------------------------------------------------
| Filter
|--------------------------------------------------------------------------
*/

function updateFilter(
    key,
    value
) {
    if (
        value === "" ||
        value === null ||
        value === undefined
    ) {
        delete activeFilters.value[
            key
        ];
    } else {
        activeFilters.value[
            key
        ] = value;
    }

    currentPage.value =
        1;

    emit(
        "filter-change",
        {
            ...activeFilters.value,
        }
    );

    if (
        props.mode ===
        "server"
    ) {
        loadServerData();
    }
}

/*
|--------------------------------------------------------------------------
| Clear filters
|--------------------------------------------------------------------------
*/

function clearFilters() {
    activeFilters.value =
        {};

    currentPage.value =
        1;

    emit(
        "filter-change",
        {}
    );

    if (
        props.mode ===
        "server"
    ) {
        loadServerData();
    }
}

/*
|--------------------------------------------------------------------------
| Has filters
|--------------------------------------------------------------------------
*/

const hasActiveFilters =
    computed(() => {
        return Object.values(
            activeFilters.value
        ).some(
            (value) =>
                value !==
                    "" &&
                value !==
                    null &&
                value !==
                    undefined
        );
    });

/*
|--------------------------------------------------------------------------
| Change page
|--------------------------------------------------------------------------
*/

function changePage(
    page
) {
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

function changePageSize(
    size
) {
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

    currentPage.value =
        1;

    emit(
        "page-size-change",
        nextSize
    );

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

        pages.push(1);

        if (
            current > 4
        ) {
            pages.push(
                "..."
            );
        }

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

        if (
            current <
            last - 3
        ) {
            pages.push(
                "..."
            );
        }

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
                    !ids.includes(
                        id
                    )
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

function toggleRow(
    row
) {
    if (!row) {
        return;
    }

    const id =
        row[
            props.rowKey
        ];

    if (
        id === null ||
        id === undefined
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
                (
                    selectedId
                ) =>
                    selectedId !==
                    id
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

function isSelected(
    row
) {
    return !!row &&
        selectedRows.value.includes(
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

    if (
        props.mode ===
        "server"
    ) {
        currentPage.value =
            1;

        loadServerData();
    }
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
| Sync Laravel paginator
|--------------------------------------------------------------------------
*/

watch(
    () =>
        props.data
            ?.current_page,
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
| Sync external filters
|--------------------------------------------------------------------------
*/

watch(
    () => props.filters,
    (value) => {
        activeFilters.value =
            {
                ...value,
            };
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
    clearFilters,
    clearSelection,
    loadServerData,
});
</script>

<template>
    <div
        class="overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm"
    >
        <!-- ========================================================= -->
        <!-- TOOLBAR -->
        <!-- ========================================================= -->

        <div
            class="flex flex-col gap-3 border-b border-slate-200 p-4 sm:flex-row sm:items-center sm:justify-between"
        >
            <!-- Left -->
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

                <!-- Clear filters -->
                <button
                    v-if="
                        filterable &&
                        hasActiveFilters
                    "
                    type="button"
                    class="rounded-xl border border-slate-200 px-3 py-2 text-xs font-medium text-slate-600 transition hover:bg-slate-50"
                    @click="
                        clearFilters
                    "
                >
                    Clear filters
                </button>

                <!-- Selected -->
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

                <!-- Toolbar -->
                <slot
                    name="toolbar"
                    :selected="
                        selectedRows
                    "
                />
            </div>

            <!-- Right -->
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

        <!-- ========================================================= -->
        <!-- TABLE -->
        <!-- ========================================================= -->

        <div
            class="overflow-x-auto"
        >
            <table
                class="w-full min-w-full text-left text-sm"
            >
                <!-- HEADER -->
                <thead
                    class="bg-slate-50"
                >
                    <tr>
                        <!-- Select -->
                        <th
                            v-if="
                                selectable
                            "
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
                        >
                            <div
                                class="flex items-center gap-2"
                            >
                                <!-- Label / Sort -->
                                <button
                                    v-if="
                                        sortable &&
                                        column.sortable !==
                                            false
                                    "
                                    type="button"
                                    class="flex items-center gap-1.5 hover:text-slate-700"
                                    @click="
                                        sort(
                                            column
                                        )
                                    "
                                >
                                    <span>
                                        {{
                                            column.label
                                        }}
                                    </span>

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
                                </button>

                                <span
                                    v-else
                                >
                                    {{
                                        column.label
                                    }}
                                </span>

                                <!-- SELECT FILTER -->
                                <select
                                    v-if="
                                        filterable &&
                                        column.filterable &&
                                        column.filterType ===
                                            'select'
                                    "
                                    :value="
                                        activeFilters[
                                            column.key
                                        ] ??
                                        ''
                                    "
                                    class="rounded-lg border border-slate-200 bg-white px-2 py-1 text-xs font-normal normal-case tracking-normal text-slate-600 outline-none focus:border-primary focus:ring-1 focus:ring-primary/20"
                                    @click.stop
                                    @change="
                                        updateFilter(
                                            column.key,
                                            $event
                                                .target
                                                .value
                                        )
                                    "
                                >
                                    <option
                                        value=""
                                    >
                                        All
                                    </option>

                                    <option
                                        v-for="option in column.filterOptions ||
                                        []"
                                        :key="
                                            option.value
                                        "
                                        :value="
                                            option.value
                                        "
                                    >
                                        {{
                                            option.label
                                        }}
                                    </option>
                                </select>
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

                <!-- ================================================= -->
                <!-- LOADING -->
                <!-- ================================================= -->

                <tbody
                    v-if="loading"
                >
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

                <!-- ================================================= -->
                <!-- DATA -->
                <!-- ================================================= -->

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
                            v-if="
                                selectable
                            "
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
                                :row="
                                    row
                                "
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
                                :row="
                                    row
                                "
                            />
                        </td>
                    </tr>
                </tbody>

                <!-- ================================================= -->
                <!-- EMPTY -->
                <!-- ================================================= -->

                <tbody
                    v-else
                >
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

        <!-- ========================================================= -->
        <!-- FOOTER -->
        <!-- ========================================================= -->

        <div
            v-if="
                pagination
            "
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
                            $event
                                .target
                                .value
                        )
                    "
                >
                    <option
                        v-for="size in pageSizeOptions"
                        :key="
                            size
                        "
                        :value="
                            size
                        "
                    >
                        {{
                            size
                        }}
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
                    totalPages >
                    1
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
                        page,
                        index
                    ) in paginationPages"
                    :key="
                        `${page}-${index}`
                    "
                >
                    <span
                        v-if="
                            page ===
                            '...'
                        "
                        class="px-2 text-xs text-slate-400"
                    >
                        ...
                    </span>

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
                        {{
                            page
                        }}
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