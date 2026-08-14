<script setup>
import { computed, ref } from "vue";
import Label from "../Label.vue";

const props = defineProps({
  text: String,
  required: {
    type: Boolean,
    default: false,
  },
  options: {
    type: Array,
    default: () => [],
  },
  placeholder: {
    type: String,
    default: "Select an option",
  },
});

const model = defineModel({
  type: [String, Number],
  default: "",
});

const open = ref(false);
const search = ref("");

const filteredOptions = computed(() => {
  if (!search.value) return props.options;

  return props.options.filter((item) =>
    item.label.toLowerCase().includes(search.value.toLowerCase())
  );
});

const selectedLabel = computed(() => {
  return (
    props.options.find((item) => item.value === model.value)?.label ||
    props.placeholder
  );
});

const selectOption = (option) => {
  model.value = option.value;
  search.value = "";
  open.value = false;
};
</script>

<template>
  <div class="relative w-full">
    <Label v-if="text" class="font-medium text-slate-700 mb-[7px] block">
      {{ text }}
      <span v-if="required" class="text-red-500 ml-1">*</span>
    </Label>

    <!-- Trigger -->
    <div
      @click="open = !open"
      class="w-full px-4 py-3 bg-white border border-primary rounded-xl cursor-pointer flex items-center justify-between"
    >
      <span
        :class="
          model
            ? 'text-slate-900'
            : 'text-slate-400'
        "
      >
        {{ selectedLabel }}
      </span>

      <i
        class="fa-solid fa-chevron-down transition"
        :class="{ 'rotate-180': open }"
      />
    </div>

    <!-- Dropdown -->
    <div
      v-if="open"
      class="absolute z-50 mt-2 w-full bg-white border rounded-xl shadow-lg overflow-hidden"
    >
      <!-- Search -->
      <div class="p-3 border-b">
        <input
          v-model="search"
          type="text"
          placeholder="Search..."
          class="w-full px-3 py-2 border rounded-lg outline-none"
        />
      </div>

      <!-- Options -->
      <div class="max-h-60 overflow-y-auto">
        <div
          v-for="option in filteredOptions"
          :key="option.value"
          @click="selectOption(option)"
          class="px-4 py-3 cursor-pointer hover:bg-slate-100 transition"
        >
          {{ option.label }}
        </div>

        <div
          v-if="filteredOptions.length === 0"
          class="px-4 py-3 text-slate-500"
        >
          No results found
        </div>
      </div>
    </div>
  </div>
</template>