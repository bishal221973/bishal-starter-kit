<script setup>
import { computed, ref, watch, onBeforeUnmount } from "vue";
import { router } from "@inertiajs/vue3";

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
        validator: (value) => ["asc", "desc"].includes(value),
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
    | Theme
    |--------------------------------------------------------------------------
    */

    primaryColor: {
        type: String,
        default: "#3D98AB",
    },

    headerBgColor: {
        type: String,
        default: "#F8FAFC",
    },

    headerTextColor: {
        type: String,
        default: "#000000",
    },

    /*
    |--------------------------------------------------------------------------
    | View
    |--------------------------------------------------------------------------
    */

    defaultView: {
        type: String,
        default: "table",
        validator: (value) => ["table", "compact"].includes(value),
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
        default: () => [
            "csv",
            "excel",
            "json",
            "print",
            "copy",
        ],
    },

    /*
    |--------------------------------------------------------------------------
    | Export Scope
    |--------------------------------------------------------------------------
    |
    | current:
    |   Current displayed page.
    |
    | filtered:
    |   All filtered rows in client mode.
    |
    | selected:
    |   Selected rows.
    |
    */

    defaultExportScope: {
        type: String,
        default: "filtered",
        validator: (value) =>
            ["current", "filtered", "selected"].includes(value),
    },

    showExportScope: {
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
        ? Number(props.data?.per_page ?? props.pageSize)
        : props.pageSize
);

const sortBy = ref(props.defaultSort);

const sortDirection = ref(props.defaultDirection);

const selectedRows = ref([]);

const showFilterModal = ref(false);

const showColumnModal = ref(false);

const viewMode = ref(props.defaultView);

const pendingFilters = ref({});

/*
|--------------------------------------------------------------------------
| Export State
|--------------------------------------------------------------------------
*/

const exportMenuOpen = ref(false);

const exportScope = ref(props.defaultExportScope);

const exporting = ref(false);

/*
|--------------------------------------------------------------------------
| Column visibility
|--------------------------------------------------------------------------
*/

const columnVisibility = ref({});

/*
|--------------------------------------------------------------------------
| Search timer
|--------------------------------------------------------------------------
*/

let searchTimer = null;

/*
|--------------------------------------------------------------------------
| Normalize columns
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

const visibleColumns = computed(() => {
    return tableColumns.value.filter(
        (column) =>
            column.hidden !== true &&
            columnVisibility.value[column.key] !== false
    );
});

/*
|--------------------------------------------------------------------------
| Export columns
|--------------------------------------------------------------------------
*/

const exportColumns = computed(() => {
    return visibleColumns.value.filter(
        (column) => column.exportable !== false
    );
});

/*
|--------------------------------------------------------------------------
| Initialize columns
|--------------------------------------------------------------------------
*/

function initializeColumns() {
    const result = {};

    tableColumns.value.forEach((column) => {
        result[column.key] = column.hidden !== true;
    });

    columnVisibility.value = result;
}

initializeColumns();

watch(
    () => props.columns,
    () => {
        initializeColumns();
    },
    {
        deep: true,
    }
);

/*
|--------------------------------------------------------------------------
| Rows
|--------------------------------------------------------------------------
*/

const rows = computed(() => {
    if (props.mode === "server") {
        return Array.isArray(props.data?.data)
            ? props.data.data
            : [];
    }

    return Array.isArray(props.data)
        ? props.data
        : [];
});

/*
|--------------------------------------------------------------------------
| Value helper
|--------------------------------------------------------------------------
*/

function getValue(row, key) {
    if (!row || !key) {
        return null;
    }

    return String(key)
        .split(".")
        .reduce(
            (value, part) => value?.[part],
            row
        );
}

/*
|--------------------------------------------------------------------------
| Filter definitions
|--------------------------------------------------------------------------
*/

const filterDefinitions = computed(() => {
    return Array.isArray(props.filters)
        ? props.filters.filter(
              (filter) =>
                  filter &&
                  typeof filter === "object" &&
                  filter.key
          )
        : [];
});

/*
|--------------------------------------------------------------------------
| Active filter count
|--------------------------------------------------------------------------
*/

const activeFilterCount = computed(() => {
    return Object.entries(
        pendingFilters.value
    ).filter(([, value]) => {
        if (
            value === null ||
            value === undefined ||
            value === ""
        ) {
            return false;
        }

        if (
            typeof value === "object" &&
            !Array.isArray(value)
        ) {
            return Object.values(value).some(
                (v) =>
                    v !== "" &&
                    v !== null &&
                    v !== undefined
            );
        }

        if (Array.isArray(value)) {
            return value.length > 0;
        }

        return true;
    }).length;
});

