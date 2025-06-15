<script setup>
import OrderTakingForm from '@/components/OrderTakingForm.vue';

const props = defineProps({
    visible: Boolean,
    table: Object,
    menuItems: Array,
});

const emit = defineEmits(['close', 'orderUpdated']);
</script>

<template>
    <dialog class="modal" :class="{ 'modal-open': visible }">
        <div class="modal-box h-[90vh] w-11/12 max-w-6xl overflow-hidden p-0">
            <OrderTakingForm
                v-if="table"
                :table="table"
                :order="table.active_order"
                :menu-items="menuItems"
                @close="emit('close')"
                @order-updated="emit('orderUpdated')"
            />
        </div>
        <form method="dialog" class="modal-backdrop">
            <button @click="emit('close')">close</button>
        </form>
    </dialog>
</template>
