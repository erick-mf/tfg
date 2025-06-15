<script setup>
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
    <div class="bg-base-200 min-h-screen font-sans">
        <main>
            <slot />
        </main>
    </div>

    <div class="toast toast-end fixed z-[100] text-base" v-if="showToast">
        <div class="alert" :class="'alert-' + toastType">
            <span>{{ toastMessage }}</span>
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
        transform: translateY(20px);
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
        transform: translateY(20px);
    }
}
</style>
