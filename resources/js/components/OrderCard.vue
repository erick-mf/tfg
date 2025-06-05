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
    deleteAction: {
        type: String,
        required: true,
    },
});

const ordersData = computed(() => props.content.data);

const emit = defineEmits(['edit-item']);

function editOrder(order) {
    emit('edit-item', order);
}

function deleteOrderConfirmed(orderToDelete) {
    router.delete(route(props.deleteAction, orderToDelete.id), {
        preserveScroll: true,
        preserveState: false,
    });
}

function getStatusClass(status) {
    const lowerStatus = String(status).toLowerCase();
    if (lowerStatus === 'pendiente') return 'badge-warning';
    if (lowerStatus === 'en preparación') return 'badge-info';
    if (lowerStatus === 'servido' || lowerStatus === 'completado') return 'badge-success';
    if (lowerStatus === 'cancelado') return 'badge-error';
    return 'badge-ghost';
}
</script>

<template>
    <div class="block sm:hidden">
        <div class="space-y-3">
            <div
                v-for="order in ordersData"
                :key="order.id"
                class="border-base-300 bg-base-100 rounded-lg border shadow-sm transition-shadow hover:shadow-md"
            >
                <div class="card-body p-3 sm:p-4">
                    <div class="flex items-start justify-between gap-2">
                        <div class="min-w-0 flex-1 pr-2">
                            <h3 class="text-base-content mb-1 text-base leading-tight font-semibold">
                                Mesa: {{ order.table_number || 'N/A' }}
                            </h3>

                            <div class="space-y-1.5 text-sm">
                                <div class="text-base-content/80">
                                    <span class="font-medium">Camarero:</span>
                                    <span class="ml-1">{{ order.user_name || 'N/A' }}</span>
                                </div>
                                <div class="text-base-content/80">
                                    <span class="font-medium">Total:</span>
                                    <span class="text-primary ml-1 font-semibold">{{ order.formated_total }}</span>
                                </div>
                            </div>
                        </div>

                        <div class="flex flex-col items-end gap-2">
                            <div :class="['badge badge-sm font-medium', getStatusClass(order.status)]">
                                {{ order.status }}
                            </div>

                            <div class="flex items-center gap-1">
                                <EditAction @click="editOrder(order)" />
                                <ConfirmationDeleteModal
                                    :item="order"
                                    :action="deleteAction"
                                    @delete="deleteOrderConfirmed(order)"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <Pagination :pagination="props.content" />
    </div>
</template>
