<script setup>
import ConfirmationDeleteModal from '@/components/ConfirmationDeleteModal.vue';
import EditAction from '@/components/EditAction.vue';
import Pagination from '@/components/Pagination.vue';
import { router } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    content: {
        type: Object,
        required: true,
    },
    columns: {
        type: Array,
        default: () => [],
    },
    deleteAction: {
        type: String,
        required: true,
        default: '',
    },
    showActions: {
        type: Boolean,
        default: true,
    },
});

const data = computed(() => props.content.data);
const pagination = computed(() => props.content);

const emit = defineEmits(['edit-item']);

function editItem(item) {
    emit('edit-item', item);
}

function deleteItem(item, action) {
    router.delete(route(`${action}`, item), {
        preserveScroll: true,
        onSuccess: () => {
            if (data.value.length === 0 && pagination.value.current_page > 1) {
                const previousPage = pagination.value.current_page - 1;
                router.visit(
                    route(route().current(), {
                        data: { ...route().params, page: previousPage },
                        preserveState: true,
                        preserveScroll: true,
                    }),
                );
            }
        },
    });
}
</script>

<template>
    <div class="border-base-300 bg-base-100 hidden w-full overflow-x-auto rounded-lg border shadow-sm sm:block">
        <div class="relative h-[calc(100vh-180px)] overflow-y-auto">
            <table class="table w-full">
                <thead class="bg-base-200 sticky top-0">
                    <tr>
                        <th
                            v-for="(column, index) in columns"
                            :key="index"
                            class="px-4 py-3 text-left whitespace-nowrap"
                            :class="{
                                'min-w-[120px]': columns.length <= 4,
                                'min-w-[80px]': columns.length > 4,
                            }"
                        >
                            {{ column.label }}
                        </th>
                        <th v-if="showActions" class="w-24 px-4 py-3 text-center whitespace-nowrap">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    <tr class="hover:bg-base-300" v-for="(item, itemIndex) in data" :key="itemIndex">
                        <td
                            v-for="(column, colIndex) in columns"
                            :key="colIndex"
                            class="max-w-[300px] overflow-hidden px-4 py-3 text-ellipsis"
                            :title="item[column.field]"
                        >
                            <p v-if="item[column.field] === null" class="text-base-content/50">No disponible</p>
                            <p v-else class="truncate">
                                {{ item[column.field] }}
                            </p>
                        </td>

                        <td v-if="showActions" class="px-4 py-3 text-center">
                            <div class="flex justify-center gap-2">
                                <EditAction @click="editItem(item)" />
                                <ConfirmationDeleteModal
                                    :item="item"
                                    :action="props.deleteAction"
                                    @delete="deleteItem"
                                />
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <Pagination :pagination="pagination" />
    </div>
</template>
