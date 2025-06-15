<script setup>
import EmptyState from '@/components/EmptyState.vue';
import SidebarBtn from '@/components/SidebarBtn.vue';
import SidebarRight from '@/components/SidebarRight.vue';
import Table from '@/components/Table.vue';
import ZReportCard from '@/components/ZReportCard.vue';
import ZReportForm from '@/components/ZReportForm.vue';
import BaseLayout from '@/layouts/BaseLayout.vue';
import { Head } from '@inertiajs/vue3';

const props = defineProps({
    formData: { type: Object, required: true },
    historyData: { type: Object, required: true },
});

const columns = [
    { label: 'Fecha y hora de cierre', field: 'formated_end_date' },
    { label: 'Realizado por ', field: 'user_name' },
    { label: 'Ingresos totales', field: 'formated_total_revenue' },
    { label: 'Diferencia caja', field: 'formated_cash_difference' },
    { label: 'Observaciones', field: 'notes' },
];
</script>

<template>
    <Head title="Gestión de Reportes Z" />

    <BaseLayout>
        <template #header>
            <div class="flex w-full items-center justify-between gap-4">
                <h1 class="text-sm font-semibold sm:text-xl">Gestión de Reportes Z</h1>
                <SidebarBtn text="Nuevo Cierre" />
            </div>
        </template>

        <template #content>
            <EmptyState v-if="historyData.data.length === 0" />
            <div v-else>
                <Table :content="historyData" :columns="columns" :showActions="false" />
                <ZReportCard :content="historyData" />
            </div>
            <SidebarRight text="Nuevo Cierre de Caja">
                <template #form>
                    <ZReportForm :summary="formData.summary" :validation="formData.validation" />
                </template>
            </SidebarRight>
        </template>
    </BaseLayout>
</template>
