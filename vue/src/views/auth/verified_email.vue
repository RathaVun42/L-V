<template>
    <div class=" flex min-h-screen items-center justify-center bg-gray-100">
        <div class="z-10 w-full max-w-md rounded-xl bg-white p-8 shadow-lg">

            <h1 class="text-center text-2xl font-bold">
                You have succeeded registering.
            </h1>

            <p class="mt-4 text-center text-lg">
                Please check your email to verify your account.
            </p>

            <div class="mt-6 flex justify-center">
                <button class="rounded-md bg-blue-400 px-6 py-3 text-white hover:bg-blue-500">
                    Send again??
                </button>
            </div>

        </div>
        <AppModal :open="!!isVerified" @close="isVerified = null" title="Email verification" >
            {{ verify ? 'You now can log in.' : 'Email verification failed.' }}
            <template #footer>
                <AppButton v-if="verify" @click="router.push({name:'login'})">
                    Login
                </AppButton>
            </template>
        </AppModal>
    </div>
</template>
<script setup>
import AppButton from '@/components/ui/AppButton.vue';
import AppModal from '@/components/ui/AppModal.vue';
import { ref } from 'vue';
import { useRoute, useRouter } from 'vue-router';
const route = useRoute()
const router = useRouter()
const verify = route.query.status === 'success'
const isVerified = ref(route.query.status)



</script>