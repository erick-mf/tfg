<script setup>
import { computed } from 'vue';

const form = defineModel('form', { type: Object, required: true });

const props = defineProps({
    order: { type: Object, required: true },
    users: { type: Array, required: true },
    tables: { type: Array, required: true },
});

const ORDER_STATUS = {
    PAID: 'pagado',
    PENDING: 'pendiente',
    PREPARING: 'en preparacion',
    CANCELLED: 'cancelado',
};
const TABLE_STATUS = {
    AVAILABLE: 'disponible',
};

const availableTables = computed(() => {
    const allTables = Array.isArray(props.tables) ? props.tables : props.tables?.data || [];
    let availables = allTables.filter((table) => table.status === TABLE_STATUS.AVAILABLE);
    if (form.value.table_id) {
        const selectedTable = allTables.find((table) => table.id == form.value.table_id);
        if (selectedTable && !availables.some((table) => table.id == selectedTable.id)) {
            availables.push(selectedTable);
        }
    }
    return availables.sort((a, b) => a.name.localeCompare(b.name));
});

const isPaidOrder = computed(() => form.value.status === ORDER_STATUS.PAID);
</script>

<template>
    <div class="space-y-4">
        <div class="form-control">
            <label class="label"><span class="label-text">Camarero</span></label>
            <div
                class="w-full"
                :class="{ 'tooltip tooltip-top': isPaidOrder }"
                :data-tip="isPaidOrder ? 'No se puede cambiar: Pedido Pagado' : null"
            >
                <select
                    v-model="form.user_id"
                    class="select select-bordered w-full"
                    :disabled="isPaidOrder"
                    :class="{ 'select-error': form.errors.user_id }"
                >
                    <option disabled value="">Seleccione un camarero</option>
                    <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                </select>
            </div>
            <label class="label" v-if="form.errors.user_id"
                ><span class="label-text-alt text-error">{{ form.errors.user_id }}</span></label
            >
        </div>

        <div class="form-control">
            <label class="label"><span class="label-text">Mesa</span></label>
            <div
                class="w-full"
                :class="{ 'tooltip tooltip-top': isPaidOrder }"
                :data-tip="isPaidOrder ? 'No se puede cambiar: Pedido Pagado' : null"
            >
                <select
                    v-model="form.table_id"
                    class="select select-bordered w-full"
                    :disabled="isPaidOrder"
                    :class="{ 'select-error': form.errors.table_id }"
                >
                    <option disabled value="">Seleccione una mesa</option>
                    <option v-for="table in availableTables" :key="table.id" :value="table.id">{{ table.name }}</option>
                </select>
            </div>
            <label class="label" v-if="form.errors.table_id"
                ><span class="label-text-alt text-error">{{ form.errors.table_id }}</span></label
            >
        </div>

        <div class="form-control">
            <label class="label"><span class="label-text">Estado del pedido</span></label>
            <div
                class="w-full"
                :class="{ 'tooltip tooltip-top': isPaidOrder }"
                :data-tip="isPaidOrder ? 'No se puede cambiar: Pedido Pagado' : null"
            >
                <select
                    v-model="form.status"
                    class="select select-bordered w-full"
                    :disabled="isPaidOrder"
                    :class="{ 'select-error': form.errors.status }"
                >
                    <option disabled value="">Seleccione un estado</option>
                    <option :value="ORDER_STATUS.PAID">Pagado</option>
                    <option :value="ORDER_STATUS.PENDING">Pendiente</option>
                    <option :value="ORDER_STATUS.PREPARING">En preparación</option>
                    <option :value="ORDER_STATUS.CANCELLED">Cancelado</option>
                </select>
            </div>
            <label class="label" v-if="form.errors.status"
                ><span class="label-text-alt text-error">{{ form.errors.status }}</span></label
            >
        </div>

        <div v-if="isPaidOrder" class="form-control">
            <label class="label"><span class="label-text">Método de pago</span></label>
            <select
                v-model="form.payment_method"
                class="select select-bordered w-full"
                :class="{ 'select-error': form.errors.payment_method }"
            >
                <option disabled value="">Seleccione un método</option>
                <option value="efectivo">Efectivo</option>
                <option value="tarjeta">Tarjeta</option>
            </select>
            <label class="label" v-if="form.errors.payment_method"
                ><span class="label-text-alt text-error">{{ form.errors.payment_method }}</span></label
            >
        </div>

        <div class="mt-4 space-y-1 text-sm">
            <div v-if="isPaidOrder && order.formated_paid_at">
                <span class="font-semibold">Fecha de pago:</span>
                <span class="text-base-content/80 ml-1">{{ order.formated_paid_at }}</span>
            </div>
            <div>
                <span class="font-semibold">Total Pedido (guardado):</span>
                <span class="text-base-content/80 ml-1">{{ order.formated_total }}</span>
            </div>
        </div>
    </div>
</template>
