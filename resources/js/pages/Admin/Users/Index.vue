<script setup>
import Filters from '@/components/Filters.vue';
import SearchForm from '@/components/SearchForm.vue';
import SidebarBtn from '@/components/SidebarBtn.vue';
import SidebarRight from '@/components/SidebarRight.vue';
import Table from '@/components/Table.vue';
import UserForm from '@/components/UserForm.vue';
import BaseLayout from '@/layouts/BaseLayout.vue';
import { ref } from 'vue';

defineProps({
    users: {
        type: Array,
    },
});

const columns = [
    { label: 'Nombre', field: 'full_name' },
    { label: 'Teléfono', field: 'phone' },
    { label: 'Teléfono Emergencia', field: 'phone_emergency' },
    { label: 'Rol', field: 'role' },
];

const editingUser = ref(null);

function handleEditUser(user) {
    editingUser.value = {
        id: user.id,
        name: user.name,
        surnames: user.surnames,
        phone: user.phone,
        phone_emergency: user.phone_emergency,
        role: user.role,
        password: '',
    };
    const sidebarRight = document.getElementById('sidebar-right');
    if (sidebarRight) sidebarRight.checked = true;
}
</script>

<template>
    <BaseLayout>
        <template #header>
            <div class="flex w-full items-center justify-between gap-4">
                <h1 class="text-xl font-semibold">Gestión de Usuarios</h1>

                <div class="flex items-center gap-3">
                    <SearchForm />
                    <Filters />
                    <SidebarBtn text="Nuevo Empleado" />
                </div>
            </div>
        </template>

        <template #content>
            <Table
                :content="users"
                :columns="columns"
                emptyMessage="No hay usuarios registrados"
                @edit-user="handleEditUser"
            />
            <SidebarRight text="Nuevo Empleado">
                <template #form>
                    <UserForm :user="editingUser" />
                </template>
            </SidebarRight>
        </template>
    </BaseLayout>
</template>
