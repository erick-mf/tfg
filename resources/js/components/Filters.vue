<script setup></script>
<template>
    <div class="dropdown dropdown-end hidden sm:block">
        <label tabindex="0" class="btn btn-ghost text-sm">
            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    stroke-width="2"
                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"
                />
            </svg>
            Filtros
        </label>
        <ul tabindex="0" class="menu menu-sm dropdown-content bg-base-100 rounded-box z-[1] mt-2 w-52 p-2 shadow">
            <li><a>Por nombre (A-Z)</a></li>
            <li><a>Por nombre (Z-A)</a></li>
            <li><a>Recientes primero</a></li>
            <li><a>Antiguos primero</a></li>
        </ul>
    </div>
</template>
