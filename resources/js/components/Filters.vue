<script setup>
import { ListFilter } from 'lucide-vue-next';
</script>
<template>
    <div class="dropdown dropdown-end hidden sm:block">
        <label tabindex="0" class="btn btn-ghost text-sm">
            <ListFilter class="h-5 w-5" />
            Filtros
        </label>
        <ul tabindex="0" class="menu menu-sm dropdown-content bg-base-100 rounded-box mt-2 w-52 p-2 shadow">
            <li><a>Por nombre (A-Z)</a></li>
            <li><a>Por nombre (Z-A)</a></li>
            <li><a>Recientes primero</a></li>
            <li><a>Antiguos primero</a></li>
        </ul>
    </div>
</template>
