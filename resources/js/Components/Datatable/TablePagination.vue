<script setup>
const props = defineProps({
  currentPage: {
    type: Number,
    required: true,
  },

  totalPages: {
    type: Number,
    required: true,
  },

  currentPageSize: {
    type: Number,
    required: true,
  },

  pageSizeOptions: {
    type: Array,
    default: () => [10, 25, 50, 100],
  },

  showingFrom: {
    type: Number,
    default: 0,
  },

  showingTo: {
    type: Number,
    default: 0,
  },

  total: {
    type: Number,
    default: 0,
  },
});

const emit = defineEmits([
  "page-change",
  "page-size-change",
]);

function changePage(page) {
  const nextPage = Number(page);

  if (!Number.isFinite(nextPage)) {
    return;
  }

  if (nextPage < 1 || nextPage > props.totalPages) {
    return;
  }

  emit("page-change", nextPage);
}

function changePageSize(size) {
  const nextSize = Number(size);

  if (!Number.isFinite(nextSize) || nextSize <= 0) {
    return;
  }

  emit("page-size-change", nextSize);
}

function paginationPages() {
  const last = props.totalPages;
  const current = props.currentPage;

  if (last <= 7) {
    return Array.from(
      {
        length: last,
      },
      (_, index) => index + 1
    );
  }

  const pages = [];

  pages.push(1);

  if (current > 4) {
    pages.push("...");
  }

  const start = Math.max(2, current - 1);
  const end = Math.min(last - 1, current + 1);

  for (let page = start; page <= end; page++) {
    pages.push(page);
  }

  if (current < last - 3) {
    pages.push("...");
  }

  pages.push(last);

  return pages;
}
</script>

<template>
  <div
    class="flex flex-col gap-3 border-t border-slate-200 px-4 py-3 sm:flex-row sm:items-center sm:justify-between"
  >
    <!-- Information -->
    <div class="flex flex-wrap items-center gap-2 text-xs text-slate-500">
      <span>
        Rows per page
      </span>

      <select
        :value="currentPageSize"
        class="rounded-lg border border-slate-200 bg-white px-2 py-1.5 text-xs outline-none focus:border-primary focus:ring-1 focus:ring-primary"
        @change="changePageSize($event.target.value)"
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
        {{ showingFrom }}–{{ showingTo }}
        of
        {{ total }}
      </span>
    </div>

    <!-- Navigation -->
    <div
      v-if="totalPages > 1"
      class="flex flex-wrap items-center gap-1"
    >
      <!-- Previous -->
      <button
        type="button"
        class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-600 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-40"
        :disabled="currentPage === 1"
        @click="changePage(currentPage - 1)"
      >
        Previous
      </button>

      <!-- Pages -->
      <template
        v-for="(page, index) in paginationPages()"
        :key="`${page}-${index}`"
      >
        <!-- Ellipsis -->
        <span
          v-if="page === '...'"
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
            currentPage === page
              ? 'bg-primary text-white'
              : 'text-slate-600 hover:bg-slate-100'
          "
          @click="changePage(page)"
        >
          {{ page }}
        </button>
      </template>

      <!-- Next -->
      <button
        type="button"
        class="rounded-lg border border-slate-200 px-3 py-1.5 text-xs font-medium text-slate-600 transition hover:bg-slate-50 disabled:cursor-not-allowed disabled:opacity-40"
        :disabled="currentPage === totalPages"
        @click="changePage(currentPage + 1)"
      >
        Next
      </button>
    </div>
  </div>
</template>