<template>
    <!-- Background -->
    <!-- <div class="absolute inset-0">
        <Lightfall />
    </div>

    <div class="absolute inset-0 bg-black/30"></div> -->

    <div class="flex z-10 min-h-screen items-center flex-col justify-center px-6 py-12 sm:px-8 bg-danger ">
        <!-- <div class="sm:mx-auto sm:w-full sm:max-w-sm">
            <img src="https://tailwindcss.com/plus-assets/img/logos/mark.svg?color=indigo&shade=500" alt="Your Company"
                class="mx-auto h-10 w-auto" />
            <h2 class="mt-10 text-center text-2xl/9 font-bold tracking-tight text-white">Sign in to your account</h2>
        </div> -->

        <div class="mt-10 z-10 sm:mx-auto sm:w-full sm:max-w-sm bg-emerald-700 px-5 border  rounded-md  py-2 ">
            <form @submit.prevent="registerM(user)" method="POST" class="space-y-3">
                <div>
                    <label for="name" class="block text-sm/6 font-medium text-black">Username</label>
                    <div class="mt-2">
                        <input id="name" type="text" name="name" required v-model="user.name"
                            class="block w-full rounded-md bg-emerald-300 px-3 py-1.5 text-base outline-transparent outline-1 -outline-offset-1 placeholder:text-shadow-black-500 focus:outline-2 focus:-outline-offset-2 focus:outline-white sm:text-sm/6" />
                        <p v-if="nameErr" class="text-red-500">{{ errMessage.name }}</p>
                    </div>
                </div>
                <div>
                    <label for="email" class="block text-sm/6 font-medium text-black">Email address</label>
                    <div class="mt-2">
                        <input id="email" type="email" name="email" required autocomplete="email" v-model="user.email"
                            class="block w-full rounded-md bg-emerald-300 px-3 py-1.5 text-base outline-transparent outline-1 -outline-offset-1 placeholder:text-shadow-black-500 focus:outline-2 focus:-outline-offset-2 focus:outline-white sm:text-sm/6" />
                            <p v-if="emailErr" class="text-red-500">{{ errMessage.email }}</p>
                    </div>
                </div>
                <div>
                    <label for="password" class="block text-sm/6 font-medium text-black">Password</label>
                    <div class="mt-2">
                        <input id="password" type="password" name="password" required v-model="user.password"
                            class="block w-full rounded-md bg-emerald-300 px-3 py-1.5 text-base outline-transparent outline-1 -outline-offset-1 placeholder:text-shadow-black-500 focus:outline-2 focus:-outline-offset-2 focus:outline-white sm:text-sm/6" />
                            <p v-if="passwordErr" class="text-red-500">{{ errMessage.password }}</p>
                    </div>
                </div>

                <div>
                    <label for="confirm_password" class="block text-sm/6 font-medium text-black">Confirm
                        password</label>
                    <div class="mt-2">
                        <input id="confirm_password" type="password" name="confirm_password" required
                            v-model="user.password_confirmation"
                            class="block w-full rounded-md bg-emerald-300 px-3 py-1.5 text-base outline-transparent outline-1 -outline-offset-1 placeholder:text-shadow-black-500 focus:outline-2 focus:-outline-offset-2 focus:outline-white sm:text-sm/6" />
                    </div>
                </div>
                <div v-if="store.is_admin">
                    <label for="role" class="block text-sm/6 font-medium text-black">Roles</label>
                    <div class="mt-2">
                        <select name="role" id="role" v-model="user.role" class="block w-full rounded-md bg-emerald-300 px-3 py-1.5 text-base outline-transparent outline-1 -outline-offset-1 placeholder:text-shadow-black-500 focus:outline-2 focus:-outline-offset-2 focus:outline-white sm:text-sm/6">
                            <option value="" disabled>Select a role</option>
                            <option v-for="value in roles" :value="value">{{ value }}</option>
                        </select>
                    </div>
                    <!-- <AppSelect id="role" :options="roles" placeholder="Select a role" v-model="user.role">

                    </AppSelect> -->
                </div>
                <div>
                    <div class="mt-5">
                        <input @change="handleFileChange" class="block w-full text-sm text-gray-50
                                    file:mr-4 file:rounded-md
                                    file:border-0
                                    file:bg-emerald-500
                                    file:px-4 file:py-2
                                    file:text-sm file:font-semibold
                                    file:text-black
                                    hover:file:bg-emerald-300" type="file" name="image" id="image"
                            accept=".png,.jpg,.webp">
                    </div>
                </div>

                <div :class="['flex justify-end', store.isAuthenticated ? 'mt-3' : '' ]">
                    <button type="submit"
                        :class="[
                            'flex w-25 justify-center  rounded-md bg-emerald-300 px-3 py-1.5 text-sm/6 font-semibold text-black hover:bg-emerald-500 focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-white',
                            store.isAuthenticated ? '' : 'mb-3' 
                        ]">
                        Register
                    </button>
                </div>
            </form>
            <div  class="flex justify-start px-5 mb-3">
                
            </div>
            <div class="flex justify-between mb-3">
                <p v-if="!store.isAuthenticated" class="mb-0 text-center text-sm/6 text-black hover:text-emerald-300">
                    <router-link :to="{ name: 'login' }" class="text-center">Already have account?</router-link>
                </p>
                <p v-if="store.isAuthenticated" class="mb-0 text-center text-sm/6 text-black">
                    <router-link :to=" '/dashboard/index' " class="text-center">Dashboard</router-link>
                </p>
            </div>
        </div>
    </div>
