<script setup>
import MenuItem from '@/components/MenuItem.vue';
import UserDropdown from '@/components/UserDropdown.vue';
</script>

<template>
    <div class="drawer-side z-5">
        <label for="admin-drawer" class="drawer-overlay"></label>
        <div class="menu bg-base-200 text-base-content min-h-full w-76 p-4">
            <div class="mb-4 flex items-center p-4">
                <svg
                    xmlns="http://www.w3.org/2000/svg"
                    viewBox="0 0 24 24"
                    fill="currentColor"
                    class="text-primary h-8 w-8"
                >
                    <path
                        fill-rule="evenodd"
                        d="M12.516 2.17a.75.75 0 00-1.032 0 11.209 11.209 0 01-7.877 3.08.75.75 0 00-.722.515A12.74 12.74 0 002.25 9.75c0 5.942 4.064 10.933 9.563 12.348a.749.749 0 00.374 0c5.499-1.415 9.563-6.406 9.563-12.348 0-1.39-.223-2.73-.635-3.985a.75.75 0 00-.722-.516l-.143.001c-2.996 0-5.717-1.17-7.734-3.08zm3.094 8.016a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.75-5.25z"
                        clip-rule="evenodd"
                    />
                </svg>
                <h2 class="ml-2 text-base font-bold">Panel de Administración</h2>
            </div>

            <!-- Menú -->
            <ul class="flex flex-grow flex-col">
                <MenuItem :href="route('admin.users.index')" :active="$page.url.startsWith('/admin/users')">
                    Usuarios
                </MenuItem>
                <MenuItem :href="route('admin.locations.index')" :active="$page.url.startsWith('/admin/locations')">
                    Ubicaciones de los Productos
                </MenuItem>
                <MenuItem :href="route('admin.products.index')" :active="$page.url.startsWith('/admin/products')">
                    Productos
                </MenuItem>
                <MenuItem :href="route('admin.categories.index')" :active="$page.url.startsWith('/admin/categories')">
                    Categorias del Menú
                </MenuItem>
                <MenuItem :href="route('admin.menu-items.index')" :active="$page.url.startsWith('/admin/menu-items')">
                    Items del Menú
                </MenuItem>
                <MenuItem :href="route('admin.tables.index')" :active="$page.url.startsWith('/admin/tables')">
                    Mesas
                </MenuItem>
                <MenuItem :href="route('admin.orders.index')" :active="$page.url.startsWith('/admin/orders')">
                    Pedidos
                </MenuItem>
                <MenuItem :href="route('admin.z-reports.index')" :active="$page.url.startsWith('/admin/z-reports')">
                    Reportes
                </MenuItem>
            </ul>
            <UserDropdown />
        </div>
    </div>
</template>
