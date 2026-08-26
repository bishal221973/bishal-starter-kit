<script setup>
import DataTable from "@/Components/Datatable/Table.vue";
const props=defineProps({
  branches:Object
})
const columns = [
    {
        key: "id",
        label: "ID",
        type: "number",
        sortable: true,
    },

    {
        key: "name",
        label: "Name",
        type: "text",
        searchable: true,
        sortable: true,
    },

    {
        key: "email",
        label: "Email",
        type: "text",
        searchable: true,
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
</script>

<template>
  <!-- {{ branches }} -->
    <DataTable
    mode="server"
    :data="branches"
    :columns="columns"
    :filters="filters"
    :route="route('branches.index')"
    :searchable="true"
    :filterable="true"
    :sortable="true"
    :pagination="true"
    :selectable="true"
    :page-size="10"
    :page-size-options="[10, 25, 50, 100]"
    primary-color="#3D98AB"
    header-bg-color="#F8FAFC"
/>
</template>