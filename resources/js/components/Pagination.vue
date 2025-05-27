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
    <div class="bg-base-200 px-4 py-2">
        <div class="flex items-center justify-between text-sm">
            <div>
                Mostrando <span class="text-sm font-medium sm:text-base">{{ props.pagination.data.length }}</span> de
                <span class="font-medium">{{ props.pagination.total }}</span> registros
            </div>

            <div v-if="props.pagination && props.pagination.links && props.pagination.links.length > 3" class="join">
                <template v-for="(link, index) in props.pagination.links" :key="index">
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
