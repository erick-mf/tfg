<script setup>
import BtnPrimary from '@/components/BtnPrimary.vue';
import AuthLayout from '@/layouts/AuthLayout.vue';
import { useForm } from '@inertiajs/vue3';

const form = useForm({
    password: '',
});

function submit() {
    if (form.password.length !== 6) {
        form.setError('password', 'La contraseña es incorrecta');
    } else {
        form.password = form.password.toUpperCase();
        form.post(route('login.submit'));
    }
}
</script>

<template>
    <AuthLayout>
        <div class="mx-auto w-full px-4 md:w-2/3 lg:w-1/2 xl:w-1/3 2xl:w-1/4">
            <div class="w-full rounded-lg bg-white p-8 shadow-xl">
                <div class="mb-8 text-center">
                    <a :href="route('login')" class="text-3xl font-semibold">Logo</a>
                </div>

                <form @submit.prevent="submit" class="flex flex-col gap-2">
                    <div class="min-h-[20px]">
                        <p v-if="form.errors.password" class="text-sm text-red-600">
                            {{ form.errors.password }}
                        </p>
                    </div>

                    <div class="flex flex-col gap-4">
                        <label class="input w-full text-lg">
                            <input
                                v-model="form.password"
                                type="password"
                                placeholder="Contraseña de acceso"
                                autocomplete="off"
                            />
                        </label>

                        <div>
                            <BtnPrimary type="submit" :processing="form.processing"> Ingresar </BtnPrimary>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </AuthLayout>
</template>
