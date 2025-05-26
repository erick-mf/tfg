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
    showActions: {
        type: Boolean,
        default: true,
    },
    emptyMessage: {
        type: String,
        default: 'No hay datos disponibles',
    },
});

const data = computed(() => props.content.data);
const pagination = computed(() => props.content);

const emit = defineEmits(['edit-user']);

function editItem(item) {
    emit('edit-user', item);
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
            <table class="table-zebra table w-full">
                <thead class="bg-base-200 sticky top-0">
                    <tr>
                        <th v-for="(column, index) in columns" :key="index">{{ column.label }}</th>
                        <th v-if="showActions">Acciones</th>
                    </tr>
                </thead>

                <tbody>
                    <tr class="hover:bg-base-300" v-for="(item, itemIndex) in data" :key="itemIndex">
                        <td v-for="(column, colIndex) in columns" :key="colIndex">
                            <p v-if="item[column.field] === null">No disponible</p>
                            <p v-else>{{ item[column.field] }}</p>
                        </td>

                        <td v-if="showActions">
                            <div class="flex gap-2">
                                <EditAction @click="editItem(item)" />
                                <ConfirmationDeleteModal
                                    :item="item"
                                    :action="'admin.users.destroy'"
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
