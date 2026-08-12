<script setup>
import { computed, ref } from "vue";
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
        default: "primary",
    },
});

const { theme } = useTheme();

const isHovered = ref(false);

const buttonColor = computed(() => {
    switch (props.type) {
        case "secondary":
            return theme.value.secondary;

        case "accent":
            return theme.value.accent;

        case "success":
            // alert(theme.value.success_color)
            // console.log(theme.value?.success)
            return theme.value.success;

        default:
            return theme.value.primary;
    }
});

const buttonStyle = computed(() => {
    // Text Button
    if (props.type === "text") {
        return {
            color: buttonColor.value,
            backgroundColor: isHovered.value
                ? `${buttonColor.value}15`
                : "transparent",
            borderColor: "transparent",
        };
    }

    // Outline Button
    return {
        color: isHovered.value ? "#ffffff" : buttonColor.value,
        backgroundColor: isHovered.value
            ? buttonColor.value
            : "#ffffff",
        borderColor: buttonColor.value,
    };
});
</script>

<template>
    <button
        :type="submit ? 'submit' : 'button'"
        :disabled="disabled"
        @mouseenter="isHovered = true"
        @mouseleave="isHovered = false"
        class="inline-flex items-center justify-center gap-2 px-5 py-2.5 rounded-lg border font-medium text-sm tracking-wide transition-all duration-200 ease-out select-none focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-offset-2 disabled:opacity-50 disabled:pointer-events-none active:scale-95"
        :style="buttonStyle"
    >
        <slot />
    </button>
</template>