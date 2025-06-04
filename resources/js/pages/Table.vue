<script setup>
import ConfirmacionDeleteModal from '@/components/ConfirmationDeleteModal.vue';
import EditAction from '@/components/EditAction.vue';
import EmptyState from '@/components/EmptyState.vue';
import Pagination from '@/components/Pagination.vue';
import SidebarBtn from '@/components/SidebarBtn.vue';
import SidebarRight from '@/components/SidebarRight.vue';
import TableForm from '@/components/TableForm.vue';
import BaseLayout from '@/layouts/BaseLayout.vue';
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    tables: {
        type: Object,
    },
});

const editTable = ref(null);

function handleEditTable(table) {
    editTable.value = {
        id: table.id,
        name: table.name,
        status: table.status,
    };
    const sidebarRight = document.getElementById('sidebar-right');
    if (sidebarRight) sidebarRight.checked = true;
}

function deleteTable(item, action) {
    router.delete(route(`${action}`, item), {
        preserveScroll: true,
        // NOTE: para no usar datos cacheados y muestre los datos actualizados
        preserveState: false,
    });
}

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
</script>

<template>
    <BaseLayout>
        <template #header>
            <div class="flex w-full items-center justify-between gap-4">
                <h1 class="text-sm font-semibold sm:text-xl">Lista de Mesas</h1>
                <SidebarBtn text="Nueva Mesa" />
            </div>
        </template>
        <template #content>
            <div class="flex h-full w-full flex-col">
                <EmptyState v-if="!props.tables.data || props.tables.data.length == 0" class="m-auto flex-grow" />
                <div v-else class="flex h-full w-full flex-col">
                    <div class="flex-1 overflow-y-auto pb-4">
                        <div class="grid grid-cols-1 gap-4 p-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4">
                            <div
                                v-for="(table, index) in props.tables.data"
                                :key="index"
                                class="group relative flex h-20 items-center justify-between rounded-lg border-2 px-4 py-3 shadow-sm transition-all duration-200 hover:shadow-md"
                                :class="getStatusColor(table.status)"
                            >
                                <div class="flex flex-col">
                                    <div class="truncate text-lg font-bold text-gray-800">
                                        {{ table.name }}
                                    </div>
                                    <div
                                        class="truncate text-sm font-medium capitalize"
                                        :class="getStatusTextColor(table.status)"
                                    >
                                        {{ table.status }}
                                    </div>
                                </div>

                                <div class="relative flex items-center gap-1">
                                    <EditAction @click="handleEditTable(table)" />
                                    <ConfirmacionDeleteModal
                                        :item="table"
                                        :action="'admin.tables.destroy'"
                                        @delete="deleteTable"
                                    />
                                </div>

                                <div
                                    class="absolute top-0 left-0 h-full w-1 rounded-l-lg"
                                    :class="{
                                        'bg-green-500': table.status === 'disponible',
                                        'bg-red-500': table.status === 'ocupada',
                                        'bg-blue-500': table.status === 'reservada',
                                        'bg-yellow-500': table.status === 'en limpieza',
                                    }"
                                ></div>
                            </div>
                        </div>
                    </div>

                    <Pagination :pagination="props.tables" />
                </div>

                <SidebarRight text="Nueva Mesa">
                    <template #form>
                        <TableForm :table="editTable" />
                    </template>
                </SidebarRight>
            </div>
        </template>
    </BaseLayout>
</template>
