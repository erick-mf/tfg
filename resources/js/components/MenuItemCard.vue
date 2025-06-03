<script setup>
import ConfirmationDeleteModal from '@/components/ConfirmationDeleteModal.vue';
import EditAction from '@/components/EditAction.vue';
import Pagination from '@/components/Pagination.vue';
import { router } from '@inertiajs/vue3';
import { CircleCheck, CircleX } from 'lucide-vue-next';
import { computed } from 'vue';

const props = defineProps({
    content: {
        type: Object,
        required: true,
    },
    deleteAction: {
        type: String,
        required: true,
    },
});

const data = computed(() => props.content.data);
const pagination = computed(() => props.content);
const emit = defineEmits(['edit-item']);

function editItem(item) {
    emit('edit-item', item);
}

function deleteItem(itemFromModal, actionRouteName) {
    router.delete(route(actionRouteName, itemFromModal.id), {
        preserveScroll: true,
    });
}
</script>

<template>
    <div class="block sm:hidden">
        <div class="space-y-3">
            <div
                v-for="item in data"
                :key="item.id"
                class="rounded-lg border border-gray-200 bg-white shadow-sm transition-shadow hover:shadow-md"
            >
                <div class="flex h-28">
                    <!-- Imagen -->
                    <div v-if="item.image_url" class="h-28 w-28 flex-shrink-0">
                        <img :src="item.image_url" :alt="item.name" class="h-full w-full rounded-l-lg object-cover" />
                    </div>

                    <!-- Contenido principal -->
                    <div class="min-w-0 flex-1 p-3">
                        <div class="mb-2 flex items-start justify-between">
                            <div class="min-w-0 flex-1 pr-2">
                                <h3 class="mb-1 line-clamp-2 text-sm leading-tight font-semibold text-gray-900">
                                    {{ item.name }}
                                </h3>

                                <div class="space-y-1.5">
                                    <div class="flex items-center text-xs text-gray-600">
                                        <span class="mr-1 font-medium">Precio:</span>
                                        <span class="font-semibold text-blue-600">{{ item.formated_price }}</span>
                                    </div>

                                    <div v-if="item.menu_category_id" class="w-full text-xs text-gray-600">
                                        <span class="font-medium">Categoría:</span>
                                        <span class="ml-1 font-medium">{{ item.category_name }}</span>
                                    </div>
                                </div>
                            </div>

                            <!-- Status y acciones -->
                            <div class="flex flex-col items-end gap-2">
                                <div class="flex items-center gap-1">
                                    <component
                                        :is="item.is_available ? CircleCheck : CircleX"
                                        class="h-4 w-4"
                                        :class="item.is_available ? 'text-green-600' : 'text-red-600'"
                                    />
                                    <span
                                        class="text-xs font-medium whitespace-nowrap"
                                        :class="item.is_available ? 'text-green-600' : 'text-red-600'"
                                    >
                                        {{ item.status }}
                                    </span>
                                </div>

                                <div class="flex items-center gap-1">
                                    <EditAction @click="editItem(item)" />
                                    <ConfirmationDeleteModal :item="item" :action="deleteAction" @delete="deleteItem" />
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="mt-4">
            <Pagination :pagination="pagination" />
        </div>
    </div>
</template>
