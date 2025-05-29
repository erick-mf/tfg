<script setup>
import ConfirmacionDeleteModal from '@/components/ConfirmationDeleteModal.vue';
import EditAction from '@/components/EditAction.vue';
import EmptyState from '@/components/EmptyState.vue';
import MenuCategoryForm from '@/components/MenuCategoryForm.vue';
import SidebarBtn from '@/components/SidebarBtn.vue';
import SidebarRight from '@/components/SidebarRight.vue';
import BaseLayout from '@/layouts/BaseLayout.vue';
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    menuCategories: {
        type: Object,
    },
});

const editMenuCategory = ref(null);

function handleEditMenuCategory(menuCategory) {
    editMenuCategory.value = {
        id: menuCategory.id,
        name: menuCategory.name,
    };
    const sidebarRight = document.getElementById('sidebar-right');
    if (sidebarRight) sidebarRight.checked = true;
}

function deleteMenuCategory(item, action) {
    router.delete(route(`${action}`, item), {
        preserveScroll: true,
    });
}
</script>

<template>
    <BaseLayout>
        <template #header>
            <div class="flex w-full items-center justify-between gap-4">
                <h1 class="text-sm font-semibold sm:text-xl">Lista de Categorias de Menu</h1>

                <SidebarBtn text="Nueva Categoría" />
            </div>
        </template>
        <template #content>
            <EmptyState v-if="props.menuCategories.data.length == 0" />
            <div v-else class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="(menuCategory, index) in props.menuCategories.data"
                    :key="index"
                    class="bg-base-100 border-base-300 hover:bg-base-200 flex items-center justify-between rounded-lg border py-4 shadow"
                >
                    <span class="pl-4 text-base font-medium text-gray-700">{{ menuCategory.name }}</span>

                    <div class="flex pr-1">
                        <EditAction @click="handleEditMenuCategory(menuCategory)" />
                        <ConfirmacionDeleteModal
                            :item="menuCategory"
                            :action="'admin.categories.destroy'"
                            @delete="deleteMenuCategory"
                        />
                    </div>
                </div>
            </div>
            <SidebarRight text="Nueva Categoría">
                <template #form>
                    <MenuCategoryForm :menuCategory="editMenuCategory" />
                </template>
            </SidebarRight>
        </template>
    </BaseLayout>
</template>
