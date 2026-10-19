<template>
    <div 
    :class="[
        'rounded-2xl w-50 border border-gray-200 bg-white p-5 shadow-sm transition hover:shadow-md',
        clickable && 'cursor-pointer hover:shadow-md'
    ]"
    @click="clickable && emit('click')"
    >
        <div class="flex items-center gap-3 ">
            <div class="flex h-13 w-13 shrink-0 items-center justify-center rounded-2xl bg-blue-50 text-blue-600">
                <i :class="icon" class="text-2xl" aria-hidden="true"></i>
            </div>
            <div class="min-w-0 flex-1">
                <p>
                    {{title}}
                </p>
                <h1>
                    {{total}}
                </h1>
            </div>

        </div>
        <div class="border-t-[1px] mt-4 border-gray-500">
            <p>
                {{description}}
            </p>
        </div>
    </div>
</template>
<script setup>
defineProps({
    total: {
        type: Number,
        default: 0
    },
    title: {
        type: String,
        default: ""
    },
    description: {
        type: String,
        default: "Description"
    },
    icon: {
        type: String,
        default: 'fa-solid fa-calendar-check'
    },
    clickable: {
        type: Boolean,
        default: false
    }
})
const emit = defineEmits(['click'])
</script>