<script setup>
defineProps({
  rows: {
    type: Array,
    default: () => [],
  },

  columns: {
    type: Array,
    default: () => [],
  },

  loading: {
    type: Boolean,
    default: false,
  },

  selectable: {
    type: Boolean,
    default: false,
  },

  rowKey: {
    type: String,
    default: "id",
  },

  selectedRows: {
    type: Array,
    default: () => [],
  },

  emptyText: {
    type: String,
    default: "No records found.",
  },

  search: {
    type: String,
    default: "",
  },

  actions: {
    type: Boolean,
    default: false,
  },
});

const emit = defineEmits([
  "row-click",
  "toggle-row",
  "clear-search",
]);

function getValue(row, key) {
  if (!row || !key) {
    return null;
  }

  return String(key)
    .split(".")
    .reduce((value, part) => value?.[part], row);
}

function isSelected(row, rowKey, selectedRows) {
  if (!row) {
    return false;
  }

  return selectedRows.includes(row[rowKey]);
}

function colspan(columns, selectable, actions) {
  return (
    columns.length +
    (selectable ? 1 : 0) +
    (actions ? 1 : 0)
  );
}
</script>

<template>
  <!-- Loading -->
  <tbody v-if="loading">
    <tr>
      <td
        :colspan="colspan(columns, selectable, actions)"
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

  <!-- Rows -->
  <tbody
    v-else-if="rows.length"
    class="divide-y divide-slate-100"
  >
    <tr
      v-for="row in rows"
      :key="row?.[rowKey]"
      class="transition hover:bg-slate-50"
      @click="emit('row-click', row)"
    >
      <!-- Selection -->
      <td
        v-if="selectable"
        class="px-4 py-3"
        @click.stop
      >
        <input
          type="checkbox"
          :checked="isSelected(row, rowKey, selectedRows)"
          class="rounded border-slate-300 text-primary focus:ring-primary"
          @change="emit('toggle-row', row)"
        />
      </td>

      <!-- Columns -->
      <td
        v-for="column in columns"
        :key="column.key"
        class="px-4 py-3 text-slate-700"
      >
        <slot
          :name="`cell-${column.key}`"
          :row="row"
          :value="getValue(row, column.key)"
        >
          {{ getValue(row, column.key) ?? "—" }}
        </slot>
      </td>

      <!-- Actions -->
      <td
        v-if="actions"
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

  <!-- Empty -->
  <tbody v-else>
    <tr>
      <td
        :colspan="colspan(columns, selectable, actions)"
        class="px-4 py-16 text-center"
      >
        <div class="flex flex-col items-center justify-center">
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

          <p class="text-sm font-medium text-slate-600">
            {{ emptyText }}
          </p>

          <button
            v-if="search"
            type="button"
            class="mt-2 text-xs font-semibold text-primary hover:underline"
            @click.stop="emit('clear-search')"
          >
            Clear search
          </button>
        </div>
      </td>
    </tr>
  </tbody>
</template>