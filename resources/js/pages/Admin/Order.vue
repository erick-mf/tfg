<script setup>
import EmptyState from '@/components/EmptyState.vue';
import Filters from '@/components/Filters.vue';
import OrderCard from '@/components/OrderCard.vue';
import OrderForm from '@/components/OrderForm.vue';
import SearchForm from '@/components/SearchForm.vue';
import SidebarRight from '@/components/SidebarRight.vue';
import Table from '@/components/Table.vue';
import BaseLayout from '@/layouts/BaseLayout.vue';
import { ref } from 'vue';

defineProps({
    orders: {
        type: Object,
    },
    users: {
        type: Object,
    },
    tables: {
        type: Object,
    },
});

const columns = [
    { label: 'Camarero', field: 'user_name' },
    { label: 'Mesa', field: 'table_number' },
    { label: 'Pagado el', field: 'formated_paid_at' },
    { label: 'Estatus', field: 'status' },
    { label: 'Metodo de pago', field: 'payment_method' },
    { label: 'Total', field: 'formated_total' },
];

const editingOrder = ref(null);

function handleEditOrder(order) {
    editingOrder.value = {
        id: order.id,
        status: order.status,
        payment_method: order.payment_method,
        paid_at: order.paid_at,
        user_id: order.user_id,
        table_id: order.table_id,
        formated_total: order.formated_total,
        formated_paid_at: order.formated_paid_at,
    };
    const sidebarRight = document.getElementById('sidebar-right');
    if (sidebarRight) sidebarRight.checked = true;
}
</script>

<template>
    <BaseLayout>
        <template #header>
            <div class="flex w-full items-center justify-between gap-4">
                <h1 class="text-sm font-semibold sm:text-xl">Lista de Pedidos</h1>

                <div class="flex items-center gap-3">
                    <SearchForm />
                    <Filters />
                </div>
            </div>
        </template>

        <template #content>
            <EmptyState v-if="orders.data.length === 0" />
            <div v-else>
                <Table
                    :content="orders"
                    :columns="columns"
                    :deleteAction="'admin.orders.destroy'"
                    @edit-item="handleEditOrder"
                />
                <OrderCard :content="orders" :deleteAction="'admin.orders.destroy'" @edit-item="handleEditOrder" />
            </div>
            <SidebarRight>
                <template #form>
                    <OrderForm :order="editingOrder" :users="users" :tables="tables" />
                </template>
            </SidebarRight>
        </template>
    </BaseLayout>
</template>
