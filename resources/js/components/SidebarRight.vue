<script setup></script>

<template>
    <div class="drawer drawer-end">
        <input id="sidebar-right" type="checkbox" class="drawer-toggle" />
        <div class="drawer-side z-50">
            <label class="drawer-overlay"></label>
            <div class="menu bg-base-200 text-base-content min-h-full w-full p-4 sm:w-[28rem]">
                <slot name="form"></slot>
            </div>
        </div>
    </div>
</template>
