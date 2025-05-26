<script setup>
import BtnPrimary from '@/components/BtnPrimary.vue';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { validatorLoginForm } from '@/lib/validatorLoginForm';
import { useForm } from '@inertiajs/vue3';
import { ref } from 'vue';

const form = useForm({
    password: '',
});

const errors = ref({
    password: '',
});

function submit() {
    const isValid = validatorLoginForm(form, errors);

    if (!isValid) return;

    form.post(route('login.submit'), {
        onSuccess: () => form.reset(),
    });
}
</script>

<template>
    <AuthLayout>
        <div class="mx-auto w-full px-4 md:w-2/3 lg:w-1/2 xl:w-1/3 2xl:w-1/4">
            <div class="w-full rounded-lg bg-white p-8 shadow-xl">
                <div class="mb-8 text-center">
                    <a :href="route('login')" class="text-3xl font-semibold">Logo</a>
                </div>

                <form @submit.prevent="submit" class="flex flex-col gap-6">
                    <div class="flex flex-col gap-2">
                        <div class="min-h-[20px]">
                            <p v-if="errors.password" class="text-sm text-red-600">
                                {{ errors.password }}
                            </p>
                        </div>
                        <label class="input w-full text-lg">
                            <input
                                v-model="form.password"
                                type="password"
                                placeholder="Contraseña de acceso"
                                autocomplete="off"
                            />
                        </label>
                    </div>
                    <BtnPrimary type="submit" :processing="form.processing"> Ingresar </BtnPrimary>
                </form>
            </div>
        </div>
    </AuthLayout>
</template>
