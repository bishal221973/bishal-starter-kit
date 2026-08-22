<script setup>
import { computed } from "vue";

const props = defineProps({
    search: {
        type: String,
        default: "",
    },

    searchable: {
        type: Boolean,
        default: true,
    },

    searchPlaceholder: {
        type: String,
        default: "Search...",
    },

    filterable: {
        type: Boolean,
        default: true,
    },

    hasActiveFilters: {
        type: Boolean,
        default: false,
    },

    selectable: {
        type: Boolean,
        default: false,
    },

    selectedCount: {
        type: Number,
        default: 0,
    },
});

const emit = defineEmits([
    "update:search",
    "clear-search",
    "clear-filters",
]);

const searchValue = computed({
    get: () => props.search,

    set: (value) => {
        emit("update:search", value);
    },
});
</script>

<template>
    <div
        class="flex flex-col gap-3 border-b border-slate-400 p-4 sm:flex-row sm:items-center sm:justify-between"
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
               

                <input
                    v-model="searchValue"
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
                    emit(
                        'clear-filters'
                    )
                "
            >
                Clear filters
            </button>

            <!-- Selected -->
            <span
                v-if="
                    selectable &&
                    selectedCount > 0
                "
                class="rounded-xl bg-primary/10 px-3 py-2 text-xs font-semibold text-primary"
            >
                {{ selectedCount }}
                selected
            </span>

            <!-- Custom toolbar -->
            <slot
                name="toolbar"
            />
        </div>

        <!-- Right -->
        <div
            class="flex items-center gap-2"
        >
            <slot
                name="toolbar-right"
            />
        </div>
    </div>
</template>