</template>
<script setup>
import User from '@/models/user';
import router from '@/router';
import { onMounted, reactive, ref } from 'vue';
import Lightfall from '@/components/Lightfall.vue';
import { CloseModal, LoadingModal, MessageModal } from '@/functions/swal';
import { adminRegister, register } from '@/services/auth';
import { userStore } from '@/stores/user';
import { getRoles } from '@/services/roles';
import AppInput from '@/components/ui/AppInput.vue';
import AppSelect from '@/components/ui/AppSelect.vue';
const store = userStore()

const user = reactive(
    new User(
        {
            name: '',
            email: '',
            password: '',
            password_confirmation: '',
            image: null,
            role: ''
        }
    )
);
const handleFileChange = (event) => {
    user.image = event.target.files[0]
}
const isRegistering = ref(false)
const nameErr = ref(false)
const emailErr = ref(false)
const passwordErr = ref(false)
const errMessage = reactive({
    name: '',
    email: '',
    password: ''
})
const roles = ref([])
const roleErr = ref('')
const fetchRoles = async ()=>{
    try{
        console.log(store.is_admin)
        if(store.is_admin){
            const data = await getRoles()
            roles.value = data
            console.log(roles.value)
        }else{
            roles.value = []
        }
    }catch(err){
        roleErr.value = err.response?.data?.message || 'error laoding roles.'
    }
}
const registerM = async (user = {}) => {
    try {
        LoadingModal('Checking you email.')
        isRegistering.value = true
        let res = null
        if(!store.isAuthenticated){
            res = await register(user);
        }
        if(store.isAuthenticated && store.is_admin){
            res = await adminRegister(user)
        }
        console.log(user.image)
        if (res?.status == 201) {
            router.push('/email_verified')
        }
        return CloseModal()
        
    } catch (err) {
        console.log(err)
        const {response} = err
        const {data} = response
        if (err.response?.status === 422) {
            const errors = err.response.data.errors
            console.log(errors)
            nameErr.value = !!errors?.name
            emailErr.value = !!errors?.email
            passwordErr.value = !!errors?.password

            Object.keys(errMessage).forEach((key) => {
                errMessage[key] = errors[key]
                    ? errors[key][0]
                    : "";
            });
      
        }
        return MessageModal({icon: "error", title: "Error", text: data.message})
    } finally {
        isRegistering.value = false
        
    }
}

onMounted(fetchRoles)
</script>