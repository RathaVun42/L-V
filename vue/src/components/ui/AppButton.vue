<template>
    <button :type="type" :disabled="disabled || loading" :class="[
        baseClasses,
        variantClasses[variant],
        sizeClasses[size],
    ]" @click="$emit('click', $event)">
        <!--click is the name of event we defined with definedEmits(['click']) 
        about $event is the event of click that we will sent to parent,
        we also can use or emit(send) back other data to parent

        The function that we use for pasting on this component will be able use the emit data as is param
        Vue internally treats the emitted value as $event.

        function handleDelete(categoryName) { categoryName is the emitted data
            console.log('Delete:', categoryName)
        }
    
    -->
        <span v-if="loading" class="h-4 w-4 animate-spin rounded-full border-2 border-current border-t-transparent">
        </span>
        <slot v-if="!loading" name="icon"></slot> <!-- this is called named slot -->
        <!-- Button content -->
        <span v-if="!loading">
            <slot /> <!--is a placeholder for parent to paste html here (normal slot) -->
            <!-- 

            <AppButton>
                <template #icon> example of using named slote
                                 #icon is shorthand for v-slot:icon
                    +
                </template>

Add Category example of normal slot
</AppButton>
-->
        </span>

    </button>
</template>
<script setup>
defineProps({
    type: {
        type: String,
        default: 'button',
    },

    variant: {
        type: String,
        default: 'primary',
    },

    size: {
        type: String,
        default: 'md',
    },

    loading: {
        type: Boolean,
        default: false,
    },

    disabled: {
        type: Boolean,
        default: false,
    },
})

defineEmits(['click'])

const baseClasses =
    'inline-flex items-center justify-center gap-2 rounded-lg font-semibold transition ' +
    'focus:outline-none focus:ring-2 focus:ring-offset-2 ' +
    'disabled:cursor-not-allowed disabled:opacity-60'

const variantClasses = {
    primary:
        'bg-green-700 text-white hover:bg-green-800 focus:ring-green-500',

    secondary:
        'bg-gray-100 text-gray-700 hover:bg-gray-200 focus:ring-gray-400',

    danger:
        'bg-red-600 text-white hover:bg-red-700 focus:ring-red-500',

    warning:
        'bg-yellow-500 text-white hover:bg-yellow-600 focus:ring-yellow-400',

    outline:
        'border border-gray-300 bg-white text-gray-700 hover:bg-gray-50 focus:ring-gray-400',

    ghost:
        'text-gray-600 hover:bg-gray-100 focus:ring-gray-400',
}

const sizeClasses = {
    sm: 'px-3 py-2 text-xs',
    md: 'px-4 py-2.5 text-sm',
    lg: 'px-6 py-3 text-base',
}
</script>