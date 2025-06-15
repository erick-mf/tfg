<script setup>
import { useForm, usePage } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

const props = defineProps({
    table: { type: Object, required: true },
    order: { type: Object, default: null },
    menuItems: { type: Array, required: true },
});

const emit = defineEmits(['close', 'orderUpdated']);

const form = useForm({
    table_id: props.table.id,
    user_id: usePage().props.auth.user.id,
    status: 'pendiente',
    items: [],
    payment_method: null,
});

const hasPendingItemsToSend = computed(() => {
    return form.items.some((item) => !item.id);
});

const selectedCategoryId = ref(null);

watch(
    () => props.order,
    (existingOrder) => {
        if (existingOrder) {
            form.defaults({
                ...form.data(),
                status: existingOrder.status,
                items: existingOrder.order_items.map((item) => ({
                    id: item.id,
                    menu_item_id: item.menu_item_id,
                    name: item.menu_item_name,
                    quantity: item.quantity,
                    unit_price: item.unit_price,
                    notes: item.notes || '',
                    status: item.status,
                })),
            }).reset();
        } else {
            form.defaults({
                table_id: props.table.id,
                user_id: usePage().props.auth.user.id,
                status: 'pendiente',
                items: [],
                payment_method: null,
            }).reset();
        }
    },
    { immediate: true },
);

const categories = computed(() => {
    const cats = props.menuItems.map((item) => ({ id: item.menu_category_id, name: item.category_name }));
    const unique = Array.from(new Map(cats.map((cat) => [cat.id, cat])).values());
    return unique.sort((a, b) => a.name.localeCompare(b.name));
});

const filteredMenuItems = computed(() => {
    if (!selectedCategoryId.value) return props.menuItems;
    return props.menuItems.filter((item) => item.menu_category_id === selectedCategoryId.value);
});

function addItem(menuItem) {
    const existingItem = form.items.find((item) => item.menu_item_id === menuItem.id && !item.id);
    if (existingItem) {
        existingItem.quantity++;
    } else {
        form.items.push({
            menu_item_id: menuItem.id,
            name: menuItem.name,
            quantity: 1,
            unit_price: menuItem.price,
            notes: '',
            status: 'pendiente',
        });
    }
}

function updateQuantity(item, amount) {
    if (item.id) return;
    item.quantity += amount;
    if (item.quantity <= 0) removeItem(item);
}

function removeItem(itemToRemove) {
    if (itemToRemove.id) return;
    form.items = form.items.filter((item) => item !== itemToRemove);
}

const totalAmount = computed(() => {
    return form.items.reduce((sum, item) => sum + item.quantity * item.unit_price, 0).toFixed(2);
});

function submit() {
    const isUpdating = !!props.order;

    if (isUpdating) {
        form.items.forEach((item) => {
            if (item.status === 'pendiente') {
                item.status = 'enviado';
            }
        });
    } else {
        form.items.forEach((item) => {
            item.status = 'enviado';
        });
    }

    const routeName = isUpdating ? 'waiter.orders.update' : 'waiter.orders.store';
    const params = isUpdating ? { order: props.order.id } : {};

    form.transform((data) => ({ ...data, _method: isUpdating ? 'PUT' : 'POST' })).post(route(routeName, params), {
        onSuccess: () => {
            emit('orderUpdated');
            emit('close');
        },
        onError: () => {
            form.reset();
        },
    });
}

function payOrder(method) {
    if (!props.order) return;
    form.status = 'pagado';
    form.payment_method = method;
    submit();
}

function getStatusInfo(status) {
    switch (status) {
        case 'pendiente':
            return { text: 'Pendiente', class: 'badge-ghost' };
        case 'enviado':
            return { text: 'Enviado', class: 'badge-info' };
        case 'en preparacion':
            return { text: 'En preparación', class: 'badge-warning' };
        case 'listo':
            return { text: 'Listo', class: 'badge-success' };
        case 'cancelado':
            return { text: 'Cancelado', class: 'badge-error' };
        default:
            return { text: status, class: 'badge-neutral' };
    }
}
</script>

