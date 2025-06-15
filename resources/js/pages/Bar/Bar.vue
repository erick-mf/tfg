<script setup>
import BarOrderCard from '@/components/BarOrderCard.vue';
import UserDropdown from '@/components/UserDropdown.vue';
import BarLayout from '@/layouts/BarLayout.vue';
import { router } from '@inertiajs/vue3';
import axios from 'axios';
import { computed, onMounted, onUnmounted, ref } from 'vue';

const props = defineProps({
    orders: { type: Array, required: true },
    menuItems: { type: Array, required: true },
});

const activeOrders = ref([...props.orders]);
const completedOrders = ref([]);

const allMenuItems = ref([...props.menuItems]);
const currentTime = ref(new Date().toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' }));
const searchTerm = ref('');
let pollingInterval = null;

const fetchOrders = async () => {
    try {
        const response = await axios.get(route('bar.orders.data'));

        activeOrders.value = response.data.activeOrders.sort((a, b) => new Date(a.created_at) - new Date(b.created_at));
        completedOrders.value = response.data.readyOrders.sort(
            (a, b) => new Date(b.created_at) - new Date(a.created_at),
        );
    } catch (error) {
        console.error('Error al obtener pedidos de la barra:', error);
    }
};

function forceUpdate() {
    fetchOrders();
}

const groupedMenuItems = computed(() => {
    const filtered = allMenuItems.value.filter((item) =>
        item.name.toLowerCase().includes(searchTerm.value.toLowerCase()),
    );
    return filtered.reduce((acc, item) => {
        const category = item.menu_category?.name || 'Sin Categoría';
        if (!acc[category]) acc[category] = [];
        acc[category].push(item);
        return acc;
    }, {});
});

function toggleAvailability(menuItem) {
    router.put(
        route('bar.menu-items.toggle', menuItem.id),
        {},
        {
            preserveState: true,
            preserveScroll: true,
            onStart: () => {
                menuItem.is_available = !menuItem.is_available;
            },
            onError: () => {
                menuItem.is_available = !menuItem.is_available;
            },
        },
    );
}

onMounted(() => {
    fetchOrders();

    const clockInterval = setInterval(() => {
        currentTime.value = new Date().toLocaleTimeString('es-ES', { hour: '2-digit', minute: '2-digit' });
    }, 1000);

    pollingInterval = setInterval(fetchOrders, 8000);

    onUnmounted(() => {
        clearInterval(clockInterval);
        clearInterval(pollingInterval);
    });
});
</script>

<template>
    <BarLayout>
        <header class="border-primary bg-base-100 sticky top-0 z-10 border-b-2 p-3">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <h1 class="text-lg font-bold">PANEL DE BARRA</h1>
                </div>
                <div class="flex items-center gap-4">
                    <span class="font-bold">{{ currentTime }}</span>
                    <button class="btn btn-neutral btn-sm" onclick="menu_availability_modal.showModal()">
                        Disponibilidad
                    </button>
                    <UserDropdown direction="bottom" />
                </div>
            </div>
        </header>

        <main class="space-y-8 p-4 sm:p-6">
            <div>
                <div class="divider divider-start text-xl font-bold">Pedidos Activos ({{ activeOrders.length }})</div>
                <div v-if="activeOrders.length === 0" class="text-base-content/60 py-10 text-center">
                    <p>¡Ningún pedido activo en la barra!</p>
                </div>
                <TransitionGroup
                    v-else
                    tag="div"
                    name="card-list"
                    class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4"
                >
                    <BarOrderCard
                        v-for="order in activeOrders"
                        :key="order.id"
                        :order="order"
                        @item-status-changed="forceUpdate"
                    />
                </TransitionGroup>
            </div>

            <div>
                <div class="divider divider-start text-xl font-bold">
                    Listos para Recoger ({{ completedOrders.length }})
                </div>
                <div v-if="completedOrders.length === 0" class="text-base-content/60 py-10 text-center">
                    <p>Ninguna comanda lista.</p>
                </div>
                <TransitionGroup
                    v-else
                    tag="div"
                    name="card-list-completed"
                    class="grid grid-cols-1 gap-4 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4"
                >
                    <BarOrderCard
                        v-for="order in completedOrders"
                        :key="order.id"
                        :order="order"
                        :is-completed="true"
                        @item-status-changed="forceUpdate"
                    />
                </TransitionGroup>
            </div>
        </main>

        <dialog id="menu_availability_modal" class="modal">
            <div class="modal-box bg-base-100 max-w-3xl">
                <form method="dialog">
                    <button class="btn btn-sm btn-circle btn-ghost absolute top-2 right-2">✕</button>
                </form>
                <h3 class="mb-4 text-lg font-bold">Disponibilidad de Productos</h3>
                <input
                    type="text"
                    v-model="searchTerm"
                    placeholder="Buscar..."
                    class="input input-bordered mb-4 w-full"
                />
                <div class="max-h-[60vh] space-y-4 overflow-y-auto">
                    <div v-for="(items, category) in groupedMenuItems" :key="category">
                        <h4 class="text-primary font-bold">{{ category }}</h4>
                        <div class="mt-2 space-y-2">
                            <div
                                v-for="item in items"
                                :key="item.id"
                                class="border-base-300 flex items-center justify-between rounded border p-3"
                            >
                                <span>{{ item.name }}</span>
                                <input
                                    type="checkbox"
                                    class="toggle toggle-primary"
                                    :checked="item.is_available"
                                    @change="toggleAvailability(item)"
                                />
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <form method="dialog" class="modal-backdrop"><button>Cerrar</button></form>
        </dialog>
    </BarLayout>
</template>

<style>
.card-list-enter-active,
.card-list-leave-active,
.card-list-move,
.card-list-completed-enter-active,
.card-list-completed-leave-active,
.card-list-completed-move {
    transition: all 0.5s ease;
}
.card-list-enter-from,
.card-list-leave-to,
.card-list-completed-enter-from,
.card-list-completed-leave-to {
    opacity: 0;
    transform: translateY(20px);
}
.card-list-leave-active,
.card-list-completed-leave-active {
    position: absolute;
}
</style>
