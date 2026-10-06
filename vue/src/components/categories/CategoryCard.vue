<template>
    <div class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm">
        <!-- Image -->
        <div class="mb-4 overflow-hidden rounded-lg">
            <img
                v-if="category.image"
                :src="category.image"
                :alt="category.name"
                class="h-40 w-full object-cover"
            />

            <div
                v-else
                class="flex h-40 items-center justify-center bg-gray-100 text-sm text-gray-400"
            >
                No image
            </div>
        </div>

        <!-- Information -->
        <div>
            <div class="flex items-start justify-between gap-2">
                <h2 class="font-semibold text-gray-900">
                    {{ category.name }}
                </h2>

                <AppBadge
                    :variant="category.is_active ? 'success' : 'danger'"
                    dot
                >
                    {{ category.is_active ? 'Active' : 'Inactive' }}
                </AppBadge>
            </div>

            <p class="mt-2 line-clamp-2 text-sm text-gray-500">
                {{ category.description || 'No description' }}
            </p>
        </div>

        <!-- Actions -->
        <div class="mt-4 flex gap-2">
            <AppButton
                variant="outline"
                size="sm"
                class="flex-1"
                @click="emit('edit', category)"
            >
                Edit
            </AppButton>

            <AppButton
                variant="danger"
                size="sm"
                @click="emit('delete', category)"
            >
                Delete
            </AppButton>
        </div>
    </div>
</template>

<script setup>
import AppButton from '@/components/ui/AppButton.vue'
import AppBadge from '@/components/ui/AppBadge.vue'

const props = defineProps({
    category: {
        type: Object,
        required: true,
    },
})
console.log(props.category.image)

const emit = defineEmits([
    'edit',
    'delete',
])


</script>