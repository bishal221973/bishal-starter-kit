<script setup>
import { computed } from "vue";
import { useTheme } from "@/composables/useTheme";

const props = defineProps({
    submit: {
        type: Boolean,
        default: false,
    },

    disabled: {
        type: Boolean,
        default: false,
    },

    type: {
        type: String,
        default: "primary", // primary, secondary, accent, text
    },

   
});

const { theme } = useTheme();

const buttonColor = computed(() => {
    switch (props.type) {
        case "secondary":
            return theme.value.secondary;

        case "accent":
            return theme.value.accent;

        case "success":
            return theme.value.success;

        case "primary":
        default:
            return theme.value.primary;
    }
});

const buttonStyle = computed(() => {
    // Text Button
    if (props.type === "text") {
        return {
            color: buttonColor.value,
            backgroundColor: "transparent",
            borderColor: "transparent",
            "--tw-ring-color": buttonColor.value,
        };
    }

    // Filled Button
    return {
        color: "#ffffff",
        backgroundColor: buttonColor.value,
        borderColor: buttonColor.value,
        "--tw-ring-color": buttonColor.value,
    };
});
</script>

<template>
    <button
        :type="submit ? 'submit' : 'button'"
        :disabled="disabled"
        class="theme-button inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg border font-medium text-sm tracking-wide transition-all duration-200 ease-out select-none focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none"
        :class="{
            'theme-button--outline': outline,
            'theme-button--text': type === 'text'
        }"
        :style="buttonStyle"
    >
        <slot />
    </button>
</template>

<style scoped>
.theme-button:hover {
    filter: brightness(0.95);
}

.theme-button:active {
    transform: scale(0.98);
}

.theme-button--outline:hover {
    opacity: 0.9;
}

.theme-button--text:hover {
    background-color: rgba(0, 0, 0, 0.04);
    filter: none;
}
</style>