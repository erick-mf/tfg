<script setup>
import BtnPrimary from '@/components/BtnPrimary.vue';
import BtnSidebarRightCancel from '@/components/BtnSidebarRightCancel.vue';
import { validatorMenuItemForm } from '@/lib/validatorMenuItemForm';
import { useForm } from '@inertiajs/vue3';
import { computed, watch } from 'vue';

const props = defineProps({
    menuItem: {
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
    price: '',
    is_available: false,
    image_path: null,
    menu_category_id: '',
    _method: 'POST',
});

const isEditing = computed(() => !!form.id);

watch(
    () => props.menuItem,
    (newMenuItem) => {
        if (newMenuItem?.id) {
            Object.assign(form, {
                id: newMenuItem.id,
                name: newMenuItem.name || '',
                price: newMenuItem.price || '',
                is_available: newMenuItem.is_available || false,
                image_path: newMenuItem.image_path || null,
                menu_category_id: newMenuItem.menu_category_id || '',
                _method: 'PUT',
            });
        } else {
            form.clearErrors();
            form.reset();
        }
    },
    { immediate: true },
);

const submitForm = () => {
    const isValid = validatorMenuItemForm(form);

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

    let targetRoute = '';

    if (!isEditing.value) {
        targetRoute = route('admin.menu-items.store');
    } else {
        targetRoute = route('admin.menu-items.update', form);
    }

    form.post(targetRoute, optionsForm);
};

const cancelForm = () => {
    form.clearErrors();
    form.reset();
};
</script>

<template>
    <div class="mx-auto my-8 w-full max-w-md">
        <h2 class="mb-6 text-center text-2xl font-bold">
            {{ isEditing ? 'Editar Item' : 'Crear Item' }}
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
                <label class="label mb-2" for="price">
                    <span>Precio</span>
                </label>
                <input
                    id="price"
                    v-model="form.price"
                    type="number"
                    placeholder="Ej: 9.99"
                    autocomplete="off"
                    min="0"
                    class="input input-bordered w-full"
                    :class="{ 'input-error': form.errors.price }"
                />
                <label class="label" v-if="form.errors.price">
                    <span class="text-error">{{ form.errors.price }}</span>
                </label>
            </div>

            <div class="form-control">
                <label class="label mb-2" for="menu_category_id">
                    <span>Categoría</span>
                </label>
                <select
                    id="menu_category_id"
                    v-model="form.menu_category_id"
                    class="select select-bordered w-full"
                    :class="{ 'select-error': form.errors.menu_category_id }"
                >
                    <option disabled value="">Seleccione una categoría</option>
                    <option v-for="category in categories" :key="category.id" :value="category.id">
                        {{ category.name }}
                    </option>
                </select>
                <label class="label" v-if="form.errors.menu_category_id">
                    <span class="text-error">{{ form.errors.menu_category_id }}</span>
                </label>
            </div>

            <div class="form-control">
                <label class="label mb-2" for="image_path">
                    <span>Imagen</span>
                </label>
                <input
                    id="image_path"
                    @change="form.image_path = $event.target.files[0]"
                    type="file"
                    autocomplete="off"
                    accept="image/jpeg, image/png, image/jpg, image/webp"
                    class="file-input w-full"
                    :class="{ 'input-error': form.errors.image_path }"
                />
                <label class="label" v-if="form.errors.image_path">
                    <span class="text-error">{{ form.errors.image_path }}</span>
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

            <div class="pt-4 sm:grid sm:grid-cols-2 sm:gap-4">
                <BtnSidebarRightCancel @click="cancelForm" />
                <BtnPrimary type="button" @click="submitForm" :disabled="form.processing">
                    {{ isEditing ? 'Actualizar' : 'Guardar' }}
                </BtnPrimary>
            </div>
        </div>
    </div>
</template>
