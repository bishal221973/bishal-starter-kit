<script setup>
// import DataTable from "@/Components/Table.vue";
import DataTable from "@/Components/Datatable/Table.vue";
import AppLayout from "@/Layouts/AppLayout.vue";

const props = defineProps({
  branches: {
    type: Object,
    default: () => ({
      data: [],
    }),
  },
});

const columns = [
  {
    key: "name",
    label: "Name",
    sortable: true,
    searchable: true,
  },

  {
    key: "slug",
    label: "Slug",
    sortable: true,
    searchable: true,
  },

  {
    key: "email",
    label: "Email",
    sortable: true,
    searchable: true,
  },

  {
    key: "phone",
    label: "Phone",
    sortable: true,
    searchable: true,
  },

  {
    key: "status",
    label: "Status",
    sortable: true,

    filterable: true,

    filterType: "select",

    filterOptions: [
      {
        label: "Active",
        value: "active",
      },
      {
        label: "Inactive",
        value: "inactive",
      },
    ],
  },

  {
    key: "created_at",
    label: "Created",
    sortable: true,
  },
];
</script>

<template>
  <AppLayout>
    <DataTable
      mode="server"
      :data="branches"
      :columns="columns"
      :route="route('branches.index')"
      :searchable="true"
      :sortable="true"
      :filterable="true"
      :pagination="true"
      :page-size="10"
    >
      <!-- Custom cell -->
      <template #cell-status="{ value }">
        <span
          class="rounded-full px-2.5 py-1 text-xs font-medium"
          :class="
            value === 'active' ? 'bg-green-100 text-green-700' : 'bg-red-100 text-red-700'
          "
        >
          {{ value === "active" ? "Active" : "Inactive" }}
        </span>
      </template>

      <!-- Actions -->
      <template #actions="{ row }">
        <div class="flex justify-end gap-2">
          <button
            type="button"
            class="text-xs font-medium text-primary hover:underline"
            @click="console.log(row)"
          >
            Edit
          </button>

          <button type="button" class="text-xs font-medium text-red-600 hover:underline">
            Delete
          </button>
        </div>
      </template>
    </DataTable>
  </AppLayout>
</template>
