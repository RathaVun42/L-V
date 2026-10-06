<template>
    <div
        class="rounded-xl border border-gray-200 bg-white p-4 shadow-sm"
        :class="{
            'border-green-500 ring-1 ring-green-500': selected,
        }"
    >
        <div class="flex gap-4">
            <!-- Product image -->
            <div class="h-20 w-20 shrink-0 overflow-hidden rounded-lg">
                <img
                    v-if="product.image"
                    :src="`${product.image}`"
                    :alt="product.name"
                    class="h-full w-full object-cover"
                />

                <div
                    v-else
                    class="flex h-full w-full items-center justify-center bg-gray-100 text-xs text-gray-400"
                >
                    No image
                </div>
            </div>

            <!-- Product information -->
            <div class="min-w-0 flex-1">
                <div class="flex items-start justify-between gap-3">
                    <div>
                        <h3 class="font-semibold text-gray-900">
                            {{ product.name }}
                        </h3>

                        <p class="mt-1 text-xs text-gray-500">
                            {{ product.category?.name || 'No category' }}
                        </p>
                    </div>

                    <input
                        type="checkbox"
                        :checked="selected"
                        class="h-5 w-5 rounded border-gray-300 text-green-700 focus:ring-green-500"
                        @change="toggle"
                    />
                </div>

                <!-- Menu settings -->
                <div
                    v-if="selected"
                    class="mt-4 grid grid-cols-1 gap-3 sm:grid-cols-2"
                >
                    <AppInput
                        :model-value="price"
                        type="number"
                        label="Price"
                        placeholder="0.00"
                        min="0"
                        step="0.01"
                        @update:model-value="
                            emit('update:price', $event)
                        "
                    />

                    <label class="flex items-center gap-2 self-end pb-2">
                        <input
                            type="checkbox"
                            :checked="available"
                            class="h-4 w-4 rounded border-gray-300 text-green-700 focus:ring-green-500"
                            @change="
                                emit(
                                    'update:available',
                                    $event.target.checked
                                )
                            "
                        />

                        <span class="text-sm font-medium text-gray-700">
                            Available
                        </span>
                    </label>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import AppInput from '@/components/ui/AppInput.vue'

const props = defineProps({
    product: {
        type: Object,
        required: true,
    },

    selected: {
        type: Boolean,
        default: false,
    },

    price: {
        type: [String, Number],
        default: '',
    },

    available: {
        type: Boolean,
        default: true,
    },
})

const emit = defineEmits([
    'toggle',
    'update:price',
    'update:available',
])


function toggle() {
    emit('toggle', props.product)
}
</script>