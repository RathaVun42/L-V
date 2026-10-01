<template>
    <div
        class="flex items-center justify-center"
        :class="fullPage ? 'min-h-[300px]' : 'py-10'"
    >
        <div class="flex flex-col items-center gap-3">
            <div
                class="h-8 w-8 animate-spin rounded-full border-4 border-gray-200 border-t-green-600"
            ></div>

            <p class="text-sm text-gray-500">
                {{ text }}
            </p>
        </div>
    </div>
</template>

<script setup>
defineProps({
    text: {
        type: String,
        default: 'Loading...',
    },

    fullPage: {
        type: Boolean,
        default: false,
    },
})
</script>