<script setup>
import ConfirmationDeleteModal from '@/components/ConfirmationDeleteModal.vue';
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
const hasContent = computed(() => data.value.length > 0);

const emit = defineEmits(['edit-user']);

function editItem(item) {
    emit('edit-user', item);
}

function deleteItem(item, action) {
    router.delete(route(`${action}`, item), {
        preserveScroll: true,
    });
}
</script>

<template>
    <div class="border-base-300 bg-base-100 w-full overflow-x-auto rounded-lg border shadow-sm">
        <div class="relative h-[calc(100vh-180px)] overflow-y-auto">
            <table class="table-zebra table w-full">
                <thead class="bg-base-200 sticky top-0 z-10">
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
                                <button class="btn btn-sm btn-ghost text-primary" @click="editItem(item)">
                                    <svg
                                        xmlns="http://www.w3.org/2000/svg"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke-width="1.5"
                                        stroke="currentColor"
                                        class="h-5 w-5"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10"
                                        />
                                    </svg>
                                </button>
                                <ConfirmationDeleteModal
                                    :item="item"
                                    :action="'admin.users.destroy'"
                                    @delete="deleteItem"
                                />
                            </div>
                        </td>
                    </tr>

                    <tr v-if="!hasContent">
                        <td :colspan="columns.length + (showActions ? 1 : 0)" class="py-8 text-center">
                            <div class="flex flex-col items-center justify-center gap-2">
                                <svg
                                    xmlns="http://www.w3.org/2000/svg"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke-width="1.5"
                                    stroke="currentColor"
                                    class="text-base-300 h-12 w-12"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m6 4.125l2.25 2.25m0 0l2.25 2.25M12 13.875l2.25-2.25M12 13.875l-2.25 2.25M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"
                                    />
                                </svg>
                                <p class="text-base-content/70">{{ emptyMessage }}</p>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div class="bg-base-200 px-4 py-2" v-if="hasContent">
            <div class="flex items-center justify-between text-sm">
                <div>
                    Mostrando <span class="font-medium">{{ data.length }}</span> de
                    <span class="font-medium">{{ pagination.total }}</span> registros
                </div>

                <Pagination :pagination="pagination" />
            </div>
        </div>
    </div>
</template>
