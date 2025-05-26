<script setup>
import ConfirmacionDeleteModal from '@/components/ConfirmationDeleteModal.vue';
import EditAction from '@/components/EditAction.vue';
import EmptyState from '@/components/EmptyState.vue';
import LocationForm from '@/components/LocationForm.vue';
import SidebarBtn from '@/components/SidebarBtn.vue';
import SidebarRight from '@/components/SidebarRight.vue';
import BaseLayout from '@/layouts/BaseLayout.vue';
import { router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    locations: {
        type: Object,
    },
});

const editLocation = ref(null);

function handleEditLocation(location) {
    editLocation.value = {
        id: location.id,
        name: location.name,
    };
    const sidebarRight = document.getElementById('sidebar-right');
    if (sidebarRight) sidebarRight.checked = true;
}

function deleteLocation(item, action) {
    router.delete(route(`${action}`, item), {
        preserveScroll: true,
    });
}
</script>

<template>
    <BaseLayout>
        <template #header>
            <div class="flex w-full items-center justify-between gap-4">
                <h1 class="text-base font-semibold sm:text-xl">Lista de Ubicaciones</h1>

                <SidebarBtn text="Nueva Ubicación" />
            </div>
        </template>
        <template #content>
            <EmptyState v-if="props.locations.data.length == 0" />
            <div v-else class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-3">
                <div
                    v-for="(location, index) in props.locations.data"
                    :key="index"
                    class="flex items-center justify-between rounded-lg border bg-white p-4 shadow-sm transition-shadow duration-200 hover:shadow"
                >
                    <span class="text-base font-medium text-gray-700">{{ location.name }}</span>

                    <div class="flex space-x-2">
                        <EditAction @click="handleEditLocation(location)" />
                        <ConfirmacionDeleteModal
                            :item="location"
                            :action="'admin.locations.destroy'"
                            @delete="deleteLocation"
                        />
                    </div>
                </div>
            </div>
            <SidebarRight text="Nueva Ubicación">
                <template #form>
                    <LocationForm :location="editLocation" />
                </template>
            </SidebarRight>
        </template>
    </BaseLayout>
</template>
