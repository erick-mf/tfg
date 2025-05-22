<script setup>
defineProps({
    drawerId: {
        type: String,
        default: 'sidebar-right',
    },
});
</script>

<template>
    <div class="drawer drawer-end">
        <input :id="drawerId" type="checkbox" class="drawer-toggle" />
        <div class="drawer-side z-50">
            <label class="drawer-overlay"></label>
            <div class="menu bg-base-200 text-base-content h-full w-full p-4 sm:w-96">
                <slot name="form"></slot>
            </div>
        </div>
    </div>
</template>
