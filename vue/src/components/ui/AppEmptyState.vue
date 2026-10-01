<template>
    <div
        class="flex flex-col items-center justify-center rounded-xl border border-dashed border-gray-300 bg-gray-50 px-6 py-12 text-center"
    >
        <!-- Icon -->
        <div
            class="mb-4 flex h-12 w-12 items-center justify-center rounded-full bg-gray-100 text-gray-400"
        >
            <slot name="icon">
                <span class="text-xl">∅</span>
            </slot>
        </div>

        <!-- Title -->
        <h3 class="text-base font-semibold text-gray-900">
            {{ title }}
        </h3>

        <!-- Description -->
        <p
            v-if="description"
            class="mt-1 max-w-md text-sm text-gray-500"
        >
            {{ description }}
        </p>

        <!-- Action -->
        <div v-if="$slots.action" class="mt-5">
            <slot name="action" />
        </div>
    </div>
</template>

<script setup>
defineProps({
    title: {
        type: String,
        default: 'No data found',
    },

    description: {
        type: String,
        default: '',
    },
})
</script>