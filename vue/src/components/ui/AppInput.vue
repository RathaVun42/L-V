<template>
    <div class="w-full">
        <!-- Label -->
        <label
            v-if="label"
            :for="id"
            class="mb-2 block text-sm font-medium text-gray-700"
        >
            {{ label }}

            <span v-if="required" class="text-red-500">*</span>
        </label>

        <!-- Input -->
        <input
            :id="id"
            :type="type"
            :value="modelValue"
            :placeholder="placeholder"
            :disabled="disabled"
            :readonly="readonly"
            :required="required"
            :class="[
                'h-11 w-full rounded-lg border bg-white px-3 text-sm',
                'outline-none transition',
                'placeholder:text-gray-400',
                'disabled:cursor-not-allowed disabled:bg-gray-100',
                error
                    ? 'border-red-500 focus:border-red-500 focus:ring-2 focus:ring-red-100'
                    : 'border-gray-300 focus:border-green-600 focus:ring-2 focus:ring-green-100',
            ]"
            @input="$emit('update:modelValue', $event.target.value)"
        />

        <!-- Error -->
        <p
            v-if="error"
            class="mt-1 text-sm text-red-500"
        >
            {{ error?.[0] }}
        </p>

        <!-- Help text -->
        <p
            v-else-if="help"
            class="mt-1 text-sm text-gray-500"
        >
            {{ help }}
        </p>
    </div>
</template>

<script setup>
defineProps({
    modelValue: {
        type: [String, Number],
        default: '',
    },
//  for modelValue it is the value of input field
    id: {
        type: String,
        default: '',
    },

    label: {
        type: String,
        default: '',
    },

    type: {
        type: String,
        default: 'text',
    },

    placeholder: {
        type: String,
        default: '',
    },

    error: {
        type: String,
        default: '',
    },

    help: {
        type: String,
        default: '',
    },

    disabled: {
        type: Boolean,
        default: false,
    },

    readonly: {
        type: Boolean,
        default: false,
    },

    required: {
        type: Boolean,
        default: false,
    },
})

defineEmits(['update:modelValue'])
</script>