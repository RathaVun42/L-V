<template>
    <button
        type="button"
        :class="[
            'rounded-full px-4 py-2 text-sm font-medium transition',
            'focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-1',
            active
                ? 'bg-green-700 text-white'
                : 'bg-gray-100 text-gray-700 hover:bg-gray-200',
        ]"
        @click="$emit('click')"
    >
        <slot />
    </button>
</template>

<script setup>
defineProps({
    active: {
        type: Boolean,
        default: false,
    },
})

defineEmits(['click'])
</script>