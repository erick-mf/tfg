<script setup>
import { router } from '@inertiajs/vue3';
import { computed, onMounted, onUnmounted, ref } from 'vue';

const props = defineProps({
    order: Object,
    isCompleted: { type: Boolean, default: false },
});
const emit = defineEmits(['itemStatusChanged']);

const now = ref(new Date());

const orderTimeAgo = computed(() => {
    const diffInMinutes = Math.floor((now.value - new Date(props.order.created_at)) / 1000 / 60);
    return `${diffInMinutes}m`;
});

const urgencyClass = computed(() => {
    if (props.isCompleted) return 'border-success bg-success/10';
    const diffInMinutes = Math.floor((now.value - new Date(props.order.created_at)) / 1000 / 60);
    if (diffInMinutes >= 15) return 'bg-error/20 border-error shadow-error/20';
    return 'bg-base-100 border-base-300';
});

function markItemAs(item, newStatus) {
    const originalStatus = item.status;
    item.status = newStatus;

    router.put(
        route('bar.updateItemStatus', { orderItem: item.id }),
        { status: newStatus },
        {
            preserveState: true,
            preserveScroll: true,
            onSuccess: () => {
                emit('itemStatusChanged');
            },
            onError: () => {
                item.status = originalStatus;
                console.log('Error al cambiar el estado');
            },
        },
    );
}

let timer;
onMounted(() => {
    timer = setInterval(() => {
        now.value = new Date();
    }, 15000);
});
onUnmounted(() => {
    clearInterval(timer);
});
</script>

<template>
    <div class="card flex flex-col rounded-lg border shadow-md transition-colors duration-500" :class="urgencyClass">
        <div class="border-b p-4" :class="urgencyClass.includes('error') ? 'border-error/30' : 'border-base-300'">
            <div class="flex items-center justify-between">
                <h2 class="card-title text-2xl font-bold">{{ order.assigned_table.name }}</h2>
                <span v-if="!isCompleted" class="font-mono text-lg font-semibold">{{ orderTimeAgo }}</span>
                <span v-else class="badge badge-success">LISTO</span>
            </div>
            <p class="text-base-content/70 text-sm">Por: {{ order.user.name }}</p>
        </div>

        <div class="max-h-[60vh] flex-grow space-y-3 overflow-y-auto p-4">
            <div v-if="order.order_items.length === 0" class="text-base-content/50 py-4 text-center">
                (No hay items de barra en este pedido)
            </div>
            <div
                v-for="item in order.order_items"
                :key="item.id"
                class="bg-base-200 rounded-md p-3"
                :class="{ 'line-through opacity-50': item.status === 'listo' || item.status === 'cancelado' }"
            >
                <div class="grid grid-cols-[1fr,auto] items-center gap-2">
                    <div>
                        <p class="font-bold">
                            <span class="text-primary mr-2 text-xl">{{ item.quantity }}x</span>{{ item.menu_item.name }}
                        </p>
                    </div>
                    <div
                        class="badge justify-self-end font-semibold"
                        :class="{
                            'badge-info animate-pulse': item.status === 'en preparacion',
                            'badge-success': item.status === 'listo',
                            'badge-error': item.status === 'cancelado',
                            'badge-ghost': item.status === 'enviado' || item.status === 'pendiente',
                        }"
                    >
                        {{ item.status }}
                    </div>
                </div>

                <p v-if="item.notes" class="text-base-content/80 border-base-content/30 mt-2 border-l-2 pl-2 text-sm">
                    {{ item.notes }}
                </p>

                <div
                    class="border-base-300/50 mt-3 flex justify-end gap-2 border-t border-dashed pt-3"
                    v-if="!isCompleted && item.status !== 'listo' && item.status !== 'cancelado'"
                >
                    <button
                        class="btn btn-xs"
                        @click="markItemAs(item, 'en preparacion')"
                        :disabled="item.status === 'en preparacion'"
                    >
                        Preparar
                    </button>
                    <button class="btn btn-xs btn-success" @click="markItemAs(item, 'listo')">Listo</button>
                    <button class="btn btn-xs btn-ghost" @click="markItemAs(item, 'cancelado')">Cancelar</button>
                </div>
            </div>
        </div>
    </div>
</template>
