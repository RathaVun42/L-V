<template>
    <span
        :class="[
            'inline-flex items-center rounded-full font-medium',
            variantClasses[variant],
            sizeClasses[size],
        ]"
    >
        <span
            v-if="dot"
            class="mr-1.5 h-1.5 w-1.5 rounded-full bg-current"
        ></span>

        <slot />
    </span>
</template>

<script setup>
defineProps({
    variant: {
        type: String,
        default: 'default',
    },

    size: {
        type: String,
        default: 'md',
    },

    dot: {
        type: Boolean,
        default: false,
    },
})

const variantClasses = {
    default: 'bg-gray-100 text-gray-700',
    success: 'bg-green-100 text-green-700',
    warning: 'bg-yellow-100 text-yellow-700',
    danger: 'bg-red-100 text-red-700',
    info: 'bg-blue-100 text-blue-700',
}

const sizeClasses = {
    sm: 'px-2 py-0.5 text-xs',
    md: 'px-2.5 py-1 text-xs',
    lg: 'px-3 py-1.5 text-sm',
}
</script>