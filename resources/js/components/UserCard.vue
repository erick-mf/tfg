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
    deleteAction: {
        type: String,
        default: 'admin.users.destroy',
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
    <div class="block sm:hidden">
        <ul class="list bg-base-100 rounded-box shadow-md" v-if="hasContent">
            <li
                v-for="(item, itemIndex) in data"
                :key="itemIndex"
                class="list-row border-base-200 hover:bg-base-200/50 flex items-center gap-3 border-b p-4 transition-colors last:border-b-0"
            >
                <div>
                    <div class="rounded-box bg-neutral text-neutral-content flex size-10 items-center justify-center">
                        <span class="text-lg font-semibold">
                            {{
                                columns.length > 0 &&
                                item[columns[0].field] &&
                                typeof item[columns[0].field] === 'string'
                                    ? item[columns[0].field].charAt(0).toUpperCase()
                                    : '?'
                            }}
                        </span>
                    </div>
                </div>

                <div class="min-w-0 flex-grow">
                    <div
                        v-if="
                            columns.length > 0 &&
                            item[columns[0].field] !== null &&
                            item[columns[0].field] !== undefined
                        "
                        class="text-base-content truncate font-semibold"
                    >
                        {{ item[columns[0].field] }}
                    </div>
                    <div v-else-if="columns.length > 0" class="text-base-content/50 text-sm italic">No disponible</div>

                    <div
                        v-if="
                            columns.length > 1 &&
                            item[columns[1].field] !== null &&
                            item[columns[1].field] !== undefined
                        "
                        class="truncate text-xs font-semibold uppercase opacity-60"
                    >
                        {{ item[columns[1].field] }}
                    </div>
                    <div v-else-if="columns.length > 1" class="text-base-content/50 text-xs italic">No disponible</div>
                </div>

                <div class="flex flex-shrink-0 items-center gap-1" v-if="showActions">
                    <button class="btn btn-sm btn-square btn-ghost" @click="editItem(item)" aria-label="Editar">
                        <svg
                            class="size-[1.2em]"
                            xmlns="http://www.w3.org/2000/svg"
                            viewBox="0 0 24 24"
                            fill="none"
                            stroke="currentColor"
                            stroke-width="2"
                            stroke-linecap="round"
                            stroke-linejoin="round"
                        >
                            <path
                                d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931z"
                            ></path>
                            <path d="M19.5 7.125L16.862 4.487"></path>
                        </svg>
                    </button>

                    <ConfirmationDeleteModal :item="item" :action="deleteAction" @delete="deleteItem">
                        <template #trigger="{ openModal }">
                            <button
                                class="btn btn-sm btn-square btn-ghost text-error hover:bg-error/20"
                                @click="openModal"
                                aria-label="Eliminar"
                            >
                                <svg
                                    class="size-[1.2em]"
                                    xmlns="http://www.w3.org/2000/svg"
                                    viewBox="0 0 24 24"
                                    fill="none"
                                    stroke="currentColor"
                                    stroke-width="2"
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                >
                                    <path d="M3 6h18"></path>
                                    <path d="M19 6v14c0 1-1 2-2 2H7c-1 0-2-1-2-2V6"></path>
                                    <path d="M8 6V4c0-1 1-2 2-2h4c1 0 2 1 2 2v2"></path>
                                    <line x1="10" y1="11" x2="10" y2="17"></line>
                                    <line x1="14" y1="11" x2="14" y2="17"></line>
                                </svg>
                            </button>
                        </template>
                    </ConfirmationDeleteModal>
                </div>
            </li>
        </ul>

        <div v-if="!hasContent" class="card bg-base-100 border-base-200 border shadow-lg">
            <div class="card-body">
                <div class="flex flex-col items-center justify-center gap-6 py-12">
                    <div class="relative">
                        <div
                            class="from-primary/10 to-secondary/10 absolute inset-0 scale-110 rounded-full bg-gradient-to-br blur-sm"
                        ></div>
                        <div class="bg-base-200 relative rounded-full p-6">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke-width="1.5"
                                stroke="currentColor"
                                class="text-base-content/40 h-12 w-12"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M20.25 7.5l-.625 10.632a2.25 2.25 0 01-2.247 2.118H6.622a2.25 2.25 0 01-2.247-2.118L3.75 7.5m6 4.125l2.25 2.25m0 0l2.25 2.25M12 13.875l2.25-2.25M12 13.875l-2.25 2.25M3.375 7.5h17.25c.621 0 1.125-.504 1.125-1.125v-1.5c0-.621-.504-1.125-1.125-1.125H3.375c-.621 0-1.125.504-1.125 1.125v1.5c0 .621.504 1.125 1.125 1.125z"
                                />
                            </svg>
                        </div>
                    </div>
                    <div class="space-y-2 text-center">
                        <p class="text-base-content text-lg font-semibold">{{ emptyMessage }}</p>
                        <p class="text-base-content/60 max-w-sm text-sm">
                            Parece que no hay información para mostrar en este momento.
                        </p>
                    </div>
                </div>
            </div>
        </div>

        <Pagination :pagination="pagination" />
    </div>
</template>
