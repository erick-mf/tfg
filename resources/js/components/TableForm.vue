<script setup>
import BtnPrimary from '@/components/BtnPrimary.vue';
import BtnSidebarRightCancel from '@/components/BtnSidebarRightCancel.vue';
import { validatorTableForm } from '@/lib/validatorTableForm';
import { useForm } from '@inertiajs/vue3';
import { computed, watch } from 'vue';

const props = defineProps({
    table: {
        type: Object,
        default: null,
    },
    categories: {
        type: Object,
        default: null,
    },
});

const form = useForm({
    id: null,
    name: '',
    status: '',
});

const isEditing = computed(() => !!form.id);

watch(
    () => props.table,
    (newTable) => {
        if (newTable?.id) {
            Object.assign(form, {
                id: newTable.id,
                name: newTable.name || '',
                status: newTable.status || '',
            });
        } else {
            form.clearErrors();
            form.reset();
        }
    },
    { immediate: true },
);

const submitForm = () => {
    const isValid = validatorTableForm(form);

    if (!isValid) return;

    const optionsForm = {
        forceformdata: true,
        onSuccess: () => {
            form.clearErrors();
            form.reset();
            const sidebarRight = document.getElementById('sidebar-right');
            if (sidebarRight) sidebarRight.checked = false;
        },
    };

    if (!isEditing.value) {
        form.post(route('admin.tables.store'), optionsForm);
    } else {
        form.put(route('admin.tables.update', form), optionsForm);
    }
};

const cancelForm = () => {
    form.clearErrors();
    form.reset();
};
</script>

<template>
    <div class="mx-auto my-8 w-full max-w-md">
        <h2 class="mb-6 text-center text-2xl font-bold">
            {{ isEditing ? 'Editar Mesa' : 'Crear Mesa' }}
        </h2>

        <div class="space-y-4">
            <div class="form-control">
                <label class="label mb-2" for="name">
                    <span>Nombre</span>
                </label>
                <input
                    id="name"
                    v-model="form.name"
                    type="text"
                    placeholder="Ej: Mesa 1"
                    autocomplete="off"
                    class="input input-bordered w-full"
                    :class="{ 'input-error': form.errors.name }"
                />
                <label class="label" v-if="form.errors.name">
                    <span class="text-error">{{ form.errors.name }}</span>
                </label>
            </div>

            <div class="form-control">
                <label class="label mb-2" for="status">
                    <span>Estado</span>
                </label>
                <select
                    id="status"
                    v-model="form.status"
                    class="select select-bordered w-full"
                    :class="{ 'select-error': form.errors.status }"
                >
                    <option disabled value="">Seleccione una categoría</option>
                    <option value="disponible">Disponible</option>
                    <option value="ocupada">Ocupada</option>
                    <option value="reservada">Reservada</option>
                    <option value="en limpieza">En limpieza</option>
                </select>
                <label class="label" v-if="form.errors.status">
                    <span class="text-error">{{ form.errors.status }}</span>
                </label>
            </div>

            <div class="pt-4 sm:grid sm:grid-cols-2 sm:gap-4">
                <BtnSidebarRightCancel @click="cancelForm" />
                <BtnPrimary type="button" @click="submitForm" :disabled="form.processing">
                    {{ isEditing ? 'Actualizar' : 'Guardar' }}
                </BtnPrimary>
            </div>
        </div>
    </div>
</template>
