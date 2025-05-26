<script setup>
import { validatorLocationForm } from '@/lib/validatorLocationForm';
import { useForm } from '@inertiajs/vue3';
import { computed, watch } from 'vue';
import BtnPrimary from './BtnPrimary.vue';
import BtnSidebarRightCancel from './BtnSidebarRightCancel.vue';

const props = defineProps({
    location: {
        type: Object,
        default: null,
    },
});

const form = useForm({
    id: null,
    name: '',
});

const isEditing = computed(() => !!form.id);

watch(
    () => props.location,
    (newLocation) => {
        if (newLocation?.id) {
            Object.assign(form, {
                id: newLocation.id,
                name: newLocation.name || '',
            });
        } else {
            form.clearErrors();
            form.reset();
        }
    },
    { immediate: true },
);

const submitForm = () => {
    let isValid = validatorLocationForm(form);

    if (!isValid) return;

    if (!isEditing.value) {
        form.post(route('admin.locations.store'), {
            onSuccess: () => {
                form.clearErrors();
                form.reset();
                const sidebarRight = document.getElementById('sidebar-right');
                if (sidebarRight) sidebarRight.checked = false;
            },
        });
    } else {
        form.put(route('admin.locations.update', form), {
            onSuccess: () => {
                form.clearErrors();
                form.reset();
                const sidebarRight = document.getElementById('sidebar-right');
                if (sidebarRight) sidebarRight.checked = false;
            },
        });
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
            {{ isEditing ? 'Editar ubicación' : 'Crear ubicación' }}
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
                    :class="{ 'input-error': form.errors.name }"
                />
                <label class="label" v-if="form.errors.name">
                    <span class="text-error text-wrap">{{ form.errors.name }}</span>
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
