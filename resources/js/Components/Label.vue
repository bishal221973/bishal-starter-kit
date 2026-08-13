<script setup>
const emit = defineEmits(["toggle-sidebar"]);
import { useTheme } from '@/composables/useTheme'

const { theme } = useTheme()
defineProps({
    opacity:{
        type:String,
        default:''
    }
})
</script>
<template>
    <label :style="{color:theme.text_color+opacity}">
        <slot/>
    </label>
</template>