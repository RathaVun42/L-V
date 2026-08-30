<template>
    <!-- Background -->
    <div class="absolute inset-0">
        <Lightfall />
    </div>

    <div class="absolute inset-0 bg-black/30"></div>

    <div class="flex z-10 min-h-screen flex-col justify-center items-center  px-6 py-12 sm:px-8 bg-danger ">
        <div class="mt-10 z-10 sm:mx-auto sm:w-full sm:max-w-sm bg-emerald-700 px-5 border  rounded-md  py-2 ">
            <form @submit.prevent="loginM(user)" method="POST" class="space-y-3">
                <div>
                    <label for="email" class="block text-sm/6 font-medium text-black">Email</label>
                    <div class="mt-2">
                        <input id="email" type="email" name="email" required v-model="user.email"
                            class="block w-full rounded-md bg-emerald-300 px-3 py-1.5 text-base outline-transparent outline-1 -outline-offset-1 placeholder:text-shadow-black-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-500 sm:text-sm/6" />
                        <p v-if="!!userErr?.email">{{ userErr.email }}</p>
                    </div>
                </div>
                <div>
                    <label for="password" class="block text-sm/6 font-medium text-black">Password</label>
                    <div class="mt-2">
                        <input id="password" type="password" name="password" required v-model="user.password"
                            class="block w-full rounded-md bg-emerald-300 px-3 py-1.5 text-base outline-transparent outline-1 -outline-offset-1 placeholder:text-shadow-black-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-500 sm:text-sm/6" />
                        <p v-if="!!userErr?.password">{{ userErr.password }}</p>
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="submit"
                        class="flex w-25 justify-center  rounded-md bg-emerald-300 px-3 py-1.5 text-sm/6 font-semibold text-black hover:bg-emerald-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500">
                        Login
                    </button>
                </div>
            </form>

            <p class="mb-0 text-center text-sm/6 text-black">
                <router-link :to="{ name: 'register' }" class="text-center">Don't have any account yet?</router-link>
            </p>

        </div>
    </div>
</template>
<script setup>
import { reactive, ref } from 'vue';
import Lightfall from '@/components/Lightfall.vue';
import { CloseModal, LoadingModal, MessageModal } from '@/functions/swal';
import { userStore } from '@/stores/user';
import router from '@/router';
import { login } from '@/services/auth';
const user = reactive({
    email: '',
    password: ''
})
const store = userStore()
const userErr = reactive({
    email: '',
    password: ''
})
function resetAllState() {
    Object.assign(user, defaultUser);
    Object.assign(userErr, defaultUserErr);
}

const defaultUser = JSON.parse(JSON.stringify(user))
const defaultUserErr = JSON.parse(JSON.stringify(userErr))
const loginM = async (user) => {
    try {
        LoadingModal('Loggin in ...')
        const response = await login(user);
        const { data } = response;
        store.setState(data.user)
        store.setSanctumToken(data.token)
        resetAllState();
        router.replace({name: 'admin.dashboard'})
        return CloseModal()
    } catch (err) {
        console.log(err)
        const {response} = err;
        console.log(response)
        if(!response){
            return MessageModal({ icon: "error", title: "Error", text: error.message });
        }
        const {data, status} = response;
        if(status === 422){
            Object.keys(userErr).forEach((key) =>{
                userErr[key] = data.errors[key] ? data.errors[0] : ''
            })
            return CloseModal()
        }
        return MessageModal({ icon: "error", title: "Error", text: data.message });
    } 
}
</script>