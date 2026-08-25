<script setup>
import { computed } from "vue";
import Label from "@/Components/Label.vue";

const props = defineProps({
    user: {
        type: Object,
        required: true,
    },

    tabs: {
        type: Array,
        default: () => [],
    },

    activeTab: {
        type: String,
        default: "personal",
    },

    profilePhoto: {
        type: String,
        default: null,
    },

    employee: {
        type: Object,
        default: null,
    },
});

const emit = defineEmits([
    "update:activeTab",
    "photo-change",
]);

const initials = computed(() => {
    const name = props.user?.name || "User";

    return name
        .split(" ")
        .filter(Boolean)
        .slice(0, 2)
        .map((word) => word.charAt(0).toUpperCase())
        .join("");
});

const goToTab = (tab) => {
    emit("update:activeTab", tab);
};

const handlePhotoChange = (event) => {
    emit("photo-change", event);
};
</script>

<template>
    <aside class="min-w-[300px] ">
        <!-- PROFILE MENU -->
        <div
            class="rounded-2xl bg-white p-3 shadow-sm"
        >
            <!-- Avatar -->
            <div class="relative mx-auto h-36 w-[150px]">
                <div
                    class="flex h-36 w-full items-center justify-center overflow-hidden rounded-2xl border-4 border-white bg-slate-100 shadow-lg"
                >
                    <img
                        v-if="profilePhoto"
                        :src="profilePhoto"
                        :alt="user.name"
                        class="h-full w-full object-cover"
                    />

                    <span
                        v-else
                        class="text-3xl font-bold text-slate-600"
                    >
                        {{ initials }}
                    </span>
                </div>

                <!-- Change photo -->
                <label
                    class="absolute -bottom-2 -right-2 flex h-9 w-9 cursor-pointer items-center justify-center rounded-full border-2 border-white bg-slate-900 text-white shadow-md transition hover:bg-slate-700"
                >
                    <i class="fa-solid fa-camera text-xs"></i>

                    <input
                        type="file"
                        accept="image/*"
                        class="hidden"
                        @change="handlePhotoChange"
                    />
                </label>
            </div>

            <!-- User info -->
            <div class="mt-5 text-center">
                <Label class="block text-lg font-bold">
                    {{ user?.name }}
                </Label>

                <Label
                    inherit
                    class="mt-0.5 block text-sm text-slate-500"
                >
                    {{ user?.email }}
                </Label>

                <div
                    v-if="employee?.designation"
                    class="mt-2 text-xs text-slate-400"
                >
                    {{ employee.designation }}
                </div>
            </div>

            <hr class="mt-5">

            <!-- Navigation -->
            <nav class="mt-5 space-y-1">
                <button
                    v-for="tab in tabs"
                    :key="tab.key"
                    type="button"
                    class="group flex w-full items-center gap-3 rounded-xl px-3 py-2.5 text-left text-sm font-medium transition"
                    :class="
                        activeTab === tab.key
                            ? 'bg-primary text-white shadow-sm'
                            : 'text-slate-600 hover:bg-slate-50 hover:text-slate-900'
                    "
                    @click="goToTab(tab.key)"
                >
                    <span
                        class="flex h-9 w-9 shrink-0 items-center justify-center rounded-lg"
                        :class="
                            activeTab === tab.key
                                ? 'bg-white/10 text-white'
                                : 'bg-slate-100 text-slate-500 group-hover:bg-slate-200'
                        "
                    >
                        <i :class="tab.icon"></i>
                    </span>

                    <span>{{ tab.label }}</span>

                    <i
                        class="fa-solid fa-chevron-right ml-auto text-[10px]"
                        :class="
                            activeTab === tab.key
                                ? 'text-white/60'
                                : 'text-slate-300'
                        "
                    ></i>
                </button>
            </nav>
        </div>
    </aside>
</template>