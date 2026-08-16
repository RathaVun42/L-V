<template>
    <div class="flex min-h-full flex-col justify-center px-6 py-12 sm:px-8 bg-danger ">
        <!-- <div class="sm:mx-auto sm:w-full sm:max-w-sm">
            <img src="https://tailwindcss.com/plus-assets/img/logos/mark.svg?color=indigo&shade=500" alt="Your Company"
                class="mx-auto h-10 w-auto" />
            <h2 class="mt-10 text-center text-2xl/9 font-bold tracking-tight text-white">Sign in to your account</h2>
        </div> -->

        <div class="mt-10 sm:mx-auto sm:w-full sm:max-w-sm bg-secondary px-5 border border-gray-300 rounded-md  py-2 ">
            <form @submit.prevent="registerM(user)" method="POST" class="space-y-3" >
                <div>
                    <label for="name" class="block text-sm/6 font-medium text-black">Username</label>
                    <div class="mt-2">
                        <input id="name" type="text" name="name" required v-model="user.name"
                            class="block w-full rounded-md bg-blue-300 px-3 py-1.5 text-base outline-transparent outline-1 -outline-offset-1 placeholder:text-shadow-black-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-500 sm:text-sm/6" />

                    </div>
                </div>
                <div>
                    <label for="email" class="block text-sm/6 font-medium text-black">Email address</label>
                    <div class="mt-2">
                        <input id="email" type="email" name="email" required autocomplete="email" v-model="user.email"
                            class="block w-full rounded-md bg-blue-300 px-3 py-1.5 text-base outline-transparent outline-1 -outline-offset-1 placeholder:text-shadow-black-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-500 sm:text-sm/6" />
                    </div>
                </div>
                <div>
                    <label for="password" class="block text-sm/6 font-medium text-black">Password</label>
                    <div class="mt-2">
                        <input id="password" type="password" name="password" required v-model="user.password"
                            class="block w-full rounded-md bg-blue-300 px-3 py-1.5 text-base outline-transparent outline-1 -outline-offset-1 placeholder:text-shadow-black-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-500 sm:text-sm/6" />
                    </div>
                </div>
                <div>
                    <label for="confirm_password" class="block text-sm/6 font-medium text-black">Confirm
                        password</label>
                    <div class="mt-2">
                        <input id="confirm_password" type="password" name="confirm_password" required v-model="user.password_confirmation"
                            class="block w-full rounded-md bg-blue-300 px-3 py-1.5 text-base outline-transparent outline-1 -outline-offset-1 placeholder:text-shadow-black-500 focus:outline-2 focus:-outline-offset-2 focus:outline-indigo-500 sm:text-sm/6" />
                    </div>
                </div>
                <div>
                    <input class="block w-full text-sm text-gray-500
                                file:mr-4 file:rounded-md
                                file:border-0
                                file:bg-blue-300
                                file:px-4 file:py-2
                                file:text-sm file:font-semibold
                                file:text-white
                                hover:file:bg-blue-500" type="file" name="avatar" id="avatar" accept=".png,.jpg,.webp">
                </div>

                <div class="flex justify-end">
                    <button type="submit"
                        class="flex w-25 justify-center  rounded-md bg-blue-300 px-3 py-1.5 text-sm/6 font-semibold text-white hover:bg-blue-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-indigo-500">
                        Register
                    </button>
                </div>


            </form>

            <p class="mt-10 text-center text-sm/6 text-gray-400">
                Already register?
                <a href="#" class="font-semibold text-indigo-400 hover:text-indigo-300">Login</a>
            </p>
        </div>
    </div>
</template>
<script setup>
import User from '@/models/user';
import router from '@/router';
import register from '@/services/auth';
import { reactive, ref } from 'vue';

const user = reactive(
    new User(
        {
            name: '',
            email: '',
            password: '',
            password_confirmation:''
        }
    )
);
const isRegistering = ref(false)
const nameErr = ref(false)
const emailErr = ref(false)
const passwordErr = ref(false)
const registerM = async (user = {}) => {
    try {
        isRegistering.value = true
        const res = await register(user);
        if (res.status == 201) {
            router.push('/email_verified')
        }     
    }catch(err){
        if (err.response?.status === 422) {
            const errors = err.response.data.errors
            nameErr.value = !!errors?.name
            emailErr.value = !!errors?.email
            passwordErr.value = !!errors?.password
        }
    }finally{
        isRegistering.value = false
    }
}
</script>