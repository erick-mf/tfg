<script setup>
import Hamburguer from '@/components/Hamburguer.vue';
import Sidebar from '@/components/Sidebar.vue';
import { usePage } from '@inertiajs/vue3';
import { onMounted, ref, watch } from 'vue';

const drawerOpen = ref(false);
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
    <div class="bg-base-100 min-h-screen">
        <div class="drawer lg:drawer-open">
            <input id="admin-drawer" type="checkbox" class="drawer-toggle" v-model="drawerOpen" />
            <div class="drawer-content flex h-screen flex-col">
                <header
                    class="bg-base-100 border-base-300 sticky top-0 z-4 flex h-16 flex-shrink-0 items-center justify-between border-b px-6"
                >
                    <Hamburguer />
                    <slot name="header"></slot>
                </header>
                <main class="flex min-h-0 flex-1 flex-col p-6">
                    <slot name="content"></slot>
                </main>
            </div>
            <Sidebar />
        </div>
    </div>

    <div class="toast toast-end fixed z-[100] text-base" v-if="showToast">
        <div :class="[`alert`, `alert-${toastType}`]">
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
    }
    10% {
        opacity: 1;
    }
    80% {
        opacity: 1;
    }
    100% {
        opacity: 0;
    }
}
</style>
