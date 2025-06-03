<script setup>
import ConfirmacionDeleteModal from '@/components/ConfirmationDeleteModal.vue';
import EditAction from '@/components/EditAction.vue';
import EmptyState from '@/components/EmptyState.vue';
import MenuCategoryForm from '@/components/MenuCategoryForm.vue';
import Pagination from '@/components/Pagination.vue';
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
    const currentPage = new URLSearchParams(window.location.search).get('page') || 1;
    router.delete(route(`${action}`, { ...item, page: currentPage }), {
        preserveScroll: true,
        preserveState: false,
    });
}
</script>

<template>
    <BaseLayout>
        <template #header>
            <div class="flex w-full items-center justify-between gap-4">
                <h1 class="text-sm font-semibold sm:text-xl">Lista de Categorías de Menú</h1>
                <SidebarBtn text="Nueva Categoría" />
            </div>
        </template>
        <template #content>
            <div class="flex h-full w-full flex-col">
                <EmptyState
                    v-if="!props.menuCategories.data || props.menuCategories.data.length === 0"
                    class="m-auto flex-grow"
                />
                <div v-else class="flex h-full w-full flex-col">
                    <div class="flex-1 overflow-y-auto pb-4">
                        <div class="grid grid-cols-1 gap-4 p-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                            <div
                                v-for="(menuCategory, index) in props.menuCategories.data"
                                :key="menuCategory.id || index"
                                class="group bg-base-200/50 hover:bg-base-200 relative flex h-20 items-center justify-between rounded-lg border-2 px-4 py-3"
                            >
                                <div class="flex min-w-0 flex-1 flex-col">
                                    <div class="truncate text-lg text-gray-800">
                                        {{ menuCategory.name }}
                                    </div>
                                </div>

                                <div class="relative ml-2 flex items-center gap-1">
                                    <EditAction @click="handleEditMenuCategory(menuCategory)" />
                                    <ConfirmacionDeleteModal
                                        :item="menuCategory"
                                        :action="'admin.categories.destroy'"
                                        @delete="deleteMenuCategory"
                                    />
                                </div>
                            </div>
                        </div>
                    </div>

                    <Pagination :pagination="props.menuCategories" />
                </div>

                <SidebarRight text="Nueva Categoría">
                    <template #form>
                        <MenuCategoryForm :menuCategory="editMenuCategory" />
                    </template>
                </SidebarRight>
            </div>
        </template>
    </BaseLayout>
</template>
