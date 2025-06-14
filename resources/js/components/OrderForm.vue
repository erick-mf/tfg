<script setup>
import BtnPrimary from '@/components/BtnPrimary.vue';
import BtnSidebarRightCancel from '@/components/BtnSidebarRightCancel.vue';
import OrderDetails from '@/components/OrderDetails.vue';
import OrderItems from '@/components/OrderItems.vue';
import { useForm } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    order: { type: Object, required: true },
    users: { type: Array, required: true },
    tables: { type: Array, required: true },
    menuItems: { type: Array, required: true },
});

const emit = defineEmits(['form-cancelled', 'form-submitted-successfully']);

const activeTab = ref('order');

const form = useForm({
    id: null,
    status: '',
    payment_method: '',
    user_id: '',
    table_id: '',
    items: [],
});

function calculateSubtotal(item) {
    const price = parseFloat(item.unit_price) || 0;
    const quantity = parseInt(item.quantity) || 0;
    return parseFloat((quantity * price).toFixed(2));
}

watch(
    () => props.order,
    (newOrder) => {
        if (newOrder?.id) {
            const preparedItems = (newOrder.order_items || []).map((item) => {
                const menuItem = props.menuItems.find((mi) => mi.id === item.menu_item_id);
                return {
                    ...item,
                    quantity: parseInt(item.quantity) || 1,
                    unit_price: parseFloat(item.unit_price) || 0,
                    subtotal: calculateSubtotal(item),
                    menuItemName: menuItem ? menuItem.name : 'Producto cargado',
                    selectedCategoryId: menuItem?.menu_category_id || null,
                    isExpanded: !item.id,
                    _temp_id: `temp_${Date.now()}_${Math.random().toString(36).substr(2, 9)}`,
                };
            });

            form.defaults({
                id: newOrder.id,
                status: newOrder.status || '',
                payment_method: newOrder.payment_method || '',
                user_id: newOrder.user_id || '',
                table_id: newOrder.table_id || '',
                items: preparedItems,
            }).reset();
        }
    },
    { immediate: true, deep: true },
);

const submit = () => {
    if (!form.id) return;

    form.transform((data) => ({
        ...data,
        items: data.items.map((item) => ({
            ...item,
            subtotal: calculateSubtotal(item),
        })),
    })).put(route('admin.orders.update', form.id), {
        preserveScroll: true,
        onSuccess: () => {
            const sidebarRight = document.getElementById('sidebar-right');
            if (sidebarRight) sidebarRight.checked = false;
        },
    });
};
</script>

<template>
    <div class="mx-auto my-8 w-full max-w-2xl px-4 sm:px-0">
        <h2 class="mb-6 text-center text-2xl font-bold">Modificar Pedido</h2>

        <div role="tablist" class="tabs tabs-bordered mb-6 w-full">
            <button
                role="tab"
                @click="activeTab = 'order'"
                :class="{ 'tab-active': activeTab === 'order' }"
                class="tab"
            >
                Detalles Pedido
            </button>
            <button
                role="tab"
                @click="activeTab = 'items'"
                :class="{ 'tab-active': activeTab === 'items' }"
                class="tab"
            >
                Items ({{ form.items.length }})
            </button>
        </div>

        <OrderDetails
            v-show="activeTab === 'order'"
            v-model:form="form"
            :order="props.order"
            :users="props.users"
            :tables="props.tables"
        />

        <OrderItems v-show="activeTab === 'items'" v-model:form="form" :menu-items="props.menuItems" />

        <div class="pt-8 sm:grid sm:grid-cols-2 sm:gap-4">
            <BtnSidebarRightCancel @click="cancelForm" />
            <BtnPrimary type="button" @click="submit" :disabled="form.processing || !form.isDirty">
                Guardar Cambios
            </BtnPrimary>
        </div>
    </div>
</template>