<template>
    <div class="bg-base-200 flex h-full flex-col">
        <!-- Header -->
        <div class="border-base-300 flex-shrink-0 border-b p-4">
            <div class="flex items-center justify-between">
                <div>
                    <h3 class="text-xl font-bold">
                        Pedido: <span class="text-primary">{{ table.name }}</span>
                    </h3>
                    <p class="text-base-content/70 text-sm">
                        <span v-if="order && !table.is_mine" class="text-warning">
                            Este pedido pertenece a otro camarero. (Solo lectura)
                        </span>
                        <span v-else>Añade productos a la comanda</span>
                    </p>
                </div>
                <button @click="emit('close')" class="btn btn-sm btn-circle btn-ghost">✕</button>
            </div>
        </div>

        <div class="flex flex-grow flex-col gap-4 overflow-hidden p-4 lg:flex-row">
            <div class="flex w-full flex-col gap-3 overflow-hidden lg:w-3/5">
                <div class="tabs tabs-boxed flex-shrink-0">
                    <a class="tab" :class="{ 'tab-active': !selectedCategoryId }" @click="selectedCategoryId = null"
                        >Todos</a
                    >
                    <a
                        v-for="cat in categories"
                        :key="cat.id"
                        class="tab"
                        :class="{ 'tab-active': selectedCategoryId === cat.id }"
                        @click="selectedCategoryId = cat.id"
                    >
                        {{ cat.name }}
                    </a>
                </div>
                <div
                    class="grid flex-grow grid-cols-2 content-start gap-3 overflow-y-auto pr-2 md:grid-cols-3 lg:grid-cols-4"
                >
                    <div
                        v-for="menuItem in filteredMenuItems"
                        :key="menuItem.id"
                        @click="addItem(menuItem)"
                        class="card card-compact bg-base-100 cursor-pointer shadow transition-transform hover:scale-105"
                        :class="{ 'pointer-events-none opacity-50': order && !table.is_mine }"
                    >
                        <figure
                            class="h-24 bg-cover bg-center"
                            :style="menuItem.image_url ? `background-image: url(${menuItem.image_url})` : ''"
                        >
                            <span v-if="!menuItem.image_path" class="text-4xl">🍽️</span>
                        </figure>
                        <div class="card-body p-2 text-center">
                            <h2 class="text-xs leading-tight font-semibold">{{ menuItem.name }}</h2>
                            <p class="text-primary text-sm font-bold">{{ menuItem.formated_price }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="bg-base-100 flex w-full flex-col rounded-lg shadow-inner lg:w-2/5">
                <h4 class="border-base-300 flex-shrink-0 border-b p-4 font-bold">Comanda</h4>

                <div
                    v-if="form.items.length === 0"
                    class="text-base-content/60 flex flex-grow items-center justify-center p-4 text-center"
                >
                    <p>El pedido está vacío.</p>
                </div>
                <div v-else class="flex-grow space-y-2 overflow-y-auto p-4">
                    <div v-for="(item, index) in form.items" :key="index" class="border-base-300 rounded-md border p-2">
                        <div class="flex items-center justify-between">
                            <div class="flex flex-wrap items-center gap-2">
                                <span class="text-sm font-semibold">{{ item.name }}</span>
                                <span v-if="item.id" class="badge badge-sm" :class="getStatusInfo(item.status).class">
                                    {{ getStatusInfo(item.status).text }}
                                </span>
                                <span v-else class="badge badge-sm badge-accent animate-pulse"> Por enviar 📤 </span>
                            </div>

                            <div class="tooltip tooltip-left z-50" data-tip="Solo el encargado puede realizar cambios">
                                <button
                                    @click="removeItem(item)"
                                    class="btn btn-xs btn-circle btn-ghost text-error/70"
                                    :disabled="item.id || (order && !table.is_mine)"
                                >
                                    ✕
                                </button>
                            </div>
                        </div>

                        <div
                            class="tooltip tooltip-bottom mt-2 w-full"
                            :data-tip="item.id ? 'Solo el encargado puede realizar cambios' : null"
                        >
                            <div class="flex items-center justify-between">
                                <div class="join">
                                    <button
                                        @click="updateQuantity(item, -1)"
                                        class="btn btn-xs join-item"
                                        :disabled="item.id || (order && !table.is_mine)"
                                    >
                                        -
                                    </button>
                                    <span class="btn btn-xs join-item pointer-events-none">{{ item.quantity }}</span>
                                    <button
                                        @click="updateQuantity(item, 1)"
                                        class="btn btn-xs join-item"
                                        :disabled="item.id || (order && !table.is_mine)"
                                    >
                                        +
                                    </button>
                                </div>
                                <span class="font-mono text-sm"
                                    >{{ (item.quantity * item.unit_price).toFixed(2) }}€</span
                                >
                            </div>
                            <input
                                type="text"
                                v-model="item.notes"
                                placeholder="Añadir nota..."
                                class="input input-xs mt-2 w-full"
                                :disabled="item.id || (order && !table.is_mine)"
                            />
                        </div>
                    </div>
                </div>

                <!-- Footer -->
                <div class="border-base-300 flex-shrink-0 border-t p-4">
                    <div class="mb-4 flex items-center justify-between text-lg font-bold">
                        <span>Total:</span>
                        <span>{{ totalAmount }}€</span>
                    </div>

                    <button
                        @click="submit"
                        class="btn w-full"
                        :class="[order && hasPendingItemsToSend ? 'btn-accent animate-pulse' : 'btn-neutral']"
                        :disabled="form.processing || form.items.length === 0 || (order && !table.is_mine)"
                    >
                        <span v-if="form.processing" class="loading loading-spinner"></span>
                        <span v-if="!order">Enviar Pedido</span>
                        <span v-else-if="hasPendingItemsToSend">Añadir y Enviar</span>
                        <span v-else>Pedido Actualizado</span>
                    </button>

                    <div v-if="order && order.status !== 'pagado'" class="mt-2 flex gap-2">
                        <button
                            @click="payOrder('efectivo')"
                            class="btn btn-success w-1/2"
                            :disabled="form.processing || form.items.length === 0 || !table.is_mine"
                        >
                            <span v-if="form.processing" class="loading loading-spinner"></span>
                            Pagar con Efectivo
                        </button>
                        <button
                            @click="payOrder('tarjeta')"
                            class="btn btn-success w-1/2"
                            :disabled="form.processing || form.items.length === 0 || !table.is_mine"
                        >
                            <span v-if="form.processing" class="loading loading-spinner"></span>
                            Pagar con Tarjeta
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
