<script setup>
import AppLayout from "@/Layouts/AppLayout.vue";
import { ref } from "vue";
import { useTheme } from "@/composables/useTheme.js";
import PageHeader from "@/Components/PageHeader.vue";
import Table from "@/Components/Datatable/Table.vue";
const props = defineProps({
  config: Object,
  mailConfig: Object,
  branches: Object,
  branches1: Object,
});

const selectedUsers = ref([]);

const { theme } = useTheme();

const columns = [
  {
    key: "name",
    label: "Name",
    sortable: true,
  },
  {
    key: "email",
    label: "Email",
    sortable: true,
  },
  {
    key: "role",
    label: "Role",
    sortable: true,
  },
  {
    key: "created_at",
    label: "Created",
    sortable: true,
  },
];

const users = [
  {
    id: 1,
    name: "Bishal",
    email: "bishal@example.com",
    role: "Admin",
    created_at: "2026-08-22",
  },
];
// console.log(RisingSelect)
</script>
<template>
  <AppLayout>
    <div class="mx-auto">
      <PageHeader
        description="Manage your application's settings and customization options."
        :breadcrumbs="[
          {
            label: 'Branch',
            href: route('branches.index'),
          },
          {
            label: 'Manage',
          },
        ]"
      />

      <div>
        <Table
          mode="server"
          :data="branches1"
          :columns="columns"
          :route="route('branches.index')"
          :pagination="true"
          :searchable="true"
          :sortable="true"
          :page-size="10"
          :loading="false"
        >
          <template #cell-name="{ row }">
            <span class="font-medium">
              {{ row.name }}
            </span>
          </template>

          <template #actions="{ row }">
            <Link
              :href="route('branches.edit', row.id)"
              class="text-primary hover:underline"
            >
              Edit
            </Link>
          </template>
        </Table>
        <Table
          :columns="columns"
          :data="branches"
          mode="client"
          selectable
          searchable
          pagination
          :page-size="10"
        >
          <template #cell-name="{ row }">
            <div class="font-medium text-slate-900">
              {{ row.name }}
            </div>
          </template>

          <template #cell-role="{ row }">
            <span
              class="rounded-full bg-primary/10 px-2.5 py-1 text-xs font-medium text-primary"
            >
              123
            </span>
          </template>

          <template #actions="{ row }">
            <div class="flex justify-end gap-2">
              <button
                class="text-xs font-medium text-primary hover:underline"
                @click="editUser(row)"
              >
                Edit
              </button>

              <button
                class="text-xs font-medium text-red-500 hover:underline"
                @click="deleteUser(row)"
              >
                Delete
              </button>
            </div>
          </template>

          <template #toolbar>
            <!-- <button
              class="rounded-xl bg-primary px-4 py-2 text-xs font-semibold text-white"
            >
              Add User
            </button> -->
          </template>
        </Table>
      </div>
    </div>
  </AppLayout>
</template>
