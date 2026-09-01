<template>
    <div class="flex z-10 min-h-screen flex-col justify-center items-center  px-6 py-12 sm:px-8 bg-danger ">
        <div class="mt-10 z-10 sm:mx-auto sm:w-full sm:max-w-sm bg-emerald-700 px-5 border  rounded-md  py-2 ">
            <form @submit.prevent="onSendResetPasswordEmail(user.email)" method="POST" class="space-y-3">
                <div>
                    <label for="email" class="block text-sm/6 font-medium text-black">Email</label>
                    <div class="mt-2">
                        <input v-model="user.email" id="email" type="email" name="email" required
                            class="block w-full rounded-md bg-emerald-300 px-3 py-1.5 text-base outline-transparent outline-1 -outline-offset-1 placeholder:text-shadow-black-500 focus:outline-2 focus:-outline-offset-2 focus:outline-emerald-500 sm:text-sm/6" />

                    </div>
                    <p v-if="userErr.email" class="mt-1 text-sm font-medium text-red-400">
                        {{ userErr.email }}
                    </p>
                </div>
                <div class="flex justify-end">
                    <button type="submit"
                        class="flex w-50 justify-center  rounded-md bg-emerald-300 px-3 py-1.5 text-sm/6 font-semibold text-black hover:bg-emerald-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-500">
                        Send reset password
                    </button>
                </div>
            </form>
        </div>
    </div>
</template>
<script setup>
import { CloseModal, LoadingModal, MessageModal } from '@/functions/swal';
import { sendResetPasswordEmail } from '@/services/auth';
import { reactive } from 'vue';
const user = reactive({
    email: ''
})
const userErr = reactive({
    email: ''
})
const defaultUser = JSON.parse(JSON.stringify(user))
const defaultUserErr = JSON.parse(JSON.stringify(userErr))
function resetState() {
    Object.assign(user, defaultUser)
    Object.assign(userErr, defaultUserErr)
}
const onSendResetPasswordEmail = async (email) => {
    try {
        LoadingModal('Sending ...')
        console.log(email)
        const response = await sendResetPasswordEmail(email)
        console.log(response)
        resetState()
        return MessageModal({ icon: "success", title: "Success", text: response.data.message })
    } catch (err) {
        console.log(err)
        const {response} = err;
        console.log(response)
        if(!response){
            return
        }
        const {status, data} = response
        if(status == 422){
            Object.keys(userErr).forEach((key)=>{
                userErr[key] = data.errors[key] ? data.errors[key][0] : ''
            })
            return CloseModal();
        }
        return MessageModal({ icon: "error", title: "Error", text: data.message });
    }
}
</script>