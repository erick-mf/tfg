<script setup>
import EmptyState from '@/components/EmptyState.vue';
import Filters from '@/components/Filters.vue';
import MenuItemCard from '@/components/MenuItemCard.vue';
import MenuItemForm from '@/components/MenuItemForm.vue';
import SearchForm from '@/components/SearchForm.vue';
import SidebarBtn from '@/components/SidebarBtn.vue';
import SidebarRight from '@/components/SidebarRight.vue';
import Table from '@/components/Table.vue';
import BaseLayout from '@/layouts/BaseLayout.vue';
import { ref } from 'vue';

defineProps({
    menuItems: {
        type: Object,
    },
    categories: {
        type: Object,
    },
});

const columns = [
    { label: 'Imagen', field: 'image_url' },
    { label: 'Nombre', field: 'name' },
    { label: 'Disponibilidad', field: 'status' },
    { label: 'Precio', field: 'formated_price' },
    { label: 'Categoría', field: 'category_name' },
];

const editingMenuItem = ref(null);

function handleEditMenuItem(menuItem) {
    editingMenuItem.value = {
        id: menuItem.id,
        name: menuItem.name,
        price: menuItem.price,
        is_available: menuItem.is_available,
        menu_category_id: menuItem.menu_category_id,
        imagen_path: menuItem.image_path,
    };
    const sidebarRight = document.getElementById('sidebar-right');
    if (sidebarRight) sidebarRight.checked = true;
}
</script>

<template>
    <BaseLayout>
        <template #header>
            <div class="flex w-full items-center justify-between gap-4">
                <h1 class="text-sm font-semibold sm:text-xl">Lista de Items de Menu</h1>

                <div class="flex items-center gap-3">
                    <SearchForm />
                    <Filters />
                    <SidebarBtn text="Nuevo Item" />
                </div>
            </div>
        </template>

        <template #content>
            <EmptyState v-if="menuItems && menuItems.data.length === 0" />
            <div v-else>
                <Table
                    :content="menuItems"
                    :columns="columns"
                    :deleteAction="'admin.menu-items.destroy'"
                    @edit-item="handleEditMenuItem"
                />
                <MenuItemCard
                    :content="menuItems"
                    :deleteAction="'admin.menu-items.destroy'"
                    @edit-item="handleEditMenuItem"
                />
            </div>
            <SidebarRight>
                <template #form>
                    <MenuItemForm :menuItem="editingMenuItem" :categories="categories" />
                </template>
            </SidebarRight>
        </template>
    </BaseLayout>
</template>