/*
|--------------------------------------------------------------------------
| Client filtering
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

    const query = search.value
        .trim()
        .toLowerCase();

    if (query) {
        result = result.filter((row) => {
            return tableColumns.value.some(
                (column) => {
                    if (
                        column.searchable === false
                    ) {
                        return false;
                    }

                    const value = getValue(
                        row,
                        column.key
                    );

                    return String(value ?? "")
                        .toLowerCase()
                        .includes(query);
                }
            );
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Filters
    |--------------------------------------------------------------------------
    */

    Object.entries(
        pendingFilters.value
    ).forEach(([key, filterValue]) => {
        if (
            filterValue === null ||
            filterValue === undefined ||
            filterValue === ""
        ) {
            return;
        }

        if (
            Array.isArray(filterValue) &&
            filterValue.length === 0
        ) {
            return;
        }

        const definition =
            filterDefinitions.value.find(
                (filter) =>
                    filter.key === key
            );

        result = result.filter((row) => {
            const value = getValue(row, key);

            /*
            |--------------------------------------------------------------------------
            | Text
            |--------------------------------------------------------------------------
            */

            if (
                definition?.type === "text"
            ) {
                return String(value ?? "")
                    .toLowerCase()
                    .includes(
                        String(
                            filterValue
                        ).toLowerCase()
                    );
            }

            /*
            |--------------------------------------------------------------------------
            | Select
            |--------------------------------------------------------------------------
            */

            if (
                definition?.type === "select"
            ) {
                if (
                    Array.isArray(
                        filterValue
                    )
                ) {
                    return filterValue
                        .map(String)
                        .includes(
                            String(value)
                        );
                }

                return (
                    String(value) ===
                    String(filterValue)
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Multi select
            |--------------------------------------------------------------------------
            */

            if (
                definition?.type ===
                "multiselect"
            ) {
                if (
                    !Array.isArray(
                        filterValue
                    )
                ) {
                    return false;
                }

                return filterValue
                    .map(String)
                    .includes(
                        String(value)
                    );
            }

            /*
            |--------------------------------------------------------------------------
            | Boolean
            |--------------------------------------------------------------------------
            */

            if (
                definition?.type ===
                "boolean"
            ) {
                return (
                    String(value) ===
                    String(filterValue)
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Number range
            |--------------------------------------------------------------------------
            */

            if (
                definition?.type ===
                "number-range"
            ) {
                const number =
                    Number(value);

                if (
                    filterValue.min !==
                        "" &&
                    filterValue.min !==
                        null &&
                    filterValue.min !==
                        undefined
                ) {
                    if (
                        number <
                        Number(
                            filterValue.min
                        )
                    ) {
                        return false;
                    }
                }

                if (
                    filterValue.max !==
                        "" &&
                    filterValue.max !==
                        null &&
                    filterValue.max !==
                        undefined
                ) {
                    if (
                        number >
                        Number(
                            filterValue.max
                        )
                    ) {
                        return false;
                    }
                }

                return true;
            }

            /*
            |--------------------------------------------------------------------------
            | Date range
            |--------------------------------------------------------------------------
            */

            if (
                definition?.type ===
                "date-range"
            ) {
                if (!value) {
                    return false;
                }

                const date =
                    new Date(value);

                if (
                    Number.isNaN(
                        date.getTime()
                    )
                ) {
                    return false;
                }

                if (
                    filterValue.from
                ) {
                    const from =
                        new Date(
                            filterValue.from
                        );

                    if (
                        date < from
                    ) {
                        return false;
                    }
                }

                if (filterValue.to) {
                    const to =
                        new Date(
                            filterValue.to
                        );

                    to.setHours(
                        23,
                        59,
                        59,
                        999
                    );

                    if (
                        date > to
                    ) {
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

            if (
                Array.isArray(
                    filterValue
                )
            ) {
                return filterValue
                    .map(String)
                    .includes(
                        String(value)
                    );
            }

            return (
                String(value ?? "") ===
                String(filterValue)
            );
        });
    });

    /*
    |--------------------------------------------------------------------------
    | Sorting
    |--------------------------------------------------------------------------
    */

    if (
        props.sortable &&
        sortBy.value
    ) {
        const direction =
            sortDirection.value ===
            "asc"
                ? 1
                : -1;

        result = result
            .map((row, index) => ({
                row,
                index,
            }))
            .sort((a, b) => {
                const aValue =
                    getValue(
                        a.row,
                        sortBy.value
                    );

                const bValue =
                    getValue(
                        b.row,
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
                    return (
                        a.index -
                        b.index
                    );
                }

                const aNumber =
                    Number(aValue);

                const bNumber =
                    Number(bValue);

                if (
                    !Number.isNaN(
                        aNumber
                    ) &&
                    !Number.isNaN(
                        bNumber
                    )
                ) {
                    return (
                        (aNumber -
                            bNumber) *
                        direction
                    );
                }

                return (
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
                    ) * direction
                );
            })
            .map(
                (item) =>
                    item.row
            );
    }

    return result;
});

/*
|--------------------------------------------------------------------------
| Totals
|--------------------------------------------------------------------------
*/

const total = computed(() => {
    if (props.mode === "server") {
        return Math.max(
            0,
            Number(
                props.data?.total ?? 0
            )
        );
    }

    return processedClientRows.value
        .length;
});

const totalPages = computed(() => {
    if (!props.pagination) {
        return 1;
    }

    if (props.mode === "server") {
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
    if (props.mode === "server") {
        return rows.value;
    }

    if (!props.pagination) {
        return processedClientRows.value;
    }

    const start =
        (currentPage.value - 1) *
        currentPageSize.value;

    return processedClientRows.value.slice(
        start,
        start +
            currentPageSize.value
    );
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
        return Number(
            props.data?.from ?? 1
        );
    }

    if (!props.pagination) {
        return 1;
    }

    return (
        (currentPage.value - 1) *
            currentPageSize.value +
        1
    );
});

const showingTo = computed(() => {
    if (!total.value) {
        return 0;
    }

    if (props.mode === "server") {
        return Number(
            props.data?.to ??
                total.value
        );
    }

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
| Pagination pages
|--------------------------------------------------------------------------
*/

const paginationPages = computed(() => {
    const last = totalPages.value;

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

    const pages = [1];

    if (current > 4) {
        pages.push("...");
    }

    const start = Math.max(
        2,
        current - 1
    );

    const end = Math.min(
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
        pages.push("...");
    }

    pages.push(last);

    return pages;
});

/*
|--------------------------------------------------------------------------
| Server params
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

    Object.entries(
        pendingFilters.value
    ).forEach(([key, value]) => {
        if (
            value === null ||
            value === undefined ||
            value === ""
        ) {
            return;
        }

        if (
            Array.isArray(value)
        ) {
            value.forEach(
                (item, index) => {
                    params[
                        `filters[${key}][${index}]`
                    ] = item;
                }
            );

            return;
        }

        if (
            typeof value ===
                "object" &&
            value !== null
        ) {
            Object.entries(
                value
            ).forEach(
                ([
                    rangeKey,
                    rangeValue,
                ]) => {
                    if (
                        rangeValue !==
                            null &&
                        rangeValue !==
                            undefined &&
                        rangeValue !== ""
                    ) {
                        params[
                            `filters[${key}][${rangeKey}]`
                        ] =
                            rangeValue;
                    }
                }
            );

            return;
        }

        params[
            `filters[${key}]`
        ] = value;
    });

    Object.keys(params).forEach(
        (key) => {
            if (
                params[key] ===
                    undefined ||
                params[key] === null ||
                params[key] === ""
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
    if (props.mode !== "server") {
        return;
    }

    if (!props.route) {
        console.warn(
            "DataTable: `route` is required in server mode."
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

            showProgress: false,
        }
    );
}

/*
|--------------------------------------------------------------------------
| Search
|--------------------------------------------------------------------------
*/

watch(search, (value) => {
    if (props.mode === "client") {
        currentPage.value = 1;

        emit(
            "search",
            value
        );

        return;
    }

    clearTimeout(searchTimer);

    searchTimer = setTimeout(
        () => {
            currentPage.value = 1;

            loadServerData();
        },
        props.searchDebounce
    );
});

/*
|--------------------------------------------------------------------------
| Sorting
|--------------------------------------------------------------------------
*/

function sort(column) {
    if (
        !props.sortable ||
        !column ||
        column.sortable === false
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

    currentPage.value = 1;

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
        loadServerData();
    }
}

/*
|--------------------------------------------------------------------------
| Filter
|--------------------------------------------------------------------------
*/

function createEmptyFilter(
    filter
) {
    if (
        filter.type ===
        "number-range"
    ) {
        return {
            min: "",
            max: "",
        };
    }

    if (
        filter.type ===
        "date-range"
    ) {
        return {
            from: "",
            to: "",
        };
    }

    if (
        filter.type ===
        "multiselect"
    ) {
        return [];
    }

    return "";
}

function openFilters() {
    const copy = {};

    filterDefinitions.value.forEach(
        (filter) => {
            if (
                Object.prototype.hasOwnProperty.call(
                    pendingFilters.value,
                    filter.key
                )
            ) {
                const value =
                    pendingFilters.value[
                        filter.key
                    ];

                if (
                    Array.isArray(value)
                ) {
                    copy[
                        filter.key
                    ] = [...value];
                } else if (
                    typeof value ===
                        "object" &&
                    value !== null
                ) {
                    copy[
                        filter.key
                    ] = {
                        ...value,
                    };
                } else {
                    copy[
                        filter.key
                    ] = value;
                }
            } else {
                copy[
                    filter.key
                ] =
                    createEmptyFilter(
                        filter
                    );
            }
        }
    );

    pendingFilters.value =
        copy;

    showFilterModal.value =
        true;
}

function updatePendingFilter(
    key,
    value
) {
    pendingFilters.value = {
        ...pendingFilters.value,

        [key]: value,
    };
}

function applyFilters() {
    const cleaned = {};

    Object.entries(
        pendingFilters.value
    ).forEach(([key, value]) => {
        if (
            value === null ||
            value === undefined ||
            value === ""
        ) {
            return;
        }

        if (
            Array.isArray(value)
        ) {
            if (value.length) {
                cleaned[key] =
                    value;
            }

            return;
        }

        if (
            typeof value ===
                "object" &&
            value !== null
        ) {
            const cleanedObject =
                {};

            Object.entries(
                value
            ).forEach(
                ([
                    objectKey,
                    objectValue,
                ]) => {
                    if (
                        objectValue !==
                            null &&
                        objectValue !==
                            undefined &&
                        objectValue !== ""
                    ) {
                        cleanedObject[
                            objectKey
                        ] =
                            objectValue;
                    }
                }
            );

            if (
                Object.keys(
                    cleanedObject
                ).length
            ) {
                cleaned[key] =
                    cleanedObject;
            }

            return;
        }

        cleaned[key] = value;
    });

    pendingFilters.value =
        cleaned;

    currentPage.value = 1;

    emit(
        "filter-change",
        cleaned
    );

    showFilterModal.value =
        false;

    if (
        props.mode ===
        "server"
    ) {
        loadServerData();
    }
}

function clearFilters() {
    pendingFilters.value =
        {};

    currentPage.value = 1;

    emit(
        "filter-change",
        {}
    );

    showFilterModal.value =
        false;

    if (
        props.mode ===
        "server"
    ) {
        loadServerData();
    }
}

function clearSearch() {
    search.value = "";

    currentPage.value = 1;

    if (
        props.mode ===
        "server"
    ) {
        loadServerData();
    }
}

/*
|--------------------------------------------------------------------------
| Pagination
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

    if (
        props.mode ===
        "server"
    ) {
        loadServerData();
    }
}

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

    currentPage.value = 1;

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

const someSelected =
    computed(() => {
        return (
            selectedRows.value
                .length > 0 &&
            !allSelected.value
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
                    id !== null &&
                    id !==
                        undefined
            );

    if (allSelected.value) {
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

function toggleRow(row) {
    if (!row) {
        return;
    }

    const id =
        row[props.rowKey];

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
                (selectedId) =>
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

function clearSelection() {
    selectedRows.value = [];

    emit(
        "selection-change",
        []
    );
}

/*
|--------------------------------------------------------------------------
| Server synchronization
|--------------------------------------------------------------------------
*/

watch(
    () =>
        props.data?.current_page,
    (page) => {
        if (
            props.mode ===
                "server" &&
            page !== undefined &&
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
| Column helpers
|--------------------------------------------------------------------------
*/

function isColumnSortable(
    column
) {
    return (
        props.sortable &&
        column.sortable !==
            false
    );
}

function columnLabel(
    column
) {
    return (
        column.label ||
        column.key
    );
}

/*
|--------------------------------------------------------------------------
| Formatting
|--------------------------------------------------------------------------
*/

function formatDate(
    value,
    column
) {
    if (!value) {
        return "—";
    }

    try {
        const date =
            new Date(value);

        if (
            Number.isNaN(
                date.getTime()
            )
        ) {
            return value;
        }

        return new Intl.DateTimeFormat(
            column.locale ||
                undefined,
            column.dateOptions ||
                {
                    year: "numeric",
                    month: "short",
                    day: "numeric",
                }
        ).format(date);
    } catch {
        return value;
    }
}

function formatDateTime(
    value
) {
    if (!value) {
        return "—";
    }

    try {
        const date =
            new Date(value);

        if (
            Number.isNaN(
                date.getTime()
            )
        ) {
            return value;
        }

        return new Intl.DateTimeFormat(
            undefined,
            {
                year: "numeric",
                month: "short",
                day: "numeric",
                hour: "2-digit",
                minute: "2-digit",
            }
        ).format(date);
    } catch {
        return value;
    }
}

function formatNumber(
    value,
    column
) {
    if (
        value === null ||
        value === undefined ||
        value === ""
    ) {
        return "—";
    }

    return new Intl.NumberFormat(
        column.locale ||
            undefined,
        column.numberOptions ||
            {}
    ).format(Number(value));
}

function booleanValue(
    value
) {
    return (
        value === true ||
        value === 1 ||
        value === "1" ||
        value === "true" ||
        value === "yes"
    );
}

function getBadgeClass(
    value,
    column
) {
    if (
        typeof column.badgeClass ===
        "function"
    ) {
        return column.badgeClass(
            value
        );
    }

    if (
        column.badgeClass &&
        typeof column.badgeClass ===
            "object"
    ) {
        return (
            column.badgeClass[
                value
            ] ||
            "bg-slate-100 text-slate-700"
        );
    }

    const normalized =
        String(value).toLowerCase();

    if (
        [
            "active",
            "success",
            "approved",
            "completed",
        ].includes(normalized)
    ) {
        return "bg-emerald-100 text-emerald-700";
    }

    if (
        [
            "inactive",
            "disabled",
            "failed",
            "rejected",
        ].includes(normalized)
    ) {
        return "bg-red-100 text-red-700";
    }

    if (
        [
            "pending",
            "processing",
        ].includes(normalized)
    ) {
        return "bg-amber-100 text-amber-700";
    }

    return "bg-slate-100 text-slate-700";
}

/*
|--------------------------------------------------------------------------
| Column visibility
|--------------------------------------------------------------------------
*/

function toggleColumn(
    key
) {
    columnVisibility.value[
        key
    ] =
        !columnVisibility.value[
            key
        ];
}

/*
|--------------------------------------------------------------------------
| EXPORT
|--------------------------------------------------------------------------
*/

/*
|--------------------------------------------------------------------------
| Export rows
|--------------------------------------------------------------------------
*/

const exportRows = computed(
    () => {
        if (
            exportScope.value ===
            "selected"
        ) {
            if (
                !selectedRows.value
                    .length
            ) {
                return [];
            }

            const selectedSet =
                new Set(
                    selectedRows.value
                );

            /*
             * Client mode:
             * search all available rows.
             *
             * Server mode:
             * only selected rows currently
             * loaded in the browser.
             */
            const source =
                props.mode ===
                "client"
                    ? rows.value
                    : rows.value;

            return source.filter(
                (row) =>
                    selectedSet.has(
                        row?.[
                            props.rowKey
                        ]
                    )
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Current page
        |--------------------------------------------------------------------------
        */

        if (
            exportScope.value ===
            "current"
        ) {
            return displayRows.value;
        }

        /*
        |--------------------------------------------------------------------------
        | Filtered
        |--------------------------------------------------------------------------
        */

        if (
            props.mode ===
            "client"
        ) {
            return processedClientRows.value;
        }

        /*
        |--------------------------------------------------------------------------
        | Server mode
        |--------------------------------------------------------------------------
        |
        | Server mode only has the current
        | page in browser memory.
        |
        */

        return rows.value;
    }
);

/*
|--------------------------------------------------------------------------
| Export value formatting
|--------------------------------------------------------------------------
*/

function getExportValue(
    row,
    column
) {
    let value = getValue(
        row,
        column.key
    );

    if (
        value === null ||
        value === undefined
    ) {
        return "";
    }

    /*
    |--------------------------------------------------------------------------
    | Custom export formatter
    |--------------------------------------------------------------------------
    */

    if (
        typeof column.exportValue ===
        "function"
    ) {
        return column.exportValue(
            value,
            row,
            column
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Boolean
    |--------------------------------------------------------------------------
    */

    if (
        column.type ===
        "boolean"
    ) {
        return booleanValue(
            value
        )
            ? "Yes"
            : "No";
    }

    /*
    |--------------------------------------------------------------------------
    | Date
    |--------------------------------------------------------------------------
    */

    if (
        column.type === "date"
    ) {
        return formatDate(
            value,
            column
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Datetime
    |--------------------------------------------------------------------------
    */

    if (
        column.type ===
        "datetime"
    ) {
        return formatDateTime(
            value
        );
    }

    /*
    |--------------------------------------------------------------------------
    | Array/Object
    |--------------------------------------------------------------------------
    */

    if (
        typeof value ===
        "object"
    ) {
        try {
            return JSON.stringify(
                value
            );
        } catch {
            return String(value);
        }
    }

    return value;
}

/*
|--------------------------------------------------------------------------
| Prepare export data
|--------------------------------------------------------------------------
*/

function getExportData(
    data
) {
    const columns =
        exportColumns.value;

    return data.map(
        (row) => {
            const result = {};

            columns.forEach(
                (column) => {
                    result[
                        column.label ||
                            column.key
                    ] =
                        getExportValue(
                            row,
                            column
                        );
                }
            );

            return result;
        }
    );
}

/*
|--------------------------------------------------------------------------
| Export
|--------------------------------------------------------------------------
*/

async function runExport(
    type
) {
    if (
        !props.exportOptions.includes(
            type
        )
    ) {
        return;
    }

    /*
    |--------------------------------------------------------------------------
    | Selected validation
    |--------------------------------------------------------------------------
    */

    if (
        exportScope.value ===
            "selected" &&
        !selectedRows.value
            .length
    ) {
        emit(
            "export-error",
            {
                type,
                reason:
                    "no-selection",
            }
        );

        exportMenuOpen.value =
            false;

        return;
    }

    const data =
        exportRows.value;

    if (!data.length) {
        emit(
            "export-error",
            {
                type,
                reason:
                    "no-data",
            }
        );

        exportMenuOpen.value =
            false;

        return;
    }

    exporting.value = true;

    emit("export", {
        type,
        scope:
            exportScope.value,
        rows: data,
    });

    try {
        switch (type) {
            case "csv":
                exportCSV(data);
                break;

            case "excel":
                await exportExcel(
                    data
                );
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

        emit(
            "export-success",
            {
                type,
                scope:
                    exportScope.value,
                count: data.length,
            }
        );
    } catch (error) {
        console.error(
            "DataTable export error:",
            error
        );

        emit(
            "export-error",
            {
                type,
                scope:
                    exportScope.value,
                error,
            }
        );
    } finally {
        exporting.value =
            false;

        exportMenuOpen.value =
            false;
    }
}

/*
|--------------------------------------------------------------------------
| CSV
|--------------------------------------------------------------------------
*/

function exportCSV(data) {
    const rows =
        getExportData(data);

    if (!rows.length) {
        return;
    }

    const headers =
        Object.keys(
            rows[0]
        );

    const csv = [
        headers
            .map(csvEscape)
            .join(","),
        ...rows.map(
            (row) =>
                headers
                    .map(
                        (header) =>
                            csvEscape(
                                row[
                                    header
                                ]
                            )
                    )
                    .join(",")
        ),
    ].join("\n");

    /*
    |--------------------------------------------------------------------------
    | UTF-8 BOM
    |--------------------------------------------------------------------------
    |
    | Helps Excel correctly detect UTF-8
    | including Nepali characters.
    |
    */

    const content =
        "\uFEFF" + csv;

    downloadFile(
        content,
        `${props.exportFilename}.csv`,
        "text/csv;charset=utf-8;"
    );
}

function csvEscape(
    value
) {
    const string =
        String(value ?? "");

    if (
        string.includes(",") ||
        string.includes('"') ||
        string.includes("\n") ||
        string.includes("\r")
    ) {
        return `"${string.replace(
            /"/g,
            '""'
        )}"`;
    }

    return string;
}

/*
|--------------------------------------------------------------------------
| JSON
|--------------------------------------------------------------------------
*/

function exportJSON(data) {
    const exportData =
        getExportData(data);

    const json =
        JSON.stringify(
            exportData,
            null,
            2
        );

    downloadFile(
        json,
        `${props.exportFilename}.json`,
        "application/json;charset=utf-8;"
    );
}

/*
|--------------------------------------------------------------------------
| Excel
|--------------------------------------------------------------------------
*/

async function exportExcel(
    data
) {
    /*
    |--------------------------------------------------------------------------
    | Dynamic import
    |--------------------------------------------------------------------------
    |
    | XLSX is loaded only when the user
    | actually chooses Excel.
    |
    */

    const XLSX =
        await import("xlsx");

    const exportData =
        getExportData(data);

    const worksheet =
        XLSX.utils.json_to_sheet(
            exportData
        );

    /*
    |--------------------------------------------------------------------------
    | Auto width
    |--------------------------------------------------------------------------
    */

    const headers =
        Object.keys(
            exportData[0] || {}
        );

    worksheet["!cols"] =
        headers.map(
            (header) => {
                const maxLength =
                    Math.max(
                        header.length,
                        ...exportData.map(
                            (row) =>
                                String(
                                    row[
                                        header
                                    ] ??
                                        ""
                                ).length
                        )
                    );

                return {
                    wch: Math.min(
                        maxLength +
                            2,
                        50
                    ),
                };
            }
        );

    const workbook =
        XLSX.utils.book_new();

    XLSX.utils.book_append_sheet(
        workbook,
        worksheet,
        "Data"
    );

    XLSX.writeFile(
        workbook,
        `${props.exportFilename}.xlsx`
    );
}

/*
|--------------------------------------------------------------------------
| Copy
|--------------------------------------------------------------------------
*/

async function copyData(
    data
) {
    const exportData =
        getExportData(data);

    if (!exportData.length) {
        return;
    }

    const headers =
        Object.keys(
            exportData[0]
        );

    const text = [
        headers.join("\t"),

        ...exportData.map(
            (row) =>
                headers
                    .map(
                        (header) =>
                            String(
                                row[
                                    header
                                ] ??
                                    ""
                            ).replace(
                                /\t/g,
                                " "
                            )
                    )
                    .join("\t")
        ),
    ].join("\n");

    if (
        navigator.clipboard &&
        window.isSecureContext
    ) {
        await navigator.clipboard.writeText(
            text
        );

        return;
    }

    /*
    |--------------------------------------------------------------------------
    | Fallback
    |--------------------------------------------------------------------------
    */

    const textarea =
        document.createElement(
            "textarea"
        );

    textarea.value = text;

    textarea.style.position =
        "fixed";

    textarea.style.opacity =
        "0";

    document.body.appendChild(
        textarea
    );

    textarea.select();

    document.execCommand(
        "copy"
    );

    document.body.removeChild(
        textarea
    );
}

/*
|--------------------------------------------------------------------------
| Print
|--------------------------------------------------------------------------
*/

function printData(data) {
    const exportData =
        getExportData(data);

    if (!exportData.length) {
        return;
    }

    const columns =
        exportColumns.value;

    const title =
        escapeHTML(
            props.exportFilename
        );

    const html = `
        <!DOCTYPE html>

        <html>
        <head>
            <meta charset="UTF-8">

            <title>${title}</title>

            <style>

                * {
                    box-sizing: border-box;
                }

                body {
                    font-family:
                        Arial,
                        "Noto Sans",
                        sans-serif;

                    padding: 30px;

                    color: #1e293b;
                }

                h2 {
                    margin:
                        0 0 20px;

                    font-size: 20px;
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

                    padding: 8px;

                    text-align:
                        left;

                    font-size:
                        12px;
                }

                th {
                    background:
                        #f8fafc;

                    font-weight:
                        600;
                }

                tr:nth-child(
                    even
                ) {
                    background:
                        #fafafa;
                }

                @media print {

                    body {
                        padding: 10px;
                    }

                    table {
                        page-break-inside:
                            auto;
                    }

                    tr {
                        page-break-inside:
                            avoid;
                        page-break-after:
                            auto;
                    }

                    thead {
                        display:
                            table-header-group;
                    }
                }

            </style>
        </head>

        <body>

            <h2>${title}</h2>

            <table>

                <thead>

                    <tr>

                        ${columns
                            .map(
                                (
                                    column
                                ) =>
                                    `<th>${escapeHTML(
                                        column.label ||
                                            column.key
                                    )}</th>`
                            )
                            .join(
                                ""
                            )}

                    </tr>

                </thead>

                <tbody>

                    ${exportData
                        .map(
                            (
                                row
                            ) => `
                                <tr>

                                    ${columns
                                        .map(
                                            (
                                                column
                                            ) => `
                                                <td>
                                                    ${escapeHTML(
                                                        row[
                                                            column.label ||
                                                                column.key
                                                        ]
                                                    )}
                                                </td>
                                            `
                                        )
                                        .join(
                                            ""
                                        )}

                                </tr>
                            `
                        )
                        .join(
                            ""
                        )}

                </tbody>

            </table>

        </body>

        </html>
    `;

    const printWindow =
        window.open(
            "",
            "_blank",
            "width=1200,height=800"
        );

    if (!printWindow) {
        throw new Error(
            "Unable to open print window. Please allow popups."
        );
    }

    printWindow.document.open();

    printWindow.document.write(
        html
    );

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

function escapeHTML(
    value
) {
    return String(
        value ?? ""
    )
        .replace(
            /&/g,
            "&amp;"
        )
        .replace(
            /</g,
            "&lt;"
        )
        .replace(
            />/g,
            "&gt;"
        )
        .replace(
            /"/g,
            "&quot;"
        )
        .replace(
            /'/g,
            "&#039;"
        );
}

/*
|--------------------------------------------------------------------------
| Download
|--------------------------------------------------------------------------
*/

function downloadFile(
    content,
    filename,
    mimeType
) {
    const blob =
        new Blob(
            [content],
            {
                type: mimeType,
            }
        );

    const url =
        URL.createObjectURL(
            blob
        );

    const link =
        document.createElement(
            "a"
        );

    link.href = url;

    link.download =
        filename;

    document.body.appendChild(
        link
    );

    link.click();

    document.body.removeChild(
        link
    );

    setTimeout(() => {
        URL.revokeObjectURL(
            url
        );
    }, 100);
}

/*
|--------------------------------------------------------------------------
| Export menu
|--------------------------------------------------------------------------
*/

function toggleExportMenu() {
    exportMenuOpen.value =
        !exportMenuOpen.value;

    if (
        exportMenuOpen.value
    ) {
        showColumnModal.value =
            false;
    }
}

/*
|--------------------------------------------------------------------------
| Click outside
|--------------------------------------------------------------------------
*/

function handleDocumentClick(
    event
) {
    const target =
        event.target;

    if (
        !target.closest(
            "[data-export-menu]"
        )
    ) {
        exportMenuOpen.value =
            false;
    }

    if (
        !target.closest(
            "[data-column-menu]"
        )
    ) {
        showColumnModal.value =
            false;
    }
}

/*
|--------------------------------------------------------------------------
| Escape
|--------------------------------------------------------------------------
*/

function handleEscape(event) {
    if (
        event.key ===
        "Escape"
    ) {
        showFilterModal.value =
            false;

        showColumnModal.value =
            false;

        exportMenuOpen.value =
            false;
    }
}

if (
    typeof window !==
    "undefined"
) {
    window.addEventListener(
        "keydown",
        handleEscape
    );

    document.addEventListener(
        "click",
        handleDocumentClick
    );
}

/*
|--------------------------------------------------------------------------
| Cleanup
|--------------------------------------------------------------------------
*/

onBeforeUnmount(() => {
    clearTimeout(
        searchTimer
    );

    if (
        typeof window !==
        "undefined"
    ) {
        window.removeEventListener(
            "keydown",
            handleEscape
        );

        document.removeEventListener(
            "click",
            handleDocumentClick
        );
    }
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
    exportData: runExport,
});
</script>

<template>
    <div
        class="w-full overflow-hidden rounded-xl border border-slate-200 bg-white"
        :style="{
            '--dt-primary': primaryColor,
        }"
    >
        <!-- ===================================================== -->
        <!-- TOOLBAR -->
        <!-- ===================================================== -->

        <div
            class="flex min-h-[68px] flex-wrap items-center justify-between gap-3 border-b border-slate-200 bg-white px-4 py-3"
        >
            <!-- LEFT -->

            <div
                class="flex min-w-0 flex-1 flex-wrap items-center gap-2"
            >
                <!-- SEARCH -->

                <div
                    v-if="searchable"
                    class="relative w-full sm:w-[300px]"
                >
                    <svg
                        class="pointer-events-none absolute left-3 top-1/2 h-4 w-4 -translate-y-1/2 text-slate-400"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <circle
                            cx="11"
                            cy="11"
                            r="8"
                        />

                        <path
                            d="m21 21-4.3-4.3"
                        />
                    </svg>

                    <input
                        v-model="search"
                        type="search"
                        :placeholder="searchPlaceholder"
                        class="h-10 w-full rounded-lg border border-slate-200 bg-slate-50 pl-9 pr-9 text-sm text-slate-700 outline-none transition focus:border-[var(--dt-primary)] focus:bg-white focus:ring-1 focus:ring-[var(--dt-primary)]"
                    />

                    <button
                        v-if="search"
                        type="button"
                        class="absolute right-2 top-1/2 flex h-6 w-6 -translate-y-1/2 items-center justify-center rounded text-slate-400 hover:bg-slate-100 hover:text-slate-700"
                        @click="clearSearch"
                    >
                        <svg
                            class="h-4 w-4"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                d="M6 6l12 12M18 6 6 18"
                            />
                        </svg>
                    </button>
                </div>

                <!-- FILTER -->

                <button
                    v-if="
                        filterable &&
                        filterDefinitions.length
                    "
                    type="button"
                    class="relative inline-flex h-10 items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 text-sm font-medium text-slate-700 shadow-sm transition hover:border-slate-300 hover:bg-slate-50"
                    @click="openFilters"
                >
                    <svg
                        class="h-4 w-4"
                        viewBox="0 0 24 24"
                        fill="none"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            d="M4 6h16M7 12h10m-7 6h4"
                        />
                    </svg>

                    Filters

                    <span
                        v-if="
                            activeFilterCount
                        "
                        class="inline-flex min-w-5 items-center justify-center rounded-full px-1.5 py-0.5 text-[11px] font-bold text-white"
                        :style="{
                            backgroundColor:
                                primaryColor,
                        }"
                    >
                        {{ activeFilterCount }}
                    </span>
                </button>

                <!-- CLEAR -->

                <button
                    v-if="activeFilterCount"
                    type="button"
                    class="inline-flex h-10 items-center gap-1 rounded-lg bg-red-50 px-3 text-sm font-medium text-red-600 hover:bg-red-100"
                    @click="clearFilters"
                >
                    Clear
                </button>

                <!-- EXPORT -->

                <div
                    v-if="
                        exportable &&
                        exportOptions.length
                    "
                    class="relative"
                    data-export-menu
                >
                    <button
                        type="button"
                        :disabled="exporting"
                        class="inline-flex h-10 items-center gap-2 rounded-lg border border-slate-200 bg-white px-3 text-sm font-medium text-slate-700 shadow-sm transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-60"
                        @click.stop="
                            toggleExportMenu
                        "
                    >
                        <!-- Download icon -->

                        <svg
                            v-if="!exporting"
                            class="h-4 w-4"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M12 3v12m0 0 4-4m-4 4-4-4M5 21h14"
                            />
                        </svg>

                        <!-- Loading -->

                        <svg
                            v-else
                            class="h-4 w-4 animate-spin"
                            viewBox="0 0 24 24"
                            fill="none"
                        >
                            <circle
                                cx="12"
                                cy="12"
                                r="9"
                                class="opacity-25"
                                stroke="currentColor"
                                stroke-width="3"
                            />

                            <path
                                d="M21 12a9 9 0 0 0-9-9"
                                stroke="currentColor"
                                stroke-width="3"
                            />
                        </svg>

                        {{
                            exporting
                                ? "Exporting..."
                                : "Export"
                        }}

                        <svg
                            v-if="!exporting"
                            class="h-4 w-4"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="m6 9 6 6 6-6"
                            />
                        </svg>
                    </button>

                    <!-- EXPORT MENU -->

                    <div
                        v-if="
                            exportMenuOpen
                        "
                        class="absolute left-0 z-[100] mt-2 w-64 overflow-hidden rounded-xl border border-slate-200 bg-white p-1.5 shadow-2xl"
                        @click.stop
                    >
                        <!-- SCOPE -->

                        <div
                            v-if="
                                showExportScope
                            "
                            class="border-b border-slate-100 px-2 pb-2 pt-1"
                        >
                            <p
                                class="mb-2 px-1 text-[11px] font-semibold uppercase tracking-wide text-slate-400"
                            >
                                Export records
                            </p>

                            <label
                                class="flex cursor-pointer items-center gap-2 rounded-lg px-2 py-2 hover:bg-slate-50"
                            >
                                <input
                                    v-model="
                                        exportScope
                                    "
                                    type="radio"
                                    value="filtered"
                                    :style="{
                                        accentColor:
                                            primaryColor,
                                    }"
                                />

                                <span
                                    class="text-sm text-slate-700"
                                >
                                    All filtered
                                </span>
                            </label>

                            <label
                                class="flex cursor-pointer items-center gap-2 rounded-lg px-2 py-2 hover:bg-slate-50"
                            >
                                <input
                                    v-model="
                                        exportScope
                                    "
                                    type="radio"
                                    value="current"
                                    :style="{
                                        accentColor:
                                            primaryColor,
                                    }"
                                />

                                <span
                                    class="text-sm text-slate-700"
                                >
                                    Current page
                                </span>
                            </label>

                            <label
                                class="flex cursor-pointer items-center gap-2 rounded-lg px-2 py-2 hover:bg-slate-50"
                                :class="
                                    selectedRows.length
                                        ? ''
                                        : 'opacity-50'
                                "
                            >
                                <input
                                    v-model="
                                        exportScope
                                    "
                                    type="radio"
                                    value="selected"
                                    :disabled="
                                        !selectedRows.length
                                    "
                                    :style="{
                                        accentColor:
                                            primaryColor,
                                    }"
                                />

                                <span
                                    class="text-sm text-slate-700"
                                >
                                    Selected
                                    <span
                                        v-if="
                                            selectedRows.length
                                        "
                                        class="text-xs text-slate-400"
                                    >
                                        ({{
                                            selectedRows.length
                                        }})
                                    </span>
                                </span>
                            </label>
                        </div>

                        <!-- CSV -->

                        <button
                            v-if="
                                exportOptions.includes(
                                    'csv'
                                )
                            "
                            type="button"
                            class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-left text-sm text-slate-700 hover:bg-slate-50"
                            @click="
                                runExport('csv')
                            "
                        >
                            <span
                                class="flex h-8 w-8 items-center justify-center rounded-lg bg-emerald-50 text-emerald-600"
                            >
                                CSV
                            </span>

                            <span>
                                <span
                                    class="block font-medium"
                                >
                                    Export CSV
                                </span>

                                <span
                                    class="block text-[11px] text-slate-400"
                                >
                                    Comma separated
                                </span>
                            </span>
                        </button>

                        <!-- EXCEL -->

                        <button
                            v-if="
                                exportOptions.includes(
                                    'excel'
                                )
                            "
                            type="button"
                            class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-left text-sm text-slate-700 hover:bg-slate-50"
                            @click="
                                runExport(
                                    'excel'
                                )
                            "
                        >
                            <span
                                class="flex h-8 w-8 items-center justify-center rounded-lg bg-green-50 text-green-600"
                            >
                                XLS
                            </span>

                            <span>
                                <span
                                    class="block font-medium"
                                >
                                    Export Excel
                                </span>

                                <span
                                    class="block text-[11px] text-slate-400"
                                >
                                    Excel workbook
                                </span>
                            </span>
                        </button>

                        <!-- JSON -->

                        <button
                            v-if="
                                exportOptions.includes(
                                    'json'
                                )
                            "
                            type="button"
                            class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-left text-sm text-slate-700 hover:bg-slate-50"
                            @click="
                                runExport(
                                    'json'
                                )
                            "
                        >
                            <span
                                class="flex h-8 w-8 items-center justify-center rounded-lg bg-amber-50 text-amber-600"
                            >
                                JSON
                            </span>

                            <span>
                                <span
                                    class="block font-medium"
                                >
                                    Export JSON
                                </span>

                                <span
                                    class="block text-[11px] text-slate-400"
                                >
                                    Raw data
                                </span>
                            </span>
                        </button>

                        <div
                            class="my-1 border-t border-slate-100"
                        />

                        <!-- COPY -->

                        <button
                            v-if="
                                exportOptions.includes(
                                    'copy'
                                )
                            "
                            type="button"
                            class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-left text-sm text-slate-700 hover:bg-slate-50"
                            @click="
                                runExport(
                                    'copy'
                                )
                            "
                        >
                            <svg
                                class="h-4 w-4 text-slate-400"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <rect
                                    x="9"
                                    y="9"
                                    width="12"
                                    height="12"
                                    rx="2"
                                />

                                <path
                                    d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"
                                />
                            </svg>

                            Copy
                        </button>

                        <!-- PRINT -->

                        <button
                            v-if="
                                exportOptions.includes(
                                    'print'
                                )
                            "
                            type="button"
                            class="flex w-full items-center gap-3 rounded-lg px-3 py-2.5 text-left text-sm text-slate-700 hover:bg-slate-50"
                            @click="
                                runExport(
                                    'print'
                                )
                            "
                        >
                            <svg
                                class="h-4 w-4 text-slate-400"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path
                                    d="M6 9V2h12v7"
                                />

                                <path
                                    d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"
                                />

                                <path
                                    d="M6 14h12v8H6z"
                                />
                            </svg>

                            Print
                        </button>
                    </div>
                </div>

                <!-- CUSTOM TOOLBAR -->

                <slot
                    name="toolbar"
                    :selected="selectedRows"
                />
            </div>

            <!-- RIGHT -->

            <div
                class="flex shrink-0 items-center gap-2"
            >
                <!-- SELECTION -->

                <div
                    v-if="
                        selectable &&
                        selectedRows.length
                    "
                    class="hidden text-sm text-slate-500 md:block"
                >
                    {{
                        selectedRows.length
                    }}
                    selected
                </div>

                <!-- VIEW SWITCHER -->

                <div
                    v-if="
                        showViewSwitcher
                    "
                    class="hidden rounded-lg border border-slate-200 bg-slate-50 p-1 sm:flex"
                >
                    <button
                        type="button"
                        class="rounded-md p-1.5 transition"
                        :class="
                            viewMode ===
                            'table'
                                ? 'bg-white text-slate-800 shadow-sm'
                                : 'text-slate-400 hover:text-slate-700'
                        "
                        title="Table view"
                        @click="
                            viewMode =
                                'table'
                        "
                    >
                        <svg
                            class="h-4 w-4"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <rect
                                x="3"
                                y="4"
                                width="18"
                                height="16"
                                rx="2"
                            />

                            <path
                                d="M3 10h18M9 4v16"
                            />
                        </svg>
                    </button>

                    <button
                        type="button"
                        class="rounded-md p-1.5 transition"
                        :class="
                            viewMode ===
                            'compact'
                                ? 'bg-white text-slate-800 shadow-sm'
                                : 'text-slate-400 hover:text-slate-700'
                        "
                        title="Compact view"
                        @click="
                            viewMode =
                                'compact'
                        "
                    >
                        <svg
                            class="h-4 w-4"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                d="M4 6h16M4 12h16M4 18h16"
                            />
                        </svg>
                    </button>
                </div>

                <!-- COLUMN MANAGER -->

                <div
                    v-if="
                        showColumnManager
                    "
                    class="relative"
                    data-column-menu
                >
                    <button
                        type="button"
                        class="inline-flex h-10 items-center justify-center rounded-lg border border-slate-200 bg-white px-3 text-sm text-slate-600 shadow-sm hover:bg-slate-50"
                        @click.stop="
                            showColumnModal =
                                !showColumnModal
                        "
                    >
                        <svg
                            class="h-4 w-4"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                d="M4 5h16M4 12h16M4 19h16"
                            />

                            <circle
                                cx="8"
                                cy="5"
                                r="2"
                                fill="currentColor"
                            />

                            <circle
                                cx="15"
                                cy="12"
                                r="2"
                                fill="currentColor"
                            />

                            <circle
                                cx="10"
                                cy="19"
                                r="2"
                                fill="currentColor"
                            />
                        </svg>

                        <span
                            class="ml-2 hidden md:inline"
                        >
                            Columns
                        </span>
                    </button>

                    <!-- COLUMN DROPDOWN -->

                    <div
                        v-if="
                            showColumnModal
                        "
                        class="absolute right-0 z-40 mt-2 w-64 rounded-xl border border-slate-200 bg-white p-3 shadow-xl"
                        @click.stop
                    >
                        <div
                            class="mb-2 flex items-center justify-between"
                        >
                            <span
                                class="text-sm font-semibold text-slate-800"
                            >
                                Columns
                            </span>

                            <button
                                type="button"
                                class="text-xs text-slate-400 hover:text-slate-700"
                                @click="
                                    initializeColumns
                                "
                            >
                                Reset
                            </button>
                        </div>

                        <div
                            class="max-h-64 space-y-1 overflow-y-auto"
                        >
                            <label
                                v-for="column in tableColumns"
                                :key="
                                    column.key
                                "
                                class="flex cursor-pointer items-center gap-2 rounded-lg px-2 py-2 hover:bg-slate-50"
                            >
                                <input
                                    type="checkbox"
                                    :checked="
                                        columnVisibility[
                                            column.key
                                        ]
                                    "
                                    class="rounded border-slate-300"
                                    :style="{
                                        accentColor:
                                            primaryColor,
                                    }"
                                    @change="
                                        toggleColumn(
                                            column.key
                                        )
                                    "
                                />

                                <span
                                    class="text-sm text-slate-600"
                                >
                                    {{
                                        columnLabel(
                                            column
                                        )
                                    }}
                                </span>
                            </label>
                        </div>
                    </div>
                </div>

                <!-- CUSTOM RIGHT -->

                <slot
                    name="toolbar-right"
                    :selected="selectedRows"
                />
            </div>
        </div>

        <!-- ===================================================== -->
        <!-- TABLE -->
        <!-- ===================================================== -->

        <div
            class="w-full overflow-x-auto"
        >
            <table
                class="w-full min-w-[760px] text-left text-sm"
                :class="
                    viewMode ===
                    'compact'
                        ? 'table-compact'
                        : ''
                "
            >
                <!-- HEADER -->

                <thead
                    :style="{
                        backgroundColor:
                            headerBgColor,
                        color: headerTextColor,
                    }"
                >
                    <tr
                        class="border-b border-slate-200"
                    >
                        <!-- SELECT -->

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
                                :indeterminate="
                                    someSelected
                                "
                                class="rounded border-slate-300"
                                :style="{
                                    accentColor:
                                        primaryColor,
                                }"
                                @change="
                                    toggleSelectAll
                                "
                            />
                        </th>

                        <!-- COLUMNS -->

                        <th
                            v-for="column in visibleColumns"
                            :key="
                                column.key
                            "
                            class="whitespace-nowrap px-4 py-3 text-xs font-semibold uppercase tracking-wide"
                            :class="{
                                'cursor-pointer select-none':
                                    isColumnSortable(
                                        column
                                    ),
                            }"
                            @click="
                                sort(column)
                            "
                        >
                            <div
                                class="flex items-center gap-1"
                            >
                                <span>
                                    {{
                                        columnLabel(
                                            column
                                        )
                                    }}
                                </span>

                                <!-- SORT -->

                                <span
                                    v-if="
                                        isColumnSortable(
                                            column
                                        )
                                    "
                                    class="flex flex-col"
                                >
                                    <svg
                                        v-if="
                                            sortBy !==
                                            column.key
                                        "
                                        class="h-3 w-3 text-slate-300"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                    >
                                        <path
                                            d="m8 9 4-4 4 4M8 15l4 4 4-4"
                                        />
                                    </svg>

                                    <svg
                                        v-else-if="
                                            sortDirection ===
                                            'asc'
                                        "
                                        class="h-3 w-3"
                                        :style="{
                                            color:
                                                primaryColor,
                                        }"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                    >
                                        <path
                                            d="m6 15 6-6 6 6"
                                        />
                                    </svg>

                                    <svg
                                        v-else
                                        class="h-3 w-3"
                                        :style="{
                                            color:
                                                primaryColor,
                                        }"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="2"
                                    >
                                        <path
                                            d="m6 9 6 6 6-6"
                                        />
                                    </svg>
                                </span>
                            </div>
                        </th>

                        <!-- ACTIONS -->

                        <th
                            v-if="
                                $slots.actions
                            "
                            class="whitespace-nowrap px-4 py-3 text-right text-xs font-semibold uppercase tracking-wide"
                        >
                            Actions
                        </th>
                    </tr>
                </thead>

                <!-- BODY -->

                <tbody
                    class="divide-y divide-slate-100"
                >
                    <!-- LOADING -->

                    <tr v-if="loading">
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
                            class="px-4 py-14 text-center"
                        >
                            <div
                                class="flex flex-col items-center justify-center gap-3"
                            >
                                <div
                                    class="h-7 w-7 animate-spin rounded-full border-2 border-slate-200 border-t-[var(--dt-primary)]"
                                />

                                <span
                                    class="text-sm text-slate-400"
                                >
                                    Loading...
                                </span>
                            </div>
                        </td>
                    </tr>

                    <!-- EMPTY -->

                    <tr
                        v-else-if="
                            !displayRows.length
                        "
                    >
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
                            class="px-4 py-14 text-center"
                        >
                            <div
                                class="flex flex-col items-center justify-center"
                            >
                                <div
                                    class="mb-3 flex h-12 w-12 items-center justify-center rounded-full bg-slate-100"
                                >
                                    <svg
                                        class="h-6 w-6 text-slate-400"
                                        viewBox="0 0 24 24"
                                        fill="none"
                                        stroke="currentColor"
                                        stroke-width="1.8"
                                    >
                                        <circle
                                            cx="11"
                                            cy="11"
                                            r="7"
                                        />

                                        <path
                                            d="m20 20-4-4"
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
                                        search ||
                                        activeFilterCount
                                    "
                                    type="button"
                                    class="mt-3 text-xs font-medium"
                                    :style="{
                                        color:
                                            primaryColor,
                                    }"
                                    @click="
                                        clearSearch();
                                        clearFilters();
                                    "
                                >
                                    Clear search &
                                    filters
                                </button>
                            </div>
                        </td>
                    </tr>

                    <!-- ROWS -->

                    <tr
                        v-for="row in displayRows"
                        v-else
                        :key="
                            row[rowKey]
                        "
                        class="group transition hover:bg-slate-50"
                        @click="
                            emit(
                                'row-click',
                                row
                            )
                        "
                    >
                        <!-- SELECT -->

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
                                    selectedRows.includes(
                                        row[
                                            rowKey
                                        ]
                                    )
                                "
                                class="rounded border-slate-300"
                                :style="{
                                    accentColor:
                                        primaryColor,
                                }"
                                @change="
                                    toggleRow(
                                        row
                                    )
                                "
                            />
                        </td>

                        <!-- CELLS -->

                        <td
                            v-for="column in visibleColumns"
                            :key="
                                column.key
                            "
                            class="px-4 py-3 text-slate-600"
                            :class="
                                viewMode ===
                                'compact'
                                    ? 'py-2'
                                    : ''
                            "
                        >
                            <!-- CUSTOM SLOT -->

                            <slot
                                v-if="
                                    $slots[
                                        `cell-${column.key}`
                                    ]
                                "
                                :name="`cell-${column.key}`"
                                :row="row"
                                :value="
                                    getValue(
                                        row,
                                        column.key
                                    )
                                "
                                :column="
                                    column
                                "
                            />

                            <!-- DEFAULT -->

                            <template v-else>
                                <!-- TEXT -->

                                <span
                                    v-if="
                                        !column.type ||
                                        column.type ===
                                            'text'
                                    "
                                    :class="
                                        column.class
                                    "
                                >
                                    {{
                                        getValue(
                                            row,
                                            column.key
                                        ) ??
                                        "—"
                                    }}
                                </span>

                                <!-- NUMBER -->

                                <span
                                    v-else-if="
                                        column.type ===
                                        'number'
                                    "
                                    class="tabular-nums"
                                >
                                    {{
                                        formatNumber(
                                            getValue(
                                                row,
                                                column.key
                                            ),
                                            column
                                        )
                                    }}
                                </span>

                                <!-- DATE -->

                                <span
                                    v-else-if="
                                        column.type ===
                                        'date'
                                    "
                                >
                                    {{
                                        formatDate(
                                            getValue(
                                                row,
                                                column.key
                                            ),
                                            column
                                        )
                                    }}
                                </span>

                                <!-- DATETIME -->

                                <span
                                    v-else-if="
                                        column.type ===
                                        'datetime'
                                    "
                                >
                                    {{
                                        formatDateTime(
                                            getValue(
                                                row,
                                                column.key
                                            )
                                        )
                                    }}
                                </span>

                                <!-- BOOLEAN -->

                                <span
                                    v-else-if="
                                        column.type ===
                                        'boolean'
                                    "
                                >
                                    <span
                                        v-if="
                                            booleanValue(
                                                getValue(
                                                    row,
                                                    column.key
                                                )
                                            )
                                        "
                                        class="inline-flex items-center gap-1 rounded-full bg-emerald-100 px-2.5 py-1 text-xs font-medium text-emerald-700"
                                    >
                                        <span
                                            class="h-1.5 w-1.5 rounded-full bg-emerald-500"
                                        />

                                        Yes
                                    </span>

                                    <span
                                        v-else
                                        class="inline-flex items-center gap-1 rounded-full bg-slate-100 px-2.5 py-1 text-xs font-medium text-slate-500"
                                    >
                                        <span
                                            class="h-1.5 w-1.5 rounded-full bg-slate-400"
                                        />

                                        No
                                    </span>
                                </span>

                                <!-- BADGE -->

                                <span
                                    v-else-if="
                                        column.type ===
                                        'badge'
                                    "
                                    class="inline-flex rounded-full px-2.5 py-1 text-xs font-medium"
                                    :class="
                                        getBadgeClass(
                                            getValue(
                                                row,
                                                column.key
                                            ),
                                            column
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
                                </span>

                                <!-- DEFAULT -->

                                <span v-else>
                                    {{
                                        getValue(
                                            row,
                                            column.key
                                        ) ??
                                        "—"
                                    }}
                                </span>
                            </template>
                        </td>

                        <!-- ACTIONS -->

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
            </table>
        </div>

        <!-- ===================================================== -->
        <!-- PAGINATION -->
        <!-- ===================================================== -->

        <div
            v-if="pagination"
            class="flex flex-col gap-3 border-t border-slate-200 px-4 py-3 sm:flex-row sm:items-center sm:justify-between"
        >
            <div
                class="text-xs text-slate-500"
            >
                Showing
                <span
                    class="font-medium text-slate-700"
                >
                    {{ showingFrom }}
                </span>
                -
                <span
                    class="font-medium text-slate-700"
                >
                    {{ showingTo }}
                </span>
                of
                <span
                    class="font-medium text-slate-700"
                >
                    {{ total }}
                </span>
                records
            </div>

            <div
                class="flex flex-wrap items-center gap-2"
            >
                <!-- PAGE SIZE -->

                <select
                    :value="
                        currentPageSize
                    "
                    class="h-9 rounded-lg border border-slate-200 bg-white px-2 text-xs text-slate-600 outline-none focus:border-[var(--dt-primary)]"
                    @change="
                        changePageSize(
                            $event.target
                                .value
                        )
                    "
                >
                    <option
                        v-for="size in pageSizeOptions"
                        :key="size"
                        :value="size"
                    >
                        {{ size }} / page
                    </option>
                </select>

                <!-- FIRST -->

                <button
                    type="button"
                    class="hidden h-9 rounded-lg border border-slate-200 px-2 text-xs text-slate-600 hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-40 sm:block"
                    :disabled="
                        currentPage <=
                        1
                    "
                    @click="
                        changePage(1)
                    "
                >
                    First
                </button>

                <!-- PREVIOUS -->

                <button
                    type="button"
                    class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-40"
                    :disabled="
                        currentPage <=
                        1
                    "
                    @click="
                        changePage(
                            currentPage -
                                1
                        )
                    "
                >
                    ‹
                </button>

                <!-- PAGES -->

                <template
                    v-for="(
                        page, index
                    ) in paginationPages"
                    :key="`${page}-${index}`"
                >
                    <span
                        v-if="
                            page ===
                            '...'
                        "
                        class="px-1 text-slate-400"
                    >
                        …
                    </span>

                    <button
                        v-else
                        type="button"
                        class="flex h-9 min-w-9 items-center justify-center rounded-lg px-2 text-xs font-medium transition"
                        :class="
                            currentPage ===
                            page
                                ? 'text-white shadow-sm'
                                : 'border border-slate-200 text-slate-600 hover:bg-slate-50'
                        "
                        :style="
                            currentPage ===
                            page
                                ? {
                                      backgroundColor:
                                          primaryColor,
                                  }
                                : {}
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

                <!-- NEXT -->

                <button
                    type="button"
                    class="flex h-9 w-9 items-center justify-center rounded-lg border border-slate-200 text-slate-600 hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-40"
                    :disabled="
                        currentPage >=
                        totalPages
                    "
                    @click="
                        changePage(
                            currentPage +
                                1
                        )
                    "
                >
                    ›
                </button>

                <!-- LAST -->

                <button
                    type="button"
                    class="hidden h-9 rounded-lg border border-slate-200 px-2 text-xs text-slate-600 hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-40 sm:block"
                    :disabled="
                        currentPage >=
                        totalPages
                    "
                    @click="
                        changePage(
                            totalPages
                        )
                    "
                >
                    Last
                </button>
            </div>
        </div>

        <!-- ===================================================== -->
        <!-- FILTER MODAL -->
        <!-- ===================================================== -->

        <Teleport to="body">
            <div
                v-if="
                    showFilterModal
                "
                class="fixed inset-0 z-[999] flex items-center justify-center p-4"
            >
                <!-- OVERLAY -->

                <div
                    class="absolute inset-0 bg-slate-900/40 backdrop-blur-sm"
                    @click="
                        showFilterModal =
                            false
                    "
                />

                <!-- MODAL -->

                <div
                    class="relative z-10 flex max-h-[90vh] w-full max-w-2xl flex-col overflow-hidden rounded-2xl bg-white shadow-2xl"
                >
                    <!-- HEADER -->

                    <div
                        class="flex items-center justify-between border-b border-slate-200 px-5 py-4"
                    >
                        <div>
                            <h3
                                class="text-base font-semibold text-slate-800"
                            >
                                Filter Records
                            </h3>

                            <p
                                class="mt-0.5 text-xs text-slate-400"
                            >
                                Refine your results
                                using the filters
                                below.
                            </p>
                        </div>

                        <button
                            type="button"
                            class="flex h-8 w-8 items-center justify-center rounded-lg text-slate-400 hover:bg-slate-100 hover:text-slate-700"
                            @click="
                                showFilterModal =
                                    false
                            "
                        >
                            <svg
                                class="h-5 w-5"
                                viewBox="0 0 24 24"
                                fill="none"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path
                                    d="M6 6l12 12M18 6 6 18"
                                />
                            </svg>
                        </button>
                    </div>

                    <!-- BODY -->

                    <div
                        class="overflow-y-auto p-5"
                    >
                        <div
                            class="grid grid-cols-1 gap-5 sm:grid-cols-2"
                        >
                            <div
                                v-for="filter in filterDefinitions"
                                :key="
                                    filter.key
                                "
                                class="space-y-2"
                                :class="
                                    filter.fullWidth
                                        ? 'sm:col-span-2'
                                        : ''
                                "
                            >
                                <label
                                    class="block text-sm font-medium text-slate-700"
                                >
                                    {{
                                        filter.label ||
                                        filter.key
                                    }}
                                </label>

                                <!-- TEXT -->

                                <input
                                    v-if="
                                        filter.type ===
                                        'text'
                                    "
                                    type="text"
                                    :value="
                                        pendingFilters[
                                            filter.key
                                        ] ??
                                        ''
                                    "
                                    :placeholder="
                                        filter.placeholder ||
                                        `Enter ${
                                            filter.label ||
                                            filter.key
                                        }`
                                    "
                                    class="h-10 w-full rounded-lg border border-slate-200 bg-white px-3 text-sm outline-none transition focus:border-[var(--dt-primary)] focus:ring-1 focus:ring-[var(--dt-primary)]"
                                    @input="
                                        updatePendingFilter(
                                            filter.key,
                                            $event.target
                                                .value
                                        )
                                    "
                                />

                                <!-- SELECT -->

                                <select
                                    v-else-if="
                                        filter.type ===
                                        'select'
                                    "
                                    :value="
                                        pendingFilters[
                                            filter.key
                                        ] ??
                                        ''
                                    "
                                    class="h-10 w-full rounded-lg border border-slate-200 bg-white px-3 text-sm outline-none focus:border-[var(--dt-primary)]"
                                    @change="
                                        updatePendingFilter(
                                            filter.key,
                                            $event.target
                                                .value
                                        )
                                    "
                                >
                                    <option value="">
                                        {{
                                            filter.placeholder ||
                                            `All ${
                                                filter.label ||
                                                filter.key
                                            }`
                                        }}
                                    </option>

                                    <option
                                        v-for="option in filter.options ||
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

                                <!-- BOOLEAN -->

                                <select
                                    v-else-if="
                                        filter.type ===
                                        'boolean'
                                    "
                                    :value="
                                        pendingFilters[
                                            filter.key
                                        ] ??
                                        ''
                                    "
                                    class="h-10 w-full rounded-lg border border-slate-200 bg-white px-3 text-sm outline-none focus:border-[var(--dt-primary)]"
                                    @change="
                                        updatePendingFilter(
                                            filter.key,
                                            $event.target
                                                .value
                                        )
                                    "
                                >
                                    <option value="">
                                        All
                                    </option>

                                    <option value="1">
                                        Yes
                                    </option>

                                    <option value="0">
                                        No
                                    </option>
                                </select>

                                <!-- NUMBER RANGE -->

                                <div
                                    v-else-if="
                                        filter.type ===
                                        'number-range'
                                    "
                                    class="grid grid-cols-2 gap-2"
                                >
                                    <input
                                        type="number"
                                        placeholder="Min"
                                        :value="
                                            pendingFilters[
                                                filter.key
                                            ]?.min ??
                                            ''
                                        "
                                        class="h-10 w-full rounded-lg border border-slate-200 px-3 text-sm outline-none focus:border-[var(--dt-primary)]"
                                        @input="
                                            updatePendingFilter(
                                                filter.key,
                                                {
                                                    ...(pendingFilters[
                                                        filter.key
                                                    ] ||
                                                        {}),
                                                    min: $event
                                                        .target
                                                        .value,
                                                }
                                            )
                                        "
                                    />

                                    <input
                                        type="number"
                                        placeholder="Max"
                                        :value="
                                            pendingFilters[
                                                filter.key
                                            ]?.max ??
                                            ''
                                        "
                                        class="h-10 w-full rounded-lg border border-slate-200 px-3 text-sm outline-none focus:border-[var(--dt-primary)]"
                                        @input="
                                            updatePendingFilter(
                                                filter.key,
                                                {
                                                    ...(pendingFilters[
                                                        filter.key
                                                    ] ||
                                                        {}),
                                                    max: $event
                                                        .target
                                                        .value,
                                                }
                                            )
                                        "
                                    />
                                </div>

                                <!-- DATE RANGE -->

                                <div
                                    v-else-if="
                                        filter.type ===
                                        'date-range'
                                    "
                                    class="grid grid-cols-2 gap-2"
                                >
                                    <input
                                        type="date"
                                        :value="
                                            pendingFilters[
                                                filter.key
                                            ]?.from ??
                                            ''
                                        "
                                        class="h-10 w-full rounded-lg border border-slate-200 px-3 text-sm outline-none focus:border-[var(--dt-primary)]"
                                        @change="
                                            updatePendingFilter(
                                                filter.key,
                                                {
                                                    ...(pendingFilters[
                                                        filter.key
                                                    ] ||
                                                        {}),
                                                    from: $event
                                                        .target
                                                        .value,
                                                }
                                            )
                                        "
                                    />

                                    <input
                                        type="date"
                                        :value="
                                            pendingFilters[
                                                filter.key
                                            ]?.to ??
                                            ''
                                        "
                                        class="h-10 w-full rounded-lg border border-slate-200 px-3 text-sm outline-none focus:border-[var(--dt-primary)]"
                                        @change="
                                            updatePendingFilter(
                                                filter.key,
                                                {
                                                    ...(pendingFilters[
                                                        filter.key
                                                    ] ||
                                                        {}),
                                                    to: $event
                                                        .target
                                                        .value,
                                                }
                                            )
                                        "
                                    />
                                </div>

                                <!-- MULTI SELECT -->

                                <select
                                    v-else-if="
                                        filter.type ===
                                        'multiselect'
                                    "
                                    multiple
                                    class="min-h-24 w-full rounded-lg border border-slate-200 bg-white px-3 py-2 text-sm outline-none focus:border-[var(--dt-primary)]"
                                    :value="
                                        pendingFilters[
                                            filter.key
                                        ] ||
                                        []
                                    "
                                    @change="
                                        updatePendingFilter(
                                            filter.key,
                                            Array.from(
                                                $event
                                                    .target
                                                    .selectedOptions
                                            ).map(
                                                (
                                                    option
                                                ) =>
                                                    option.value
                                            )
                                        )
                                    "
                                >
                                    <option
                                        v-for="option in filter.options ||
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

                                <!-- CUSTOM -->

                                <slot
                                    v-else
                                    name="filter"
                                    :filter="
                                        filter
                                    "
                                    :value="
                                        pendingFilters[
                                            filter.key
                                        ]
                                    "
                                    :update="
                                        (value) =>
                                            updatePendingFilter(
                                                filter.key,
                                                value
                                            )
                                    "
                                />
                            </div>
                        </div>
                    </div>

                    <!-- FOOTER -->

                    <div
                        class="flex items-center justify-between border-t border-slate-200 bg-slate-50 px-5 py-4"
                    >
                        <button
                            type="button"
                            class="text-sm font-medium text-red-500 hover:text-red-600"
                            @click="
                                clearFilters
                            "
                        >
                            Clear all
                        </button>

                        <div
                            class="flex gap-2"
                        >
                            <button
                                type="button"
                                class="rounded-lg border border-slate-200 bg-white px-4 py-2 text-sm font-medium text-slate-600 hover:bg-slate-100"
                                @click="
                                    showFilterModal =
                                        false
                                "
                            >
                                Cancel
                            </button>

                            <button
                                type="button"
                                class="rounded-lg px-4 py-2 text-sm font-medium text-white shadow-sm"
                                :style="{
                                    backgroundColor:
                                        primaryColor,
                                }"
                                @click="
                                    applyFilters
                                "
                            >
                                Apply Filters
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </Teleport>
    </div>
</template>

<style scoped>
.table-compact th,
.table-compact td {
    padding-top: 0.5rem;
    padding-bottom: 0.5rem;
}

input[type="search"]::-webkit-search-cancel-button {
    display: none;
}
</style>