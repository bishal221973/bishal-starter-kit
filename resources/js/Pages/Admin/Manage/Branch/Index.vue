<script setup>
import DataTable from "@/Components/Datatable/Table.vue";

defineProps({
    branches: Object,
});

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
</script>

<template>
    <DataTable
        mode="server"
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

        primary-color="#3D98AB"
        header-bg-color="#F8FAFC"
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