<script setup>
import OrderTakingModal from '@/components/OrderTakingModal.vue';
import { useAuth } from '@/composables/useAuth';
import WaiterLayout from '@/layouts/WaiterLayout.vue';
import { router } from '@inertiajs/vue3';
import { computed, ref } from 'vue';

const props = defineProps({
    tables: { type: Array, required: true },
    menuItems: { type: Array, required: true },
});

const selectedTable = ref(null);
const isOrderFormVisible = ref(false);

const { user } = useAuth();
const myTables = computed(() => props.tables.filter((table) => table.is_mine));
const availableTables = computed(() => props.tables.filter((table) => table.status === 'disponible'));
const otherOccupiedTables = computed(() =>
    props.tables.filter((table) => table.status === 'ocupada' && !table.is_mine),
);

function getStatusColor(status) {
    const colors = {
        disponible: 'bg-green-100 hover:bg-green-200 border-green-300',
        ocupada: 'bg-red-100 hover:bg-red-200 border-red-300',
        reservada: 'bg-blue-100 hover:bg-blue-200 border-blue-300',
        'en limpieza': 'bg-yellow-100 hover:bg-yellow-200 border-yellow-300',
    };
    return colors[status] || 'bg-gray-100 hover:bg-gray-200 border-gray-300';
}

function getStatusTextColor(status) {
    const colors = {
        disponible: 'text-green-800',
        ocupada: 'text-red-800',
        reservada: 'text-blue-800',
        'en limpieza': 'text-yellow-800',
    };
    return colors[status] || 'text-gray-800';
}

function getStatusBarColor(status) {
    const colors = {
        disponible: 'bg-green-500',
        ocupada: 'bg-red-500',
        reservada: 'bg-blue-500',
        'en limpieza': 'bg-yellow-500',
    };
    return colors[status] || 'bg-gray-500';
}

function handleTableClick(table) {
    if (!table.is_editable) {
        alert(`Mesa ${table.name} está ocupada por ${user.value.name || 'otro camarero'}`);
        return;
    }
    selectedTable.value = table;
    isOrderFormVisible.value = true;
}

function closeOrderForm() {
    isOrderFormVisible.value = false;
    selectedTable.value = null;
}

function handleOrderUpdate() {
    router.reload({ only: ['tables'] });
}
</script>

<template>
    <WaiterLayout>
        <div class="space-y-8">
            <section>
                <h2 class="mb-4 text-xl font-bold">Mis Mesas Activas</h2>
                <div v-if="myTables.length === 0" class="text-base-content/60 bg-base-100 rounded-lg p-6 text-center">
                    No tienes mesas activas.
                </div>
                <div v-else class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5">
                    <div
                        v-for="table in myTables"
                        :key="table.id"
                        @click="handleTableClick(table)"
                        class="relative flex h-24 cursor-pointer flex-col justify-center rounded-lg border-2 p-4 shadow-sm transition-all hover:-translate-y-1 hover:shadow-md"
                        :class="getStatusColor(table.status)"
                    >
                        <div
                            class="absolute top-0 left-0 h-full w-1.5 rounded-l-md"
                            :class="getStatusBarColor(table.status)"
                        ></div>
                        <div class="text-lg font-bold text-gray-800">{{ table.name }}</div>
                        <div
                            v-if="table.active_order"
                            class="text-sm font-semibold"
                            :class="getStatusTextColor(table.status)"
                        >
                            {{ table.active_order.formated_total }}
                        </div>
                    </div>
                </div>
            </section>

            <section>
                <div class="divider">Mesas Disponibles</div>
                <div
                    v-if="availableTables.length === 0"
                    class="text-base-content/60 bg-base-100 rounded-lg p-6 text-center"
                >
                    No hay mesas disponibles.
                </div>
                <div v-else class="grid grid-cols-2 gap-4 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5">
                    <div
                        v-for="table in availableTables"
                        :key="table.id"
                        @click="handleTableClick(table)"
                        class="relative flex h-24 cursor-pointer flex-col justify-center rounded-lg border-2 p-4 shadow-sm transition-all hover:-translate-y-1 hover:shadow-md"
                        :class="getStatusColor(table.status)"
                    >
                        <div
                            class="absolute top-0 left-0 h-full w-1.5 rounded-l-md"
                            :class="getStatusBarColor(table.status)"
                        ></div>
                        <div class="text-lg font-bold text-gray-800">{{ table.name }}</div>
                        <div class="text-sm font-medium capitalize" :class="getStatusTextColor(table.status)">
                            {{ table.status }}
                        </div>
                    </div>
                </div>
            </section>

            <section>
                <div class="divider">Otras Mesas Ocupadas</div>
                <div
                    v-if="otherOccupiedTables.length === 0"
                    class="text-base-content/60 bg-base-100 rounded-lg p-6 text-center"
                >
                    Todas las mesas ocupadas son tuyas.
                </div>
                <div v-else class="grid grid-cols-3 gap-3 sm:grid-cols-4 md:grid-cols-6 lg:grid-cols-8">
                    <div
                        v-for="table in otherOccupiedTables"
                        :key="table.id"
                        class="tooltip"
                        :data-tip="`Atendida por: ${table.active_order?.user?.name || 'Otro'}`"
                    >
                        <div
                            class="relative flex h-20 flex-col justify-center rounded-lg border-2 p-3 opacity-70"
                            :class="getStatusColor(table.status)"
                        >
                            <div
                                class="absolute top-0 left-0 h-full w-1.5 rounded-l-md"
                                :class="getStatusBarColor(table.status)"
                            ></div>
                            <div class="text-md font-bold text-gray-800">{{ table.name }}</div>
                            <div class="text-xs font-medium capitalize" :class="getStatusTextColor(table.status)">
                                Ocupada
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

        <OrderTakingModal
            :visible="isOrderFormVisible"
            :table="selectedTable"
            :menu-items="menuItems"
            @close="closeOrderForm"
            @order-updated="handleOrderUpdate"
        />
    </WaiterLayout>
</template>
