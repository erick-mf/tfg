<script setup>
import EmptyState from '@/components/EmptyState.vue';
import Filters from '@/components/Filters.vue';
import ProductCard from '@/components/ProductCard.vue';
import ProductForm from '@/components/ProductForm.vue';
import SearchForm from '@/components/SearchForm.vue';
import SidebarBtn from '@/components/SidebarBtn.vue';
import SidebarRight from '@/components/SidebarRight.vue';
import Table from '@/components/Table.vue';
import BaseLayout from '@/layouts/BaseLayout.vue';
import { ref } from 'vue';

defineProps({
    products: {
        type: Object,
    },
    locations: {
        type: Object,
    },
});

const columns = [
    { label: 'Nombre', field: 'name' },
    { label: 'Disponibilidad', field: 'status' },
    { label: 'Ubicación', field: 'location_name' },
];

const editingProduct = ref(null);

function handleEditProduct(product) {
    editingProduct.value = {
        id: product.id,
        name: product.name,
        is_available: product.is_available,
        location_id: product.location_id,
    };
    const sidebarRight = document.getElementById('sidebar-right');
    if (sidebarRight) sidebarRight.checked = true;
}
</script>

<template>
    <BaseLayout>
        <template #header>
            <div class="flex w-full items-center justify-between gap-4">
                <h1 class="text-sm font-semibold sm:text-xl">Lista de Productos</h1>

                <div class="flex items-center gap-3">
                    <SearchForm />
                    <Filters />
                    <SidebarBtn text="Nuevo Producto" />
                </div>
            </div>
        </template>

        <template #content>
            <EmptyState v-if="products.data.length === 0" />
            <div v-else>
                <Table
                    :content="products"
                    :columns="columns"
                    :deleteAction="'admin.products.destroy'"
                    @edit-item="handleEditProduct"
                />
                <ProductCard
                    :content="products"
                    :deleteAction="'admin.products.destroy'"
                    @edit-item="handleEditProduct"
                />
            </div>
            <SidebarRight text="Nuevo Producto">
                <template #form>
                    <ProductForm :product="editingProduct" :locations="locations" />
                </template>
            </SidebarRight>
        </template>
    </BaseLayout>
</template>
