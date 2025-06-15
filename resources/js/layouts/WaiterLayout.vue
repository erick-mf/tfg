<script setup>
import UserDropdown from '@/components/UserDropdown.vue'; // Importamos el componente de usuario
import { usePage } from '@inertiajs/vue3';
import { onMounted, ref, watch } from 'vue';

const page = usePage();
const showToast = ref(false);
const toastMessage = ref('');
const toastType = ref('');

const displayToast = () => {
    if (page.props.flash?.toast) {
        showToast.value = true;
        toastMessage.value = page.props.flash.toast.message;
        toastType.value = page.props.flash.toast.type || 'info';
        setTimeout(() => {
            showToast.value = false;
        }, 3000);
    }
};

onMounted(() => {
    displayToast();
});

watch(
    () => page.props.flash,
    (newValue) => {
        if (newValue?.toast) {
            displayToast();
        }
    },
    { deep: true },
);
</script>

<template>
    <div data-theme="light" class="bg-base-200 min-h-screen">
        <div class="flex h-screen flex-col">
            <header class="bg-base-100/90 border-base-300 sticky top-0 z-4 border-b p-3 shadow-sm backdrop-blur-sm">
                <div class="mx-auto flex w-full max-w-7xl items-center justify-between px-2">
                    <div class="flex items-center gap-4">
                        <h1 class="text-xl font-bold">Mapa de Mesas</h1>
                    </div>

                    <div class="flex items-center gap-3">
                        <UserDropdown direction="bottom" />
                    </div>
                </div>
            </header>

            <main class="flex-1 overflow-y-auto">
                <div class="mx-auto max-w-7xl p-4 sm:p-6">
                    <slot></slot>
                </div>
            </main>
        </div>

        <div class="toast toast-top toast-center z-50 text-base" v-if="showToast">
            <div class="alert shadow-lg" :class="`alert-${toastType}`">
                <span>{{ toastMessage }}</span>
            </div>
        </div>
    </div>
</template>

<style scoped>
.toast {
    transition: opacity 0.3s ease-in-out;
    animation: fadeInOut 3s forwards;
}

@keyframes fadeInOut {
    0% {
        opacity: 0;
        transform: translateY(-20px);
    }
    10% {
        opacity: 1;
        transform: translateY(0);
    }
    90% {
        opacity: 1;
        transform: translateY(0);
    }
    100% {
        opacity: 0;
        transform: translateY(-20px);
    }
}
</style>
