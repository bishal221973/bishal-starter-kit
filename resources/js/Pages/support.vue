<script setup>
import { useForm } from '@inertiajs/vue3';

const form = useForm({
    message: '',
});

const submit = () => {
    form.post(route('support.chat'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset('message');
        },
    });
};
</script>

<template>
    <form @submit.prevent="submit">
        <input
            v-model="form.message"
            type="text"
            placeholder="Ask something about the starter kit..."
            :disabled="form.processing"
        />

        <button
            type="submit"
            :disabled="form.processing"
        >
            {{ form.processing ? 'Sending...' : 'Send' }}
        </button>

        <div v-if="form.errors.message">
            {{ form.errors.message }}
        </div>
    </form>
</template>