<script setup>
import BtnPrimary from '@/components/BtnPrimary.vue';
import BtnSidebarRightCancel from '@/components/BtnSidebarRightCancel.vue';
import { validatorUserForm } from '@/lib/validatorUserForm';
import { useForm } from '@inertiajs/vue3';
import { computed, ref, watch } from 'vue';

const props = defineProps({
    user: {
        type: Object,
        default: null,
    },
});

const roles = [
    { value: 'camarero', label: 'Camarero' },
    { value: 'cocinero', label: 'Cocinero' },
    { value: 'encargado', label: 'Encargado' },
];

const form = useForm({
    id: null,
    name: '',
    surnames: '',
    phone: '',
    phone_emergency: '',
    role: '',
    password: '',
});

const errors = ref({
    name: '',
    surnames: '',
    phone: '',
    phone_emergency: '',
    role: '',
    password: '',
});

// Computed para saber si es edición
const isEditing = computed(() => !!form.id);

// Función para resetear errores
const resetErrors = () => {
    Object.keys(errors.value).forEach((key) => {
        errors.value[key] = '';
    });
};

// Función para cerrar sidebar y limpiar
const closeSidebarAndReset = () => {
    const sidebarRight = document.getElementById('sidebar-right');
    if (sidebarRight) sidebarRight.checked = false;
    form.reset();
    resetErrors();
};

watch(
    () => props.user,
    (newUser) => {
        if (newUser?.id) {
            // Usar Object.assign para mayor claridad
            Object.assign(form, {
                id: newUser.id,
                name: newUser.name || '',
                surnames: newUser.surnames || '',
                phone: newUser.phone || '',
                phone_emergency: newUser.phone_emergency || '',
                role: newUser.role || '',
                password: '', // Siempre vacío en edición
            });
        } else {
            form.reset();
            resetErrors();
        }
    },
    { immediate: true },
);

const submitForm = () => {
    const isValid = validatorUserForm(form, errors, props.user);

    if (!isValid) return;

    const successCallback = {
        onSuccess: closeSidebarAndReset,
        onError: (errors) => console.error('Error:', errors),
    };

    if (isEditing.value) {
        // En edición, eliminar password si está vacío
        const updateData = { ...form.data() };
        if (!updateData.password || !updateData.password.trim()) {
            delete updateData.password;
        }

        form.transform(() => updateData).put(route('admin.users.update', form.id), successCallback);
    } else {
        // Creación - enviar todos los datos
        form.post(route('admin.users.store'), successCallback);
    }
};

const cancelForm = () => {
    form.reset();
    resetErrors();
};
</script>

<template>
    <div class="mx-auto my-8 w-full max-w-md">
        <h2 class="mb-6 text-center text-2xl font-bold">
            {{ isEditing ? 'Editar usuario' : 'Crear usuario' }}
        </h2>

        <div class="space-y-4">
            <!-- Nombre -->
            <div class="form-control">
                <label class="label mb-2" for="name">
                    <span>Nombre</span>
                </label>
                <input
                    id="name"
                    v-model="form.name"
                    type="text"
                    placeholder="Ej: Juan"
                    autocomplete="off"
                    class="input input-bordered w-full"
                    :class="{ 'input-error': errors.name }"
                />
                <label class="label" v-if="errors.name">
                    <span class="text-error">{{ errors.name }}</span>
                </label>
            </div>

            <!-- Apellidos -->
            <div class="form-control">
                <label class="label mb-2" for="surnames">
                    <span>Apellidos</span>
                </label>
                <input
                    id="surnames"
                    v-model="form.surnames"
                    type="text"
                    placeholder="Ej: Pérez García"
                    autocomplete="off"
                    class="input input-bordered w-full"
                    :class="{ 'input-error': errors.surnames }"
                />
                <label class="label" v-if="errors.surnames">
                    <span class="text-error">{{ errors.surnames }}</span>
                </label>
            </div>

            <!-- Contraseña (solo en creación) -->
            <div class="form-control" v-if="!isEditing">
                <label class="label mb-2" for="password">
                    <span>Contraseña</span>
                </label>
                <input
                    id="password"
                    v-model="form.password"
                    type="password"
                    placeholder="6 dígitos"
                    autocomplete="new-password"
                    class="input input-bordered w-full"
                    :class="{ 'input-error': errors.password }"
                />
                <label class="label" v-if="errors.password">
                    <span class="text-error">{{ errors.password }}</span>
                </label>
            </div>

            <!-- Teléfono -->
            <div class="form-control">
                <label class="label mb-2" for="phone">
                    <span>Teléfono</span>
                </label>
                <input
                    id="phone"
                    v-model="form.phone"
                    type="tel"
                    placeholder="Ej: 612345678"
                    autocomplete="tel"
                    maxlength="9"
                    class="input input-bordered w-full"
                    :class="{ 'input-error': errors.phone }"
                />
                <label class="label" v-if="errors.phone">
                    <span class="text-error">{{ errors.phone }}</span>
                </label>
            </div>

            <!-- Teléfono de emergencia -->
            <div class="form-control">
                <label class="label mb-2" for="phone_emergency">
                    <span>Teléfono de emergencia (opcional)</span>
                </label>
                <input
                    id="phone_emergency"
                    v-model="form.phone_emergency"
                    type="tel"
                    placeholder="Ej: 612345678"
                    autocomplete="tel"
                    maxlength="9"
                    class="input input-bordered w-full"
                    :class="{ 'input-error': errors.phone_emergency }"
                />
                <label class="label" v-if="errors.phone_emergency">
                    <span class="text-error">{{ errors.phone_emergency }}</span>
                </label>
            </div>

            <!-- Rol -->
            <div class="form-control">
                <label class="label mb-2" for="role">
                    <span>Rol</span>
                </label>
                <select
                    id="role"
                    v-model="form.role"
                    class="select select-bordered w-full"
                    :class="{ 'select-error': errors.role }"
                >
                    <option disabled value="">Seleccione un rol</option>
                    <option v-for="role in roles" :key="role.value" :value="role.value">
                        {{ role.label }}
                    </option>
                </select>
                <label class="label" v-if="errors.role">
                    <span class="text-error">{{ errors.role }}</span>
                </label>
            </div>

            <!-- Botones -->
            <div class="pt-4 sm:grid sm:grid-cols-2 sm:gap-4">
                <BtnSidebarRightCancel @click="cancelForm" />
                <BtnPrimary type="button" @click="submitForm" :disabled="form.processing">
                    {{ isEditing ? 'Actualizar' : 'Guardar' }}
                </BtnPrimary>
            </div>
        </div>
    </div>
</template>
