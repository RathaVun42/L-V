<template>
    <div class="overflow-hidden rounded-xl border border-gray-200 bg-white shadow-sm">
        <!-- Image -->
        <div class="overflow-hidden">
            <img
                v-if="product.image"
                :src="`${product.image}`"
                :alt="product.name"
                class="h-44 w-full object-cover"
            />

            <div
                v-else
                class="flex h-44 items-center justify-center bg-gray-100 text-sm text-gray-400"
            >
                No image
            </div>
        </div>

        <!-- Content -->
        <div class="p-4">
            <div class="flex items-start justify-between gap-3">
                <div class="min-w-0">
                    <h2 class="truncate font-semibold text-gray-900">
                        {{ product.name }}
                    </h2>

                    <p class="mt-1 text-xs text-gray-500">
                        {{ product.category?.name || 'No category' }}
                    </p>
                </div>

                <AppBadge
                    :variant="product.is_active ? 'success' : 'danger'"
                    dot
                >
                    {{ product.is_active ? 'Active' : 'Inactive' }}
                </AppBadge>
            </div>

            <p class="mt-3 line-clamp-2 text-sm text-gray-500">
                {{ product.description || 'No description' }}
            </p>

            <!-- Actions -->
            <div class="mt-4 flex gap-2">
                <AppButton
                    variant="outline"
                    size="sm"
                    class="flex-1"
                    @click="emit('edit', product)"
                >
                    Edit
                </AppButton>

                <AppButton
                    variant="danger"
                    size="sm"
                    @click="emit('delete', product)"
                >
                    Delete
                </AppButton>
            </div>
        </div>
    </div>
</template>

<script setup>
import AppButton from '@/components/ui/AppButton.vue'
import AppBadge from '@/components/ui/AppBadge.vue'

defineProps({
    product: {
        type: Object,
        required: true,
    },
})

const emit = defineEmits(['edit', 'delete'])

</script>