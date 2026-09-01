<template>
    <div class="absolute inset-0">
        <Lightfall />
    </div>
    <div class="absolute inset-0 bg-black/30"></div>
    <RouterView></RouterView>
</template>
<script setup>
    import Lightfall from '@/components/Lightfall.vue';
</script>