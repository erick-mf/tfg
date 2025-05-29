<script setup>
import ConfirmationDeleteModal from '@/components/ConfirmationDeleteModal.vue';
import EditAction from '@/components/EditAction.vue';
import Pagination from '@/components/Pagination.vue';
import { router } from '@inertiajs/vue3';
import { Phone, ShieldAlert, User } from 'lucide-vue-next';
import { computed } from 'vue';

const props = defineProps({
    content: {
        type: Object,
        required: true,
    },
    deleteAction: {
        type: String,
        required: true,
        default: '',
    },
});

const data = computed(() => props.content.data);
const pagination = computed(() => props.content);

const emit = defineEmits(['edit-item']);

function editItem(item) {
    emit('edit-item', item);
}

function deleteItem(item, action) {
    router.delete(route(`${action}`, item), {
        preserveScroll: true,
    });
}

function firstLetter(string) {
    return string.charAt(0).toUpperCase();
}
</script>

<template>
    <div class="block sm:hidden">
        <div class="space-y-3">
            <div
                v-for="(item, itemIndex) in data"
                :key="itemIndex"
                class="card bg-base-100 border-base-200 border shadow transition-shadow duration-200 hover:shadow-md"
            >
                <div class="card-body p-4">
                    <div class="flex items-start gap-3">
                        <!-- Avatar simple -->
                        <div
                            class="bg-primary/15 text-primary flex h-10 w-10 flex-shrink-0 items-center justify-center rounded-full"
                        >
                            <span class="text-sm font-medium">
                                {{ firstLetter(item.name) || '?' }}
                            </span>
                        </div>

                        <div class="min-w-0 flex-1">
                            <!-- Nombre -->
                            <h3 class="text-base-content mb-3 text-base font-semibold">
                                {{ item.name || 'Sin nombre' }}
                            </h3>

                            <!-- Información -->
                            <div class="space-y-2">
                                <div class="flex items-center gap-2 text-sm">
                                    <User class="text-base-content/60 h-4 w-4" />
                                    <span class="text-base-content/60">{{ item.role || 'No asignado' }}</span>
                                </div>

                                <div class="flex items-center gap-2 text-sm">
                                    <Phone class="text-base-content/60 h-4 w-4" />
                                    <span class="text-base-content/80">{{ item.phone || 'No registrado' }}</span>
                                </div>

                                <div class="flex items-center gap-2 text-sm">
                                    <ShieldAlert class="text-base-content/60 h-4 w-4" />
                                    <span class="text-base-content/80">{{
                                        item.phone_emergency || 'No registrado'
                                    }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Botones de acción -->
                    <div class="mt-4 flex justify-end gap-2">
                        <EditAction @click="editItem(item)" />
                        <ConfirmationDeleteModal :item="item" :action="deleteAction" @delete="deleteItem" />
                    </div>
                </div>
            </div>
        </div>

        <Pagination :pagination="pagination" />
    </div>
</template>
