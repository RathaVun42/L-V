<template>
    <Teleport to="body">
        <div
            v-if="open"
            class="fixed inset-0 z-50 flex items-center justify-center bg-black/40 p-4"
            @click.self="close"
        >
            <div
                class="w-full max-w-lg rounded-2xl bg-white shadow-xl"
            >
                <!-- Header -->
                <div
                    class="flex items-center justify-between border-b border-gray-200 px-6 py-4"
                >
                    <h2 class="text-lg font-semibold text-gray-900">
                        {{ title }}
                    </h2>

                    <button
                        type="button"
                        class="rounded-lg p-2 text-gray-400 hover:bg-gray-100 hover:text-gray-600"
                        @click="close"
                    >
                        ✕
                    </button>
                </div>

                <!-- Body -->
                <div class="px-6 py-5">
                    <slot />
                </div>

                <!-- Footer -->
                <div
                    v-if="$slots.footer"
                    class="flex justify-end gap-3 border-t border-gray-200 px-6 py-4"
                >
                    <slot name="footer" />
                </div>
            </div>
        </div>
    </Teleport>
</template>

<script setup>
defineProps({
    open: {
        type: Boolean,
        default: false,
    },

    title: {
        type: String,
        default: '',
    },
})

const emit = defineEmits(['close'])

function close() {
    emit('close')
}
</script>