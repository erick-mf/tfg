<!-- BtnPrimary.vue -->
<script setup>
defineProps({
    type: {
        type: String,
        default: 'submit',
    },
    processing: {
        type: Boolean,
        default: false,
    },
});
</script>

<template>
    <button
        :type="type"
        :disabled="processing"
        class="btn btn-primary w-full"
        :class="processing ? 'btn-disabled cursor-not-allowed' : ''"
    >
        <slot v-if="!processing" />
        <span v-else>Procesando...</span>
    </button>
</template>
