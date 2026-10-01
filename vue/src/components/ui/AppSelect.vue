<template>
    <div class="w-full">
        <label
            v-if="label"
            :for="id"
            class="mb-2 block text-sm font-medium text-gray-700"
        >
            {{ label }}
            <span v-if="required" class="text-red-500">*</span>
        </label>

        <select
            :id="id"
            :value="modelValue"
            :disabled="disabled"
            :required="required"
            :class="[
                'h-11 w-full rounded-lg border bg-white px-3 text-sm',
                'outline-none transition',
                'disabled:cursor-not-allowed disabled:bg-gray-100',
                error
                    ? 'border-red-500 focus:border-red-500 focus:ring-2 focus:ring-red-100'
                    : 'border-gray-300 focus:border-green-600 focus:ring-2 focus:ring-green-100',
            ]"
            @change="$emit('update:modelValue', $event.target.value)"  
        >
        <!-- 
            we named emit event update:modelValue here, then the parent will can use v-model to listen the value or event we emitted
        -->
            <option value="" disabled>
                {{ placeholder }}
            </option>

            <option
                v-for="option in options"
                :key="option.value"
                :value="option.value"
            >
                {{ option.label }}
            </option>
        </select>

        <p v-if="error" class="mt-1 text-sm text-red-500">
            {{ error }}
        </p>

        <p v-else-if="help" class="mt-1 text-sm text-gray-500">
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

    options: {
        type: Array,
        default: () => [],
    },

    id: {
        type: String,
        default: '',
    },

    label: {
        type: String,
        default: '',
    },

    placeholder: {
        type: String,
        default: 'Select an option',
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

    required: {
        type: Boolean,
        default: false,
    },
})

defineEmits(['update:modelValue'])
</script>