<script setup>
defineProps({
    text: {
        type: String,
        default: 'Abrir Formulario',
    },
    drawerId: {
        type: String,
        default: 'sidebar-right',
    },
});
</script>

<template>
    <label :for="drawerId" class="btn drawer-button">
        {{ text }}
    </label>
</template>
