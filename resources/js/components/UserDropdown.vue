<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="dropdown dropdown-top">
        <label tabindex="0" class="avatar ml-2 flex cursor-pointer flex-row items-center gap-2">
            <div class="btn btn-circle avatar avatar-placeholder bg-white">
                <div class="w-10 rounded-full">
                    <span class="text-xl font-bold">{{ $page.props.auth.user?.name?.charAt(0) || '?' }}</span>
                </div>
            </div>
            <p class="text-base font-semibold">{{ $page.props.auth.user?.name || 'Anónimo' }}</p>
        </label>
        <ul tabindex="0" class="menu menu-md dropdown-content bg-base-100 rounded-box z-[1] mt-3 w-52 p-2 shadow">
            <li>
                <Link method="post" as="button" :href="route('logout')">Cerrar sesión</Link>
            </li>
        </ul>
    </div>
</template>
