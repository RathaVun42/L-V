<template></template>
<script setup>
    import { logout } from '@/services/auth';
    import { onMounted } from 'vue';
    import { useRouter } from 'vue-router';
    import { userStore } from '@/stores/user';
    const store = userStore();
    const router = useRouter();

    onMounted(()=>{
        const token = store.getSanctumToken();
        logout(token);
        store.reset();
        router.replace({name: 'login'})
    })
</script>