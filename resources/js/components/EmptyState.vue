<script setup>
import { CircleAlert } from 'lucide-vue-next';
</script>

<template>
    <div class="flex flex-col items-center justify-center py-12 text-center">
        <CircleAlert class="mb-4 h-20 w-20 text-gray-700" />

        <h3 class="mb-1 text-lg font-medium text-gray-700">No se encontraron resultados</h3>
        <p class="mb-4 text-sm text-gray-500">Parece que no hay información para mostrar en este momento.</p>
    </div>
</template>
