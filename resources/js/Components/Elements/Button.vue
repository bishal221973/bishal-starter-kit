<script setup>
import { computed } from "vue";

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

const buttonClass = computed(() => {
    const variants = {
        primary: "bg-primary",
        secondary: "bg-secondary",
        accent: "bg-accent",
        success: "bg-success",
        warning: "bg-warning",
        danger: "bg-danger",
        info: "bg-info",
    };

    return variants[props.type] || variants.primary;
});
</script>

<template>
    <button
        :type="submit ? 'submit' : 'button'"
        :disabled="disabled"
        :class="['modern-btn', buttonClass]"
    >
        <span class="relative z-10">
            <slot />
        </span>
    </button>
</template>

<style scoped>
.modern-btn {
    position: relative;
    overflow: hidden;
    color: white;
    padding: 12px 24px;
    border: none;
    border-radius: 12px;
    font-weight: 600;
    cursor: pointer;
    transition: all 0.3s ease;
    transform: translateY(0);
    box-shadow: 0 6px 18px rgba(0, 0, 0, 0.15);
}

.modern-btn:hover {
    transform: translateY(-3px);
    box-shadow: 0 12px 28px rgba(0, 0, 0, 0.2);
}

.modern-btn:active {
    transform: scale(0.98);
}

.modern-btn:disabled {
    opacity: 0.6;
    cursor: not-allowed;
    transform: none;
}

/* Shine effect */
.modern-btn::before {
    content: "";
    position: absolute;
    top: 0;
    left: -120%;
    width: 50%;
    height: 100%;
    background: linear-gradient(
        90deg,
        transparent,
        rgba(255, 255, 255, 0.3),
        transparent
    );
    transform: skewX(-25deg);
    transition: 0.8s;
}

.modern-btn:hover::before {
    left: 130%;
}
</style>