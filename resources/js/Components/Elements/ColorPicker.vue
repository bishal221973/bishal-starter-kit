<script setup>
import { onMounted, useTemplateRef } from "vue";
import Label from "../Label.vue";

defineProps({
    text: String,
    required: {
        type: Boolean,
        default: false,
    },
});

const model = defineModel({
    type: String,
    default: "#3d98aa",
});

const inputRef = useTemplateRef("inputElement");

onMounted(() => {
    if (inputRef.value?.hasAttribute("autofocus")) {
        inputRef.value.focus();
    }
});

defineExpose({
    focus: () => inputRef.value?.focus(),
});
</script>

<template>
    <div class="space-y-2">
        <Label
            v-if="text"
            class="font-medium text-slate-700"
        >
            {{ text }}
            <span
                v-if="required"
                class="text-red-500 ml-1"
            >
                *
            </span>
        </Label>

        <div
            class="group relative flex items-center bg-white border border-slate-200 rounded-2xl overflow-hidden shadow-sm hover:border-primary/40 hover:shadow-md focus-within:border-primary focus-within:ring-4 focus-within:ring-primary/10 transition-all duration-300"
        >
            <!-- Color Preview -->
            <div class="pl-3">
                <label
                    class="relative block w-12 h-12 rounded-xl overflow-hidden border-2 border-white shadow cursor-pointer"
                    :style="{ backgroundColor: model }"
                >
                    <input
                        v-model="model"
                        type="color"
                        class="absolute inset-0 opacity-0 cursor-pointer"
                    />
                </label>
            </div>

            <!-- Hex Input -->
            <input
                ref="inputElement"
                v-model="model"
                type="text"
                maxlength="7"
                placeholder="#3d98aa"
                class="flex-1 border-0 opacity-0 bg-transparent py-4 px-4 text-sm font-mono text-slate-700 placeholder-slate-400 focus:ring-0 focus:outline-none"
                v-bind="$attrs"
            />

            <!-- Hex Badge -->
            <div
                class="hidden md:flex items-center mr-3 px-3 py-1.5 rounded-lg bg-slate-100 text-xs font-semibold text-slate-500 uppercase"
            >
                HEX
            </div>
        </div>

        
    </div>
</template>