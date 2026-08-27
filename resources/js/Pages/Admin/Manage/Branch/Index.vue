<script setup>
import DataTable from "@/Components/Datatable/Table.vue";

defineProps({
  branches: Object,
});

const columns = [
  {
    key: "name",
    label: "Name",
    type: "text",
    searchable: true,
    sortable: true,
    items: [
      {
        label: "Name",
        key: "name",
      },
      {
        label: "Email",
        key: "email",
      },
    ],
  },

  {
    key: "status",
    label: "Status",
    type: "badge",
    sortable: true,
  },
  {
    key: "active",
    label: "Active",
    type: "boolean",
  },
  {
    key: "created_at",
    label: "Created",
    type: "date",
    sortable: true,
  },
];

const filters = [
  {
    key: "status",
    label: "Status",
    type: "select",
    options: [
      {
        label: "Active",
        value: "1",
      },
      {
        label: "Inactive",
        value: "0",
      },
      {
        label: "Pending",
        value: "pending",
      },
    ],
  },

  {
    key: "active",
    label: "Active",
    type: "boolean",
  },

  {
    key: "name",
    label: "Name",
    type: "text",
  },

  {
    key: "created_at",
    label: "Created Date",
    type: "date-range",
    fullWidth: true,
  },

  {
    key: "id",
    label: "ID Range",
    type: "number-range",
  },
];
const tableTheme = {
  container: {
    class:
      "w-full overflow-hidden rounded-2xl border border-slate-200/80 bg-white shadow-sm ring-1 ring-slate-100",
    style: {},
  },
  toolbar: {
    class:
      "border-b border-slate-100 bg-white/80 backdrop-blur-md px-6 py-4.5 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between",
    style: {},
  },
  search: {
    wrapperClass:
      "relative w-full sm:w-[240px] focus-within:sm:w-[320px] transition-all duration-300 ease-out",
    inputClass:
      "h-10 w-full rounded-xl border border-slate-200 bg-slate-50/50 pl-10 pr-10 text-sm text-[#041124] font-medium outline-none placeholder:text-slate-400 transition-all duration-200 focus:border-[#628891] focus:bg-white focus:ring-4 focus:ring-[#628891]/10 hover:border-2 focus:border-2 hover:border-slate-300",
    iconClass:
      "text-[#628891] absolute left-3.5 top-1/2 -translate-y-1/2 transition-colors duration-200 group-focus-within:text-[#041124]",
  },
  buttons: {
    base:
      "inline-flex h-10 items-center justify-center gap-2 rounded-xl border text-sm font-semibold transition-all duration-200 active:scale-[0.98] disabled:cursor-not-allowed disabled:opacity-50 disabled:pointer-events-none",
    default:
      "border-slate-200 bg-white px-4 text-[#041124] shadow-sm hover:border-slate-300 hover:bg-slate-50",
    primary:
      "border-[#041124] bg-[#041124] px-4 text-white shadow-sm hover:bg-[#628891] hover:border-[#628891] focus:ring-4 focus:ring-[#041124]/10",
    danger:
      "border-red-100 bg-red-50/50 px-4 text-red-600 hover:bg-red-100/70 hover:border-red-200 focus:ring-4 focus:ring-red-500/10",
    filter:
      "border-slate-200 bg-white pr-4 pl-3 text-[#628891] border-[#628891]  shadow-sm hover:border-[#628891] hover:bg-[#628891] hover:text-[#f2f2f2] focus:ring-4 focus:ring-[#628891]/10 rounded-[50px]",
    export:
      "border-slate-200 bg-white px-4 text-[#041124] shadow-sm hover:border-[#628891] hover:bg-[#628891]/5",
    column:
      "border-slate-200 bg-white px-4 text-[#041124] shadow-sm hover:border-[#628891] hover:bg-[#628891]/5",
  },
  filter: {
    badge:
      "ml-1.5 inline-flex h-5 min-w-5 items-center justify-center rounded-full bg-[#628891] px-1 text-[10px] font-bold text-white ring-2 ring-white",
    modalOverlay:
      "fixed inset-0 z-50 bg-[#041124]/40 backdrop-blur-sm transition-opacity duration-300",
    modal:
      "fixed left-1/2 top-1/2 z-50 w-full max-w-lg -translate-x-1/2 -translate-y-1/2 rounded-2xl border border-slate-200/80 bg-white shadow-2xl ring-1 ring-[#041124]/5 transition-all duration-300",
    header:
      "flex items-center justify-between border-b border-slate-100 bg-white px-6 py-4.5",
    body: "bg-white px-6 py-6 space-y-4 max-h-[60vh] overflow-y-auto",
    footer:
      "flex items-center justify-end gap-3 border-t border-slate-100 bg-slate-50/50 px-6 py-4",
    input:
      "h-10 w-full rounded-xl border border-slate-200 bg-white px-3.5 text-sm text-[#041124] placeholder:text-slate-400 outline-none transition focus:border-[#628891] focus:ring-4 focus:ring-[#628891]/10 hover:border-slate-300",
    select:
      "h-10 w-full rounded-xl border border-slate-200 bg-white px-3.5 text-sm text-[#041124] outline-none transition focus:border-[#628891] focus:ring-4 focus:ring-[#628891]/10 hover:border-slate-300 cursor-pointer",
  },
  table: {
    class: "min-w-full border-collapse bg-white text-left",
    style: {},
  },
  thead: {
    class: "bg-[#628891] text-white select-none",
    style: {},
    rowClass: "border-b border-[#628891]/20",
    cellClass:
      "px-6 py-5 bg-[#628891] text-xs font-bold uppercase tracking-wider text-white/90 shadow-[inset_0_-1px_0_rgba(0,0,0,0.05)]",
  },
  tbody: {
    class: "divide-y divide-slate-100 bg-white",
    style: {},
  },
  row: {
    class:
      "group border-b border-slate-100 transition-colors duration-150 hover:bg-[#628891]/4",
    style: {},
  },
  cell: {
    class:
      "px-6 py-3 text-sm text-[#041124]/90 font-medium align-middle whitespace-nowrap",
    style: {},
  },
  selection: {
    checkboxClass:
      "h-4.5 w-4.5 cursor-pointer rounded-md border-slate-300 text-[#628891] focus:ring-[#628891]/20 transition-all duration-150 checked:bg-[#628891]",
  },
  pagination: {
    class:
      "flex flex-col gap-4 border-t border-slate-100 bg-white px-6 py-4 sm:flex-row sm:items-center sm:justify-between select-none",
    infoClass: "text-sm font-medium text-slate-500",
    selectClass:
      "h-9 rounded-lg border border-slate-200 bg-white px-2.5 text-xs font-semibold text-[#041124] outline-none transition focus:border-[#628891] focus:ring-4 focus:ring-[#628891]/10 hover:border-slate-300 cursor-pointer",
    buttonClass:
      "inline-flex h-9 min-w-9 items-center justify-center rounded-lg border border-slate-200 bg-white px-3 text-xs font-semibold text-[#041124] transition-all duration-150 hover:border-slate-300 hover:bg-slate-50 active:scale-95 disabled:cursor-not-allowed disabled:opacity-40 disabled:pointer-events-none",
    activeClass:
      "!border-[#041124] !bg-[#041124] !text-white hover:!bg-[#628891] hover:!border-[#628891] shadow-sm",
    disabledClass: "cursor-not-allowed opacity-40",
  },
  export: {
    button:
      "border-slate-200 bg-white px-3 text-[#041124] hover:border-[#628891] hover:bg-[#628891]/5",
    scope: "rounded-xl border border-slate-200/60 bg-slate-50/50 p-2",
    scopeItemHoverBackground: "#62889130",
    disabledColor: "#999",
  },
  columnManager: {
    button:
      "border-slate-200 bg-white px-3 text-sm font-medium text-[#041124] shadow-sm hover:border-[#628891] hover:bg-[#628891]/5",
    menu: "",
    menuHeader: "",
    title: "",
    resetButton: "",
    list: "",
    item: "",
    itemActive: "",
    label: "",
    checkbox: "",
  },
  loading: {
    spinner:
      "h-6 w-6 animate-spin rounded-full border-2 border-slate-100 border-t-[#628891]",
    text: "text-sm font-medium text-slate-400 tracking-wide",
  },
  empty: {
    icon:
      "flex h-14 w-14 items-center justify-center rounded-2xl bg-[#628891]/8 text-[#628891]",
    iconClass: "text-[#628891] h-6 w-6",
    text: "text-sm font-medium text-slate-400 mt-2",
    action:
      "rounded-xl bg-[#041124] px-4 py-2 text-sm font-semibold text-white shadow-sm transition-all duration-200 hover:bg-[#628891] active:scale-[0.98]",
  },
  pagination: {
    class:
      "flex flex-col gap-4 border-t border-slate-200 bg-white px-5 py-4 sm:flex-row sm:items-center sm:justify-between",

    infoClass: "text-sm text-slate-500",

    selectClass:
      "h-9 rounded-lg border border-slate-200 bg-white px-3 pr-5 text-sm text-[#628891] outline-none transition focus:border-[#628891] focus:ring-2 focus:ring-[#628891]/20",

    buttonClass:
      "inline-flex h-9 min-w-9 items-center justify-center rounded-lg border border-slate-200 bg-white px-3 text-sm font-medium text-[#041124] transition hover:border-[#628891] hover:bg-[#628891]/10 disabled:cursor-not-allowed disabled:opacity-40",

    activeClass:
      "!border-[#628891] !bg-[#628891] !text-white hover:!border-[#628891] hover:!bg-[#628891]",

    disabledClass: "cursor-not-allowed opacity-40",
  },
};

const tableTheme1 = {
  /*
  |--------------------------------------------------------------------------
  | Container
  |--------------------------------------------------------------------------
  */
  container: {
    class:
      "w-full overflow-hidden rounded-2xl border border-slate-200 bg-white shadow-sm",
    style: {},
  },

  /*
  |--------------------------------------------------------------------------
  | Toolbar
  |--------------------------------------------------------------------------
  */
  toolbar: {
    class: "border-b border-slate-200 bg-white px-5 py-4",
    style: {},
  },

  /*
  |--------------------------------------------------------------------------
  | Search
  |--------------------------------------------------------------------------
  */
  search: {
    wrapperClass: "w-full sm:w-[320px]",

    inputClass:
      "h-10 w-full rounded-xl border border-slate-200 bg-slate-50 pl-10 pr-10 text-sm text-[#041124] outline-none placeholder:text-slate-400 transition focus:border-[#628891] focus:bg-white focus:ring-2 focus:ring-[#628891]/10",

    iconClass: "text-[#628891]",
  },

  /*
  |--------------------------------------------------------------------------
  | Buttons
  |--------------------------------------------------------------------------
  */
  buttons: {
    base:
      "inline-flex h-10 items-center justify-center gap-2 rounded-xl border text-sm font-medium transition-all duration-200 disabled:cursor-not-allowed disabled:opacity-50",

    default:
      "border-slate-200 bg-white px-3 text-[#041124] shadow-sm hover:border-[#628891] hover:bg-slate-50",

    primary:
      "border-[#041124] bg-[#041124] px-4 text-white shadow-sm hover:bg-[#628891] hover:border-[#628891]",

    danger: "border-red-200 bg-red-50 px-3 text-red-600 hover:bg-red-100",

    filter:
      "border-slate-200 bg-white px-3 text-[#041124] shadow-sm hover:border-[#628891] hover:bg-[#628891]/5",

    export:
      "border-[#628891]/30 bg-white px-3 text-[#041124] shadow-sm hover:border-[#628891] hover:bg-[#628891]/10",

    column:
      "border-slate-200 bg-white px-3 text-[#041124] shadow-sm hover:border-[#628891] hover:bg-slate-50",
  },

  /*
  |--------------------------------------------------------------------------
  | Filter
  |--------------------------------------------------------------------------
  */
  filter: {
    badge:
      "ml-1 inline-flex min-w-5 items-center justify-center rounded-full bg-[#628891] px-1.5 py-0.5 text-[10px] font-bold text-white",

    modalOverlay: "bg-[#041124]/60 backdrop-blur-sm",

    modal: "border border-slate-200 bg-white shadow-2xl",

    header:
      "flex items-center justify-between border-b border-slate-200 bg-white px-6 py-4",

    body: "bg-white px-6 py-5",

    footer:
      "flex items-center justify-between border-t border-slate-200 bg-slate-50 px-6 py-4",

    input:
      "h-10 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-[#041124] outline-none transition focus:border-[#628891] focus:ring-2 focus:ring-[#628891]/10",

    select:
      "h-10 w-full rounded-xl border border-slate-200 bg-white px-3 text-sm text-[#041124] outline-none transition focus:border-[#628891] focus:ring-2 focus:ring-[#628891]/10",
  },

  /*
  |--------------------------------------------------------------------------
  | Table
  |--------------------------------------------------------------------------
  */
  table: {
    class: "min-w-full border-collapse bg-white",
    style: {},
  },

  /*
  |--------------------------------------------------------------------------
  | Table Head
  |--------------------------------------------------------------------------
  */
  thead: {
    class: "bg-[#628891] text-white",

    style: {},

    rowClass: "border-b border-[#628891]/30",

    cellClass:
      "px-5 py-6 sticky top-0 text-left text-xs font-semibold uppercase tracking-wider text-white",
  },

  /*
  |--------------------------------------------------------------------------
  | Table Body
  |--------------------------------------------------------------------------
  */
  tbody: {
    class: "divide-y divide-slate-100 bg-white",

    style: {},
  },

  /*
  |--------------------------------------------------------------------------
  | Row
  |--------------------------------------------------------------------------
  */
  row: {
    class:
      "border-b border-slate-100 transition-colors duration-150 hover:bg-[#628891]/5",

    style: {},
  },

  /*
  |--------------------------------------------------------------------------
  | Cell
  |--------------------------------------------------------------------------
  */
  cell: {
    class: "px-5 py-4 text-sm text-[#041124] align-middle",

    style: {},
  },

  /*
  |--------------------------------------------------------------------------
  | Selection
  |--------------------------------------------------------------------------
  */
  selection: {
    checkboxClass:
      "h-4 w-4 cursor-pointer rounded border-slate-300 text-[#628891] accent-[#628891] focus:ring-[#628891]/20",
  },

  /*
  |--------------------------------------------------------------------------
  | Pagination
  |--------------------------------------------------------------------------
  */
  pagination: {
    class:
      "flex flex-col gap-4 border-t border-slate-200 bg-white px-5 py-4 sm:flex-row sm:items-center sm:justify-between",

    infoClass: "text-sm text-slate-500",

    selectClass:
      "h-9 rounded-lg border border-slate-200 bg-white px-3 text-xs text-[#041124] outline-none transition focus:border-[#628891] focus:ring-2 focus:ring-[#628891]/10",

    buttonClass:
      "inline-flex h-9 min-w-9 items-center justify-center rounded-lg border border-slate-200 bg-white px-3 text-xs font-medium text-[#041124] transition hover:border-[#628891] hover:bg-[#628891]/10 disabled:cursor-not-allowed disabled:opacity-40",

    activeClass:
      "!border-[#041124] !bg-[#041124] !text-white hover:!bg-[#628891] hover:!border-[#628891]",

    disabledClass: "cursor-not-allowed opacity-40",
  },

  /*
  |--------------------------------------------------------------------------
  | Export
  |--------------------------------------------------------------------------
  */
  export: {
    button: "border-[#628891]/30 bg-white px-3 text-[#041124] hover:bg-[#628891]/10",

    menu: "rounded-xl border border-slate-200 bg-white p-2 shadow-xl",

    item:
      "flex w-full items-center rounded-lg px-3 py-2 text-left text-sm font-medium text-[#041124] transition hover:bg-[#628891]/10 hover:text-[#041124]",

    scope: "rounded-xl border border-slate-200 bg-slate-50 p-2",

    /*
     * These are used directly by your template through theme.export.*
     */
    menuBackground: "#ffffff",

    menuBorderColor: "#dbe4e7",

    itemColor: "#041124",

    itemHoverBackground: "#628891",

    scopeBackground: "#ffffff",

    scopeBorderColor: "#dbe4e7",

    scopeItemColor: "#041124",

    scopeItemHoverBackground: "#628891",

    scopeItemPadding: "9px 10px",

    radioColor: "#628891",

    selectedCountColor: "#628891",

    disabledColor: "#94a3b8",
  },

  /*
  |--------------------------------------------------------------------------
  | Column Manager
  |--------------------------------------------------------------------------
  */
  columnManager: {
    button:
      "border-slate-200 bg-white px-3 text-sm font-medium text-[#041124] shadow-sm hover:border-[#628891] hover:bg-[#628891]/10",

    menu: "w-64 rounded-xl border border-slate-200 bg-white p-3 shadow-xl",

    menuHeader: "mb-2 flex items-center justify-between border-b border-slate-100 pb-2",

    title: "text-sm font-semibold text-[#041124]",

    resetButton: "text-xs font-medium text-[#628891] transition hover:text-[#041124]",

    list: "max-h-64 space-y-1 overflow-y-auto",

    item:
      "flex cursor-pointer items-center gap-2 rounded-lg px-2 py-2 text-sm transition hover:bg-[#628891]/10",

    itemActive: "bg-[#628891]/10",

    label: "text-sm text-[#041124]",

    checkbox:
      "h-4 w-4 cursor-pointer rounded border-slate-300 accent-[#628891] focus:ring-[#628891]/20",
  },

  /*
  |--------------------------------------------------------------------------
  | Loading
  |--------------------------------------------------------------------------
  */
  loading: {
    spinner:
      "h-7 w-7 animate-spin rounded-full border-2 border-slate-200 border-t-[#628891]",

    text: "text-sm text-slate-500",
  },

  /*
  |--------------------------------------------------------------------------
  | Empty
  |--------------------------------------------------------------------------
  */
  empty: {
    icon: "flex h-12 w-12 items-center justify-center rounded-xl bg-[#628891]/10",

    iconClass: "text-[#628891]",

    text: "text-sm text-slate-500",

    action:
      "rounded-xl bg-[#041124] px-4 py-2 text-sm font-medium text-white transition hover:bg-[#628891]",
  },
};
</script>

<template>
  <DataTable
    :data="branches"
    :columns="columns"
    :filters="filters"
    route="/branches"
    :export-route="route('test')"
    searchable
    filterable
    sortable
    pagination
    selectable
    :page-size="10"
    :page-size-options="[10, 25, 50, 100]"
    :theme="tableTheme"
  >
    <template #cell-name="{ row }">
      <div class="space-y-1">
        <div>
          <div class="font-medium text-slate-700">
            {{ row.name }}
          </div>
        </div>

        <div>
          <div class="text-sm text-slate-500">
            {{ row.email }}
          </div>
        </div>
      </div>
    </template>
  </DataTable>
</template>

<style scoped>
#birta-table-head {
  display: none !important;
}
</style>
