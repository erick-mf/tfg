<script setup>
import { Link } from '@inertiajs/vue3';
import { CircleChevronRight } from 'lucide-vue-next';

defineProps({
    href: {
        type: String,
        required: true,
    },
    active: {
        type: Boolean,
        default: false,
    },
});
</script>

<template>
    <li>
        <Link :href="href" :class="['flex items-center', active ? 'bg-primary/10 text-primary' : 'hover:bg-base-200']">
            <CircleChevronRight :class="['h-5 w-5', active ? 'block' : 'hidden']" />
            <span class="text-base"><slot /></span>
        </Link>
    </li>
</template>
