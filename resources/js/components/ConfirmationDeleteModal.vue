<script setup>
import { Trash2 } from 'lucide-vue-next';
import { ref } from 'vue';

const props = defineProps({
    item: {
        type: Object,
        required: true,
    },
    action: {
        type: String,
        required: true,
    },
});

const emit = defineEmits(['delete']);

const dialog = ref(null);

function openModal() {
    dialog.value.showModal();
}

function handleDelete() {
    emit('delete', props.item, props.action);
    dialog.value.close();
}
</script>

<template>
    <button class="btn btn-sm btn-ghost text-error tooltip tooltip-bottom" data-tip="Eliminar" @click="openModal">
        <Trash2 class="h-5 w-5" />
    </button>

    <dialog ref="dialog" id="my_modal_5" class="modal modal-middle">
        <div class="modal-box">
            <div class="flex flex-col items-start">
                <h3 class="text-lg font-bold">¡Atención!</h3>
                <p class="py-4">
                    ¿Seguro que quieres eliminar <strong class="truncate">{{ item.name }}</strong
                    >?
                </p>
            </div>
            <div class="modal-action">
                <form method="dialog">
                    <button class="btn mr-2">Cerrar</button>
                    <button class="btn btn-error" @click="handleDelete">Eliminar</button>
                </form>
            </div>
        </div>
    </dialog>
</template>
