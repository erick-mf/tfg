<script setup>
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
        password: '', // opcion
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
                    <!-- Buscador mejorado -->
                    <div class="relative">
                        <input
                            type="text"
                            placeholder="Buscar usuarios..."
                            class="input input-sm input-bordered w-full max-w-xs pr-4 pl-9"
                        />
                        <span class="absolute top-1/2 left-3 -translate-y-1/2">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-4 w-4 text-gray-400"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"
                                />
                            </svg>
                        </span>
                    </div>

                    <!-- Filtros (dropdown) -->
                    <div class="dropdown dropdown-end">
                        <label tabindex="0" class="btn btn-ghost text-base">
                            <svg
                                xmlns="http://www.w3.org/2000/svg"
                                class="h-5 w-5"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    stroke-width="2"
                                    d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.293A1 1 0 013 6.586V4z"
                                />
                            </svg>
                            Filtros
                        </label>
                        <ul
                            tabindex="0"
                            class="menu menu-sm dropdown-content bg-base-100 rounded-box z-[1] mt-2 w-52 p-2 shadow"
                        >
                            <li><a>Por nombre (A-Z)</a></li>
                            <li><a>Por nombre (Z-A)</a></li>
                            <li><a>Recientes primero</a></li>
                            <li><a>Antiguos primero</a></li>
                        </ul>
                    </div>

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
            <SidebarRight text="Nuevo Empleado" drawerId="sidebar-right">
                <template #form>
                    <UserForm :user="editingUser" />
                </template>
            </SidebarRight>
        </template>
    </BaseLayout>
</template>
