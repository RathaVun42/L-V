<template>
    <div class="mb-6 flex flex-col gap-4 sm:flex-row sm:items-center sm:justify-between">
            <div>
                <h1 >
                    {{title}}
                </h1>

                <p >
                   {{subtitle}}
                </p>
            </div>

            <slot name="button" ></slot>
        </div>
</template>
<script setup>
defineProps({
    title: {
        type: String,
        default: 'Title'
    }, 
    subtitle: {
        type: String,
        default: 'Subtitle'
    }
})
</script>