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
    | Mode
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
    |     { id: 1, name: "John" }
    | ]
    |
    | Server:
    | Laravel paginator:
    |
    | {
    |     data: [],
    |     current_page: 1,
    |     last_page: 10,
    |     per_page: 10,
    |     total: 100,
    |     from: 1,
    |     to: 10
    | }
    |
    |--------------------------------------------------------------------------
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
    |
    | Used for client-side/external loading.
    | Server mode uses internal serverLoading.
    |
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
    | Server-side
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

const currentPage = ref(1);

const currentPageSize = ref(
    Math.max(
        1,
        Number(props.pageSize)
    )
);

const sortBy = ref(
    props.defaultSort
);

const sortDirection = ref(
    props.defaultDirection
);

const selectedRows = ref([]);

/*
|--------------------------------------------------------------------------
| Server loading
|--------------------------------------------------------------------------
|
| IMPORTANT:
|
| Server-side loading is controlled by this component.
| We don't depend on the parent changing :loading.
|
|--------------------------------------------------------------------------
*/

const serverLoading = ref(false);

/*
|--------------------------------------------------------------------------
| Search timer
|--------------------------------------------------------------------------
*/

let searchTimer = null;

/*
|--------------------------------------------------------------------------
| Current Inertia request
|--------------------------------------------------------------------------
*/

let requestVersion = 0;

/*
|--------------------------------------------------------------------------
| Loading
|--------------------------------------------------------------------------
*/

const isLoading = computed(() => {
    if (props.mode === "server") {
        return serverLoading.value;
    }

    return props.loading;
});

/*
|--------------------------------------------------------------------------
| Safe columns
|--------------------------------------------------------------------------
*/

const tableColumns = computed(() => {
    if (!Array.isArray(props.columns)) {
        return [];
    }

    return props.columns.filter(
        (column) =>
            column &&
            typeof column === "object" &&
            column.key
    );
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
    if (
        !row ||
        !key
    ) {
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
    | Server
    |--------------------------------------------------------------------------
    */

    if (
        props.mode ===
        "server"
    ) {
        const serverRows =
            props.data?.data;

        return Array.isArray(
            serverRows
        )
            ? serverRows
            : [];
    }

    /*
    |--------------------------------------------------------------------------
    | Client
    |--------------------------------------------------------------------------
    */

    return Array.isArray(
        props.data
    )
        ? props.data
        : [];
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

        if (
            props.mode !==
            "client"
        ) {
            return rows.value;
        }

        /*
        |--------------------------------------------------------------------------
        | Clone
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
            result =
                result.filter(
                    (row) => {
                        return tableColumns.value.some(
                            (
                                column
                            ) => {
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
                    | Nulls
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
                    | Same
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
                    | Numbers
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
                    | Strings
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
                                numeric:
                                    true,

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
    /*
    |--------------------------------------------------------------------------
    | Server
    |--------------------------------------------------------------------------
    */

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

    /*
    |--------------------------------------------------------------------------
    | Client
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

    if (
        !props.pagination
    ) {
        return 1;
    }

    /*
    |--------------------------------------------------------------------------
    | Server
    |--------------------------------------------------------------------------
    */

    if (
        props.mode ===
        "server"
    ) {
        return Math.max(
            1,
            Number(
                props.data
                    ?.last_page ??
                    1
            )
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Client
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
                .length /
                size
        )
    );
});

/*
|--------------------------------------------------------------------------
| Display rows
|--------------------------------------------------------------------------
*/

const displayRows =
    computed(() => {
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

        const size =
            Math.max(
                1,
                Number(
                    currentPageSize.value
                )
            );

        const start =
            (
                currentPage.value -
                1
            ) * size;

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

const showingFrom =
    computed(() => {
        if (
            !total.value
        ) {
            return 0;
        }

        /*
        |--------------------------------------------------------------------------
        | Server
        |--------------------------------------------------------------------------
        */

        if (
            props.mode ===
            "server"
        ) {
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
        | Client no pagination
        |--------------------------------------------------------------------------
        */

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

const showingTo =
    computed(() => {
        if (
            !total.value
        ) {
            return 0;
        }

        /*
        |--------------------------------------------------------------------------
        | Server
        |--------------------------------------------------------------------------
        */

        if (
            props.mode ===
            "server"
        ) {
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
        | Client no pagination
        |--------------------------------------------------------------------------
        */

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
    | Remove empty values
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

    if (
        !props.route
    ) {
        console.warn(
            "DataTable: `route` prop is required in server mode."
        );

        return;
    }

    /*
    |--------------------------------------------------------------------------
    | Increment request version
    |--------------------------------------------------------------------------
    */

    const currentRequest =
        ++requestVersion;

    /*
    |--------------------------------------------------------------------------
    | Build params
    |--------------------------------------------------------------------------
    */

    const params =
        buildServerParams();

    /*
    |--------------------------------------------------------------------------
    | Emit
    |--------------------------------------------------------------------------
    */

    emit(
        "query-change",
        params
    );

    /*
    |--------------------------------------------------------------------------
    | Loading
    |--------------------------------------------------------------------------
    */

    serverLoading.value =
        true;

    /*
    |--------------------------------------------------------------------------
    | Inertia
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

            /*
            |--------------------------------------------------------------------------
            | Start
            |--------------------------------------------------------------------------
            */

            onStart() {
                if (
                    currentRequest ===
                    requestVersion
                ) {
                    serverLoading.value =
                        true;
                }
            },

            /*
            |--------------------------------------------------------------------------
            | Finish
            |--------------------------------------------------------------------------
            */

            onFinish() {
                /*
                |--------------------------------------------------------------------------
                | Only the latest request controls loading
                |--------------------------------------------------------------------------
                */

                if (
                    currentRequest ===
                    requestVersion
                ) {
                    serverLoading.value =
                        false;
                }
            },

            /*
            |--------------------------------------------------------------------------
            | Cancel
            |--------------------------------------------------------------------------
            */

            onCancel() {
                if (
                    currentRequest ===
                    requestVersion
                ) {
                    serverLoading.value =
                        false;
                }
            },

            /*
            |--------------------------------------------------------------------------
            | Error
            |--------------------------------------------------------------------------
            */

            onError() {
                if (
                    currentRequest ===
                    requestVersion
                ) {
                    serverLoading.value =
                        false;
                }
            },
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
        /*
        |--------------------------------------------------------------------------
        | Client
        |--------------------------------------------------------------------------
        */

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

        /*
        |--------------------------------------------------------------------------
        | Server
        |--------------------------------------------------------------------------
        */

        clearTimeout(
            searchTimer
        );

        searchTimer =
            setTimeout(
                () => {
                    currentPage.value =
                        1;

                    loadServerData();
                },
                Math.max(
                    0,
                    props.searchDebounce
                )
            );
    }
);

/*
|--------------------------------------------------------------------------
| Sorting
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
    | Existing column
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
    | Reset page
    |--------------------------------------------------------------------------
    */

    currentPage.value =
        1;

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
    | Server
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
| Change page
|--------------------------------------------------------------------------
*/

function changePage(page) {
    const nextPage =
        Number(page);

    if (
        !Number.isInteger(
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

    if (
        nextPage ===
        currentPage.value
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
    | Server
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
        !Number.isInteger(
            nextSize
        ) ||
        nextSize <= 0
    ) {
        return;
    }

    if (
        nextSize ===
        currentPageSize.value
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

    /*
    |--------------------------------------------------------------------------
    | Server
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
        | <= 7 pages
        |--------------------------------------------------------------------------
        */

        if (
            last <= 7
        ) {
            return Array.from(
                {
                    length: last,
                },
                (
                    _,
                    index
                ) =>
                    index + 1
            );
        }

        const pages = [];

        /*
        |--------------------------------------------------------------------------
        | First
        |--------------------------------------------------------------------------
        */

        pages.push(1);

        /*
        |--------------------------------------------------------------------------
        | Left ellipsis
        |--------------------------------------------------------------------------
        */

        if (
            current > 4
        ) {
            pages.push(
                "..."
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Middle
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
            pages.push(
                page
            );
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
            pages.push(
                "..."
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Last
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
            (row) => {
                const id =
                    row?.[
                        props.rowKey
                    ];

                return selectedRows.value.includes(
                    id
                );
            }
        );
    });

/*
|--------------------------------------------------------------------------
| Toggle select all
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
    clearTimeout(
        searchTimer
    );

    search.value = "";

    /*
    |--------------------------------------------------------------------------
    | Server
    |--------------------------------------------------------------------------
    */

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
| Sync server current page
|--------------------------------------------------------------------------
*/

watch(
    () =>
        props.data
            ?.current_page,

    (page) => {
        if (
            props.mode !==
            "server"
        ) {
            return;
        }

        if (
            page !==
                undefined &&
            page !== null
        ) {
            const nextPage =
                Number(page);

            if (
                Number.isInteger(
                    nextPage
                ) &&
                nextPage > 0
            ) {
                currentPage.value =
                    nextPage;
            }
        }
    },

    {
        immediate: true,
    }
);

/*
|--------------------------------------------------------------------------
| Sync server page size
|--------------------------------------------------------------------------
*/

watch(
    () =>
        props.data?.per_page,

    (perPage) => {
        if (
            props.mode !==
            "server"
        ) {
            return;
        }

        if (
            perPage !==
                undefined &&
            perPage !== null
        ) {
            const nextSize =
                Number(
                    perPage
                );

            if (
                Number.isInteger(
                    nextSize
                ) &&
                nextSize > 0
            ) {
                currentPageSize.value =
                    nextSize;
            }
        }
    },

    {
        immediate: true,
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

                    <!-- Loading indicator -->
                    <svg
                        v-if="
                            mode ===
                                'server' &&
                            serverLoading
                        "
                        class="absolute right-3 top-1/2 h-4 w-4 -translate-y-1/2 animate-spin text-primary"
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
                </div>

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
                <!-- ================================================= -->
                <!-- HEADER -->
                <!-- ================================================= -->

                <thead
                    class="bg-slate-50"
                >
                    <tr>
                        <!-- Checkbox -->
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
                                    !displayRows.length ||
                                    isLoading
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

                <!-- ================================================= -->
                <!-- LOADING -->
                <!-- ================================================= -->

                <tbody
                    v-if="
                        isLoading
                    "
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
                                class="flex flex-col items-center justify-center gap-3 text-sm text-slate-500"
                            >
                                <svg
                                    class="h-6 w-6 animate-spin text-primary"
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

                                <span>
                                    Loading...
                                </span>
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
                    :disabled="
                        isLoading
                    "
                    class="rounded-lg border border-slate-200 bg-white px-2 py-1.5 text-xs outline-none focus:border-primary focus:ring-1 focus:ring-primary disabled:cursor-not-allowed disabled:opacity-50"
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
                            1 ||
                        isLoading
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
                        class="h-8 min-w-8 rounded-lg px-2 text-xs font-medium transition disabled:cursor-not-allowed disabled:opacity-50"
                        :class="
                            currentPage ===
                            page
                                ? 'bg-primary text-white'
                                : 'text-slate-600 hover:bg-slate-100'
                        "
                        :disabled="
                            isLoading
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
                            totalPages ||
                        isLoading
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