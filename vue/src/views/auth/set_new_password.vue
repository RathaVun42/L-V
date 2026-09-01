<template>
    <div class="flex z-10 min-h-screen flex-col justify-center items-center  px-6 py-12 sm:px-8 bg-danger ">
        <div class="mt-10 z-10 sm:mx-auto sm:w-full sm:max-w-sm bg-emerald-700 px-5 border  rounded-md  py-2 ">
            <form @submit.prevent="onSetNewPassword" method="POST" class="space-y-3">
                <div>
                    <label for="password" class="block text-sm/6 font-medium text-black">Password</label>
                    <div class="mt-2">
                        <input id="password" type="password" name="password" required v-model="user.password"
                            class="block w-full rounded-md bg-emerald-300 px-3 py-1.5 text-base outline-transparent outline-1 -outline-offset-1 placeholder:text-shadow-black-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-500 sm:text-sm/6" />
                        <p v-if="userErr.password" class="text-red-500">{{ userErr.password }}</p>
                    </div>
                </div>
                <div>
                    <label for="confirm_password" class="block text-sm/6 font-medium text-black">Confirm
                        password</label>
                    <div class="mt-2">
                        <input id="confirm_password" type="password" name="confirm_password" required
                            v-model="user.password_confirmation"
                            class="block w-full rounded-md bg-emerald-300 px-3 py-1.5 text-base outline-transparent outline-1 -outline-offset-1 placeholder:text-shadow-black-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-500 sm:text-sm/6" />
                    </div>
                </div>
                <div class="flex justify-end">
                    <button type="submit"
                        class="flex w-50 justify-center  rounded-md bg-emerald-300 px-3 py-1.5 text-sm/6 font-semibold text-black hover:bg-emerald-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-500">
                        Set new password
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
<script setup>
import { CloseModal, MessageModal } from '@/functions/swal';
import { reactive } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import axios from 'axios';

const route = useRoute();
const router = useRouter();


const user = reactive({
    password: '',
    password_confirmation: ''
})
const userErr = reactive({
    password: ''
})
const userDefault = JSON.parse(JSON.stringify(user))
const userErrDefault = JSON.parse(JSON.stringify(userErr))
function resetUser() {
    Object.assign(user, userDefault)
    Object.assign(userErr, userErrDefault)
}
async function onSetNewPassword() {
    try {
        const response = await axios.post(new URL(route.query['forwarded-url']), user);
        console.log(response)
        resetUser()
        MessageModal(
            { icon: "success", title: "Success", text: response.data.message },
            () => {
                router.push({ name: 'login' })
            }
        )
    }catch(err){
        console.log(err)
        const {response} = err
        console.log(response)
        if(!response){
            return
        }
        const {data, status} = response
        if(status == 422){
            Object.keys(userErr).forEach((key)=>{
                userErr[key] = data.errors[key] ? data.errors[key][0] : ''
            })
            return CloseModal()
        }
        return MessageModal({ icon: "error", title: "Error", text: data.message });
    }

}
</script>