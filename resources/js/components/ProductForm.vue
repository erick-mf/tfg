<script setup>
import BtnPrimary from '@/components/BtnPrimary.vue';
import BtnSidebarRightCancel from '@/components/BtnSidebarRightCancel.vue';
import { validatorProductForm } from '@/lib/validatorProductForm';
import { useForm } from '@inertiajs/vue3';
import { computed, watch } from 'vue';

const props = defineProps({
    product: {
        type: Object,
        default: null,
    },
    locations: {
        type: Object,
        default: null,
    },
});

const form = useForm({
    id: null,
    name: '',
    is_available: false,
    location_id: '',
});

const isEditing = computed(() => !!form.id);

watch(
    () => props.product,
    (newProduct) => {
        if (newProduct?.id) {
            Object.assign(form, {
                id: newProduct.id,
                name: newProduct.name || '',
                is_available: newProduct.is_available || false,
                location_id: newProduct.location_id || '',
            });
        } else {
            form.clearErrors();
            form.reset();
        }
    },
    { immediate: true },
);

const submitForm = () => {
    const isValid = validatorProductForm(form);

    if (!isValid) return;

    if (!isEditing.value) {
        form.post(route('admin.products.store'), {
            onSuccess: () => {
                form.clearErrors();
                form.reset();
                const sidebarRight = document.getElementById('sidebar-right');
                if (sidebarRight) sidebarRight.checked = false;
            },
        });
    } else {
        form.put(route('admin.products.update', form), {
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
            {{ isEditing ? 'Editar Producto' : 'Crear Producto' }}
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
                    placeholder="Ej: Vino"
                    autocomplete="off"
                    class="input input-bordered w-full"
                    :class="{ 'input-error': form.errors.name }"
                />
                <label class="label" v-if="form.errors.name">
                    <span class="text-error">{{ form.errors.name }}</span>
                </label>
            </div>

            <div class="form-control">
                <label class="label mb-2 cursor-pointer justify-start gap-3" for="is_available">
                    <input
                        id="is_available"
                        v-model="form.is_available"
                        type="checkbox"
                        class="checkbox"
                        :class="[form.is_available ? 'checkbox-success' : 'checkbox-error']"
                    />
                    <span class="label-text">{{ form.is_available ? 'Disponible' : 'No disponible' }}</span>
                </label>
                <label class="label" v-if="form.errors.is_available">
                    <span class="text-error">{{ form.errors.is_available }}</span>
                </label>
            </div>

            <div class="form-control">
                <label class="label mb-2" for="location_id">
                    <span>Ubicación</span>
                </label>
                <select
                    id="location_id"
                    v-model="form.location_id"
                    class="select select-bordered w-full"
                    :class="{ 'select-error': form.errors.location_id }"
                >
                    <option disabled value="">Seleccione una ubicación</option>
                    <option v-for="location in locations" :key="location.id" :value="location.id">
                        {{ location.name }}
                    </option>
                </select>
                <label class="label" v-if="form.errors.location_id">
                    <span class="text-error">{{ form.errors.location_id }}</span>
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
