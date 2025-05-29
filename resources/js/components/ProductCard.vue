<script setup>
import ConfirmationDeleteModal from '@/components/ConfirmationDeleteModal.vue';
import EditAction from '@/components/EditAction.vue';
import Pagination from '@/components/Pagination.vue';
import { router } from '@inertiajs/vue3';
import { Archive, MapPin } from 'lucide-vue-next';
import { computed } from 'vue';
const props = defineProps({
    content: {
        type: Object,
        required: true,
    },
    deleteAction: {
        type: String,
        required: true,
        default: '',
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
    });
}
</script>
<template>
    <div class="block sm:hidden">
        <div class="space-y-3">
            <div
                v-for="(item, itemIndex) in data"
                :key="itemIndex"
                class="card bg-base-100 border-base-200/70 hover:border-primary/20 border shadow-sm transition-all duration-200 hover:shadow-md"
            >
                <div class="card-body p-4">
                    <div class="flex items-start justify-between">
                        <div class="min-w-0 flex-1">
                            <h3 class="text-base-content mb-1 truncate text-base font-semibold">
                                {{ item.name || 'Sin nombre' }}
                            </h3>
                            <div class="text-base-content/70 flex items-center gap-1.5 text-sm">
                                <MapPin class="h-3.5 w-3.5" />
                                <span class="truncate">{{ item.location_name || 'Sin ubicación' }}</span>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2">
                            <Archive class="text-base-content/60 h-4 w-4" />
                            <span class="text-base-content/60 text-sm">Estado:</span>
                            <span class="font-semibold" :class="item.is_available ? 'text-success' : 'text-error'">
                                {{ item.status }}
                            </span>
                        </div>

                        <div class="ml-4 flex gap-2">
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
