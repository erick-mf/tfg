<script setup>
import BtnPrimary from '@/components/BtnPrimary.vue';
import BtnSidebarRightCancel from '@/components/BtnSidebarRightCancel.vue';
import { useForm } from '@inertiajs/vue3';
import { computed } from 'vue';

const props = defineProps({
    summary: Object,
    validation: Object,
});

const form = useForm({
    counted_cash: null,
    notes: '',
});

const cashDifference = computed(() => {
    if (form.counted_cash === null || form.counted_cash === '') return null;
    return (parseFloat(form.counted_cash) - parseFloat(props.summary.expectedCash)).toFixed(2);
});

const submit = () => {
    form.post(route('admin.z-reports.store'), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            const sidebarCheckbox = document.getElementById('sidebar-right');
            if (sidebarCheckbox) sidebarCheckbox.checked = false;
        },
    });
};

const formatCurrency = (value) => new Intl.NumberFormat('es-ES', { style: 'currency', currency: 'EUR' }).format(value);

const cancelForm = () => {
    form.reset();
};
</script>

<template>
    <div class="p-2">
        <div v-if="!validation.canClose" class="alert alert-error mb-6">
            <div>
                <h3 class="font-bold">No se puede cerrar la caja</h3>
                <div v-for="message in validation.messages" :key="message" class="text-xs">{{ message }}</div>
            </div>
        </div>

        <div class="collapse-arrow bg-base-200 collapse mb-6">
            <input type="checkbox" checked />
            <div class="collapse-title text-xl font-medium">Resumen del Turno</div>
            <div class="collapse-content">
                <div class="stats stats-vertical w-full shadow-md">
                    <div class="stat">
                        <div class="stat-title">Ingresos Totales</div>
                        <div class="stat-value text-primary">{{ formatCurrency(summary.totalRevenue) }}</div>
                    </div>
                    <div class="stat">
                        <div class="stat-title">Ventas con Tarjeta</div>
                        <div class="stat-value text-secondary">{{ formatCurrency(summary.totalCard) }}</div>
                    </div>
                    <div class="stat">
                        <div class="stat-title">Efectivo Esperado</div>
                        <div class="stat-value">{{ formatCurrency(summary.expectedCash) }}</div>
                    </div>
                </div>
            </div>
        </div>

        <form @submit.prevent="submit" class="space-y-4">
            <div>
                <label for="counted_cash" class="label"><span class="label-text">Efectivo Contado</span></label>
                <input
                    id="counted_cash"
                    type="number"
                    step="0.01"
                    class="input input-bordered w-full"
                    v-model="form.counted_cash"
                    required
                    :disabled="!validation.canClose"
                />
                <div v-if="form.errors.counted_cash" class="text-error mt-1 text-sm">
                    {{ form.errors.counted_cash }}
                </div>
            </div>

            <div v-if="cashDifference !== null" class="stat bg-base-200 rounded-lg">
                <div class="stat-title">Diferencia de Caja</div>
                <div
                    class="stat-value"
                    :class="{ 'text-success': cashDifference >= 0, 'text-error': cashDifference < 0 }"
                >
                    {{ formatCurrency(cashDifference) }}
                </div>
            </div>

            <div>
                <label for="notes" class="label"><span class="label-text">Notas (Opcional)</span></label>
                <textarea
                    id="notes"
                    class="textarea textarea-bordered w-full"
                    v-model="form.notes"
                    :disabled="!validation.canClose"
                ></textarea>
                <div v-if="form.errors.notes" class="text-error mt-1 text-sm">{{ form.errors.notes }}</div>
            </div>

            <BtnSidebarRightCancel @click="cancelForm" />
            <BtnPrimary type="submit" :disabled="!validation.canClose || form.processing">
                Confirmar Cierre
            </BtnPrimary>
        </form>
    </div>
</template>
