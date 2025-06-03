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
                class="card bg-base-100 border-base-200/70 hover:border-primary/20 border shadow-sm transition-all duration-200 hover:shadow-md"
            >
                <figure v-if="item.image_url" class="h-48 w-full overflow-hidden bg-gray-200">
                    <img :src="item.image_url" :alt="item.name" class="h-full w-full object-cover" />
                </figure>

                <div class="card-body p-4">
                    <div class="flex items-start justify-between">
                        <div class="min-w-0 flex-1">
                            <h3 class="text-base-content mb-1 truncate text-lg font-semibold">
                                {{ item.name }}
                            </h3>
                            <div class="text-base-content/80 mb-1 flex items-center gap-1.5 text-sm">
                                <span class="text-base-content/60 text-sm">Precio:</span>
                                <span class="font-semibold">{{ item.formated_price }}</span>
                            </div>
                            <div
                                v-if="item.menu_category_id"
                                class="text-base-content/70 flex items-center gap-1.5 text-xs"
                            >
                                <span class="text-base-content/60 text-sm">Categoría:</span>
                                <span class="font-semibold">{{ item.category_name }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="mt-4 flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <component
                                :is="item.is_available ? CircleCheck : CircleX"
                                class="h-5 w-5"
                                :class="item.is_available ? 'text-success' : 'text-error'"
                            />
                            <span class="font-semibold" :class="item.is_available ? 'text-success' : 'text-error'">
                                {{ item.status }}
                            </span>
                        </div>

                        <div class="flex items-center gap-2">
                            <EditAction @click="editItem(item)" />
                            <ConfirmationDeleteModal :item="item" :action="deleteAction" @delete="deleteItem" />
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
