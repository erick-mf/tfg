<script setup>
import BtnPrimary from '@/components/BtnPrimary.vue';
import BtnSidebarRightCancel from '@/components/BtnSidebarRightCancel.vue';
import { useForm } from '@inertiajs/vue3';
import { computed, watch } from 'vue';

const props = defineProps({
    order: {
        type: Object,
        default: null,
    },
    users: {
        type: Object,
        default: null,
    },
    tables: {
        type: Object,
        default: null,
    },
});

const form = useForm({
    id: null,
    status: '',
    payment_method: '',
    paid_at: '',
    user_id: '',
    table_id: '',
});

const isEditing = computed(() => !!form.id);

const availableTables = computed(() => {
    let availables = props.tables.filter((table) => table.status === 'disponible');
    if (form.table_id) {
        const selectedTable = props.tables.find((table) => table.id == form.table_id);

        if (selectedTable && !availables.some((table) => table.id == selectedTable.id)) {
            availables.push(selectedTable);
        }
    }

    return availables;
});

watch(
    () => props.order,
    (newOrder) => {
        if (newOrder?.id) {
            Object.assign(form, {
                id: newOrder.id,
                status: newOrder.status || '',
                payment_method: newOrder.payment_method || '',
                paid_at: newOrder.paid_at || '',
                user_id: newOrder.user_id || '',
                table_id: newOrder.table_id || '',
            });
        } else {
            form.clearErrors();
            form.reset();
        }
    },
    { immediate: true },
);

const submitForm = () => {
    const optionsForm = {
        onSuccess: () => {
            form.clearErrors();
            form.reset();
            const sidebarRight = document.getElementById('sidebar-right');
            if (sidebarRight) sidebarRight.checked = false;
        },
    };

    form.put(route('admin.orders.update', form), optionsForm);
};

const cancelForm = () => {
    form.clearErrors();
    form.reset();
};
</script>

<template>
    <div v-if="props.order" class="mx-auto my-8 w-full max-w-md">
        <h2 class="mb-6 text-center text-2xl font-bold">Modificar Pedido</h2>

        <div class="space-y-4">
            <div class="form-control">
                <label class="label mb-2 w-full" for="user_id">
                    <span>Cambiar el camarero</span>
                </label>
                <div
                    clas="w-full"
                    :class="{ 'tooltip tooltip-top': form.status === 'pagado' }"
                    :data-tip="form.status === 'pagado' ? 'No se puede cambiar si el estado es pagado' : null"
                >
                    <select
                        id="user_id"
                        v-model="form.user_id"
                        class="select select-bordered w-full"
                        :disabled="form.status === 'pagado'"
                        :class="{ 'select-error': form.errors.user_id }"
                    >
                        <option disabled value="">Seleccione un camarero</option>
                        <option v-for="user in users" :key="user.id" :value="user.id">
                            {{ user.name }}
                        </option>
                    </select>
                    <label class="label" v-if="form.errors.user_id">
                        <span class="text-error">{{ form.errors.user_id }}</span>
                    </label>
                </div>
            </div>

            <div class="form-control">
                <label class="label mb-2 w-full" for="table_id">
                    <span>Cambiar la mesa</span>
                </label>
                <div
                    clas="w-full"
                    :class="{ 'tooltip tooltip-top': form.status === 'pagado' }"
                    :data-tip="form.status === 'pagado' ? 'No se puede cambiar si el estado es pagado' : null"
                >
                    <select
                        id="table_id"
                        v-model="form.table_id"
                        class="select select-bordered w-full"
                        :disabled="form.status === 'pagado'"
                        :class="{ 'select-error': form.errors.table_id }"
                    >
                        <option disabled value="">Seleccione una mesa</option>
                        <option v-for="table in availableTables" :key="table.id" :value="table.id">
                            {{ table.name }}
                        </option>
                    </select>
                </div>
                <label class="label" v-if="form.errors.table_id">
                    <span class="text-error">{{ form.errors.table_id }}</span>
                </label>
            </div>

            <div class="form-control">
                <label class="label mb-2" for="status">
                    <span>Cambiar el estado del pedido</span>
                </label>
                <select
                    id="status"
                    v-model="form.status"
                    class="select select-bordered w-full"
                    :disabled="form.status === 'pagado' && !isEditing"
                    :class="{ 'select-error': form.errors.status }"
                >
                    <option disabled value="">Seleccione un estado</option>
                    <option value="pagado">Pagado</option>
                    <option value="pendiente">Pendiente</option>
                    <option value="en preparacion">En preparación</option>
                </select>
                <label class="label" v-if="form.errors.status">
                    <span class="text-error">{{ form.errors.status }}</span>
                </label>
            </div>

            <div v-if="form.status === 'pagado'" class="form-control">
                <label class="label mb-2" for="payment_method">
                    <span>Cambiar el metodo de pago</span>
                </label>
                <select
                    id="payment_method"
                    v-model="form.payment_method"
                    class="select select-bordered w-full"
                    :class="{ 'select-error': form.errors.payment_method }"
                >
                    <option disabled value="">Seleccione un metodo</option>
                    <option value="efectivo">Efectivo</option>
                    <option value="tarjeta">Tarjeta</option>
                </select>
                <label class="label" v-if="form.errors.payment_method">
                    <span class="text-error">{{ form.errors.payment_method }}</span>
                </label>
            </div>

            <div v-if="form.status === 'pagado'" class="form-control mt-4 text-base">
                <span class="text-base-content font-semibold">Fecha de pago:</span>
                <span class="text-base-content/70 ml-1"> {{ order.formated_paid_at }}</span>
            </div>

            <div class="form-control mt-4 text-base">
                <span class="text-base-content font-semibold">Total:</span>
                <span class="text-base-content/70 ml-1"> {{ order.formated_total }}</span>
            </div>

            <div class="pt-4 sm:grid sm:grid-cols-2 sm:gap-4">
                <BtnSidebarRightCancel @click="cancelForm" />
                <BtnPrimary type="button" @click="submitForm" :disabled="form.processing"> Actualizar </BtnPrimary>
            </div>
        </div>
    </div>
</template>
