<script setup>
import OrderItem from '@/components/OrderItem.vue';
import { computed, nextTick, ref } from 'vue';

const form = defineModel('form', { type: Object, required: true });
const props = defineProps({
    menuItems: { type: Array, required: true },
});

const ORDER_STATUS = { PAID: 'pagado' };
const itemElements = ref([]);

const totalAmount = computed(() => {
    return form.value.items.reduce((sum, item) => sum + (parseFloat(item.subtotal) || 0), 0).toFixed(2);
});

const canAddItem = computed(() => {
    if (!form.value.id || form.value.status === ORDER_STATUS.PAID) return false;
    const lastItem = form.value.items[form.value.items.length - 1];
    return !lastItem || lastItem.menu_item_id;
});

const addNewItem = async () => {
    if (!canAddItem.value) return;
    form.value.items.forEach((item) => {
        item.isExpanded = false;
    });
    const newItem = {
        id: null,
        menu_item_id: null,
        quantity: 1,
        unit_price: 0,
        subtotal: 0,
        status: 'enviado',
        notes: '',
        order_id: form.value.id,
        menuItemName: 'Seleccionar producto...',
        selectedCategoryId: null,
        isExpanded: true,
        _temp_id: `temp_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`,
    };
    form.value.items.push(newItem);
    await nextTick();
    const lastItemElement = itemElements.value[itemElements.value.length - 1];
    lastItemElement?.$el.querySelector('select')?.focus();
};

const removeItem = (index) => {
    form.value.items.splice(index, 1);
};
</script>

<template>
    <div class="space-y-6">
        <div class="flex items-center justify-between">
            <h3 class="text-lg font-semibold">Items del Pedido</h3>
            <button type="button" @click="addNewItem" class="btn btn-neutral btn-sm" :disabled="!canAddItem">
                + Agregar Item
            </button>
        </div>

        <div v-if="form.items.length === 0" class="text-base-content/60 py-8 text-center">
            Este pedido aún no tiene items.
        </div>

        <div v-else class="max-h-[calc(100vh-22rem)] space-y-2 overflow-y-auto pr-1">
            <OrderItem
                v-for="(item, index) in form.items"
                :key="item._temp_id"
                :ref="
                    (el) => {
                        if (el) itemElements[index] = el;
                    }
                "
                v-model:item="form.items[index]"
                :menu-items="menuItems"
                :form-errors="form.errors"
                :item-index="index"
                :is-order-paid="form.status === ORDER_STATUS.PAID"
                @remove="removeItem(index)"
            />
        </div>

        <div v-if="form.items.length > 0" class="divider pt-2">
            <span class="font-semibold">Total Items (actual): {{ totalAmount }}€</span>
        </div>
    </div>
</template>
