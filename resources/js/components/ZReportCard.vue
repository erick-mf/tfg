<script setup>
import Pagination from '@/components/Pagination.vue';
import { Calendar, FileText } from 'lucide-vue-next';
import { computed } from 'vue';

const props = defineProps({
    content: {
        type: Object,
        required: true,
    },
});

const reports = computed(() => props.content.data);

const pagination = computed(() => props.content);
</script>

<template>
    <div class="block sm:hidden">
        <div class="space-y-4">
            <div
                v-for="report in reports"
                :key="report.id"
                class="card bg-base-100 border-base-200 border shadow-sm transition-shadow duration-200 hover:shadow-lg"
            >
                <div class="card-body p-4">
                    <div class="flex items-start justify-between gap-3">
                        <div class="flex-1">
                            <div class="text-base-content/70 mt-1 flex items-center gap-2 text-xs">
                                <Calendar class="h-3 w-3" />
                                <span>{{ report.formated_end_date }}</span>
                            </div>
                        </div>
                        <div class="text-right text-xs">
                            <span class="font-semibold">{{ report.user_name }}</span>
                        </div>
                    </div>

                    <div class="divider my-2"></div>

                    <div class="grid grid-cols-2 gap-4">
                        <div class="flex items-center gap-2">
                            <DollarSign class="text-primary h-5 w-5" />
                            <div>
                                <div class="text-base-content/70 text-xs">Ingresos</div>
                                <div class="font-bold">{{ report.formated_total_revenue }}</div>
                            </div>
                        </div>
                        <div class="flex items-center gap-2">
                            <div>
                                <div class="text-base-content/70 text-xs">Diferencia</div>
                                <div class="font-bold">{{ report.formated_cash_difference }}</div>
                            </div>
                        </div>
                    </div>

                    <div v-if="report.notes" class="mt-3">
                        <div class="flex items-start gap-2 text-sm">
                            <FileText class="text-base-content/60 mt-0.5 h-4 w-4 flex-shrink-0" />
                            <p class="text-base-content/80 italic">{{ report.notes }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <Pagination :pagination="pagination" class="mt-6" />
    </div>
</template>
