<script setup>
import { Link } from '@inertiajs/vue3';
import { computed } from 'vue';

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

const limitedLinks = computed(() => {
    if (!props.pagination?.links) return [];

    const links = props.pagination.links;

    if (links.length <= 7) {
        return links;
    }

    const prevButton = links[0];
    const nextButton = links[links.length - 1];
    const pageLinks = links.slice(1, -1);

    const currentIndex = pageLinks.findIndex((link) => link.active);
    const start = Math.max(0, currentIndex - 2);
    const end = Math.min(pageLinks.length, currentIndex + 3);

    const visiblePages = pageLinks.slice(start, end);

    return [prevButton, ...visiblePages, nextButton];
});
</script>

<template>
    <div class="bg-base-200 mt-4 px-4 py-2">
        <div class="flex flex-col items-center justify-between gap-3 text-sm sm:flex-row">
            <div class="order-2 sm:order-1">
                Mostrando <span class="text-sm font-medium sm:text-base">{{ props.pagination.data.length }}</span> de
                <span class="font-medium">{{ props.pagination.total }}</span> registros
            </div>

            <div
                v-if="props.pagination && props.pagination.links && props.pagination.links.length > 3"
                class="join order-1 sm:order-2"
            >
                <template v-for="(link, index) in limitedLinks" :key="index">
                    <button
                        v-if="!link.url"
                        class="join-item btn btn-disabled btn-sm sm:btn-md"
                        v-html="getButtonLabel(link.label)"
                        disabled
                    ></button>
                    <Link
                        v-else
                        :href="link.url"
                        preserve-scroll
                        class="join-item btn btn-sm sm:btn-md"
                        :class="{ 'btn-active': link.active }"
                    >
                        {{ getButtonLabel(link.label) }}
                    </Link>
                </template>
            </div>
        </div>
    </div>
</template>
