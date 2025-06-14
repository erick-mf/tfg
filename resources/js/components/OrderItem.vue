<script setup>
import { computed } from 'vue';

const item = defineModel('item', { type: Object, required: true });
const props = defineProps({
    menuItems: { type: Array, required: true },
    formErrors: { type: Object, default: () => ({}) },
    itemIndex: { type: Number, required: true },
    isOrderPaid: { type: Boolean, default: false },
});
const emit = defineEmits(['remove']);

const categories = computed(() => {
    const uniqueCategoriesMap = new Map();
    props.menuItems.forEach((mi) => {
        if (mi.menu_category && !uniqueCategoriesMap.has(mi.menu_category.id)) {
            uniqueCategoriesMap.set(mi.menu_category.id, mi.menu_category);
        }
    });
    return Array.from(uniqueCategoriesMap.values()).sort((a, b) => a.name.localeCompare(b.name));
});

const filteredMenuItems = computed(() => {
    if (!item.value.selectedCategoryId) {
        return props.menuItems.sort((a, b) => a.name.localeCompare(b.name));
    }
    return props.menuItems
        .filter((mi) => mi.menu_category?.id == item.value.selectedCategoryId)
        .sort((a, b) => a.name.localeCompare(b.name));
});

function calculateSubtotal() {
    const price = parseFloat(item.value.unit_price) || 0;
    const quantity = parseInt(item.value.quantity) || 0;
    item.value.subtotal = parseFloat((quantity * price).toFixed(2));
}

function onProductChange() {
    const menuItem = props.menuItems.find((mi) => mi.id === item.value.menu_item_id);
    if (menuItem) {
        item.value.unit_price = parseFloat(menuItem.price);
        item.value.menuItemName = menuItem.name;
    } else {
        item.value.unit_price = 0;
        item.value.menuItemName = 'Seleccionar...';
    }
    calculateSubtotal();
}
</script>

<template>
    <div class="bg-base-200 collapse shadow">
        <input type="checkbox" v-model="item.isExpanded" :id="`item-toggle-${item._temp_id}`" class="peer sr-only" />

        <div
            class="collapse-title collapse-arrow peer-checked:bg-base-300 peer-checked:border-neutral-content/20 flex items-center justify-between py-2 text-sm font-medium peer-checked:border-b"
        >
            <label :for="`item-toggle-${item._temp_id}`" class="flex-grow cursor-pointer truncate">
                {{ item.menuItemName || `Item ${itemIndex + 1}` }}
                <span v-if="item.quantity > 0 && item.subtotal > 0" class="text-base-content/70 text-xs">
                    ({{ item.quantity }}x {{ item.unit_price?.toFixed(2) }}€ = {{ item.subtotal?.toFixed(2) }}€)
                </span>
            </label>

            <button
                type="button"
                @click="emit('remove')"
                class="btn btn-ghost btn-error btn-xs z-10 ml-2 shrink-0 p-1"
                :disabled="isOrderPaid"
                title="Eliminar Item"
            >
                ✕
            </button>
        </div>

        <div class="collapse-content">
            <div class="grid grid-cols-1 gap-2 p-1 pt-2 sm:grid-cols-2">
                <div class="form-control">
                    <label class="label py-0"><span class="label-text text-xs">Categoría</span></label>
                    <select
                        v-model="item.selectedCategoryId"
                        class="select select-bordered select-sm w-full"
                        :disabled="isOrderPaid"
                    >
                        <option :value="null">Todas las categorías</option>
                        <option v-for="cat in categories" :key="cat.id" :value="cat.id">{{ cat.name }}</option>
                    </select>
                </div>
                <div class="form-control">
                    <label class="label py-0"><span class="label-text text-xs">Producto</span></label>
                    <select
                        v-model="item.menu_item_id"
                        @change="onProductChange"
                        class="select select-bordered select-sm w-full"
                        :class="{ 'select-error': formErrors[`items.${itemIndex}.menu_item_id`] }"
                        :disabled="isOrderPaid"
                    >
                        <option :value="null">Seleccionar...</option>
                        <option v-for="mi in filteredMenuItems" :key="mi.id" :value="mi.id">
                            {{ mi.name }} - {{ mi.price }}€
                        </option>
                    </select>
                    <label class="label py-0" v-if="formErrors[`items.${itemIndex}.menu_item_id`]"
                        ><span class="label-text-alt text-error text-xs">{{
                            formErrors[`items.${itemIndex}.menu_item_id`]
                        }}</span></label
                    >
                </div>
                <div class="form-control">
                    <label class="label py-0"><span class="label-text text-xs">Cantidad</span></label>
                    <input
                        v-model.number="item.quantity"
                        @input="calculateSubtotal"
                        type="number"
                        min="1"
                        class="input input-bordered input-sm w-full"
                        :class="{ 'input-error': formErrors[`items.${itemIndex}.quantity`] }"
                        :disabled="isOrderPaid"
                    />
                    <label class="label py-0" v-if="formErrors[`items.${itemIndex}.quantity`]"
                        ><span class="label-text-alt text-error text-xs">{{
                            formErrors[`items.${itemIndex}.quantity`]
                        }}</span></label
                    >
                </div>
                <div class="form-control">
                    <label class="label py-0"><span class="label-text text-xs">Precio Unit.</span></label>
                    <div class="input input-bordered input-sm bg-base-300/50 flex w-full items-center text-sm">
                        {{ item.unit_price?.toFixed(2) || '0.00' }}€
                    </div>
                </div>
                <div class="form-control sm:col-span-2">
                    <label class="label py-0"><span class="label-text text-xs">Subtotal</span></label>
                    <input
                        :value="item.subtotal?.toFixed(2) || '0.00'"
                        readonly
                        class="input input-bordered input-sm bg-base-300 w-full font-semibold"
                    />
                </div>
                <div class="form-control sm:col-span-2">
                    <label class="label py-0"><span class="label-text text-xs">Notas</span></label>
                    <textarea
                        v-model="item.notes"
                        class="textarea textarea-bordered textarea-sm w-full resize-none"
                        :class="{ 'textarea-error': formErrors[`items.${itemIndex}.notes`] }"
                        rows="2"
                        placeholder="Notas adicionales..."
                        :disabled="isOrderPaid"
                    ></textarea>
                    <label class="label py-0" v-if="formErrors[`items.${itemIndex}.notes`]"
                        ><span class="label-text-alt text-error text-xs">{{
                            formErrors[`items.${itemIndex}.notes`]
                        }}</span></label
                    >
                </div>
            </div>
        </div>
    </div>
</template>
