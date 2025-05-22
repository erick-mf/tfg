<script setup>
import { Link } from '@inertiajs/vue3';

defineProps({
    href: {
        type: String,
        required: true,
    },
    active: {
        type: Boolean,
        default: false,
    },
    icon: {
        type: String,
        required: true,
    },
});
</script>

<template>
    <li>
        <Link :href="href" :class="['flex items-center', active ? 'bg-primary/10 text-primary' : 'hover:bg-base-200']">
            <svg
                xmlns="http://www.w3.org/2000/svg"
                class="h-5 w-5"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                :class="{
                    'stroke-primary': active,
                    'stroke-current': !active,
                }"
            >
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" :d="icon" />
            </svg>
            <span class="ml-2 text-base"><slot /></span>
        </Link>
    </li>
</template>
