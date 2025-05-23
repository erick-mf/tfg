<script setup>
import { Link } from '@inertiajs/vue3';

const props = defineProps({
    pagination: {
        type: Object,
        required: true,
    },
});

function getButtonLabel(label) {
    if (label.includes('« Previous') || label.toLowerCase().includes('previous')) {
        return '«';
    }
    if (label.includes('Next »') || label.toLowerCase().includes('next')) {
        return '»';
    }
    return label;
}
</script>

<template>
    <div v-if="pagination && pagination.links && pagination.links.length > 3" class="join">
        <template v-for="(link, index) in pagination.links" :key="index">
            <button
                v-if="!link.url"
                class="join-item btn btn-disabled btn-md"
                v-html="getButtonLabel(link.label)"
                disabled
            ></button>

            <Link
                v-else
                :href="link.url"
                preserve-scroll
                class="join-item btn btn-md"
                :class="{ 'btn-active': link.active }"
                v-html="getButtonLabel(link.label)"
            ></Link>
        </template>
    </div>
</template>
