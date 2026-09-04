<template>
    <div class="min-h-screen bg-gray-50 px-10 py-10">
        <div class="mx-auto max-w-3xl rounded-2xl bg-white p-6 shadow-sm sm:p-10">

            <!-- Profile Image -->
            <div class="mb-8 flex flex-col items-center">
                <div
                    class="flex h-32 w-32 items-center justify-center overflow-hidden rounded-full border-4 border-gray-100 bg-gray-100">
                    <img v-if="profileImage" :src="profileImage" alt="Profile" class="h-full w-full object-cover" />

                    <span v-else class="text-sm text-gray-400">
                        Image
                    </span>
                </div>

                <label class="mt-3 cursor-pointer text-sm font-medium text-green-700 hover:text-green-800">
                    Change image

                    <input type="file" accept="image/*" class="hidden" @change="handleImageUpload" />
                </label>
            </div>

            <!-- Profile Information -->
            <div class="space-y-5">
                <!-- Name -->
                <div class="grid gap-2 sm:grid-cols-[100px_1fr] sm:items-center sm:gap-5">
                    <label class="text-sm font-medium text-gray-700">
                        Name
                    </label>
                    <div class="mt-2">
                        <input v-model="form.name" type="text" placeholder="Enter name"
                            class="h-11 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none transition focus:border-green-600 focus:ring-2 focus:ring-green-100" />
                        <p v-if="formErr.name" class="text-green-700 text-sm">{{ formErr.name }}</p>
                    </div>
                </div>

                <!-- Role -->
                <div class="grid gap-2 sm:grid-cols-[100px_1fr] sm:items-center sm:gap-5">
                    <label class="text-sm font-medium text-gray-700">
                        Role
                    </label>

                    <input readonly v-model="form.role" type="text" placeholder="Enter role"
                        class="h-11 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none transition focus:border-green-600 focus:ring-2 focus:ring-green-100" />

                </div>

                <!-- Email -->
                <div class="grid gap-2 sm:grid-cols-[100px_1fr] sm:items-center sm:gap-5">
                    <label class="text-sm font-medium text-gray-700">
                        Email
                    </label>
                    <div class="mt-2">
                        <input v-model="form.email" type="email" placeholder="Enter email"
                            class="h-11 w-full rounded-lg border border-gray-300 px-3 text-sm outline-none transition focus:border-green-600 focus:ring-2 focus:ring-green-100" />
                        <p v-if="formErr.email" class="text-green-700 text-sm">{{ formErr.email }}</p>
                    </div>
                </div>

                <div v-if="isFormChaged" class="flex justify-end">
                    <button type="button"
                        class="cursor-pointer rounded-lg bg-green-700 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
                        @click="saveChangedUserInfo">
                        Save
                    </button>
                </div>
            </div>

            <!-- Change Password -->
            <div class="mt-10 border-t border-gray-200 pt-8">
                <h2 class="mb-5 text-xl font-semibold text-gray-800">
                    Change Password
                </h2>

                <div class="rounded-xl border border-gray-200 bg-gray-50 p-5 sm:p-6">
                    <div class="mb-5">
                        <label class="mb-2 block text-sm font-medium text-gray-700">
                            Old password
                        </label>

                        <input v-model="password.old_password" type="password" placeholder="Enter old password"
                            class="h-11 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none transition focus:border-green-600 focus:ring-2 focus:ring-green-100" />
                        <p v-if="passwordErr.old_password" class="text-red-500">{{ passwordErr.old_password }}</p>
                    </div>
                    <!-- Password -->
                    <div class="mb-5">
                        <label class="mb-2 block text-sm font-medium text-gray-700">
                            Password
                        </label>

                        <input v-model="password.new_password" type="password" placeholder="Enter new password"
                            class="h-11 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none transition focus:border-green-600 focus:ring-2 focus:ring-green-100" />
                        <p v-if="passwordErr.new_password" class="text-red-500">{{ passwordErr.new_password }}</p>
                    </div>

                    <!-- Confirm Password -->
                    <div class="mb-6">
                        <label class="mb-2 block text-sm font-medium text-gray-700">
                            Confirm Password
                        </label>

                        <input v-model="password.new_password_confirmation" type="password"
                            placeholder="Confirm new password"
                            class="h-11 w-full rounded-lg border border-gray-300 bg-white px-3 text-sm outline-none transition focus:border-green-600 focus:ring-2 focus:ring-green-100" />
                    </div>

                    <!-- Save -->
                    <div class="flex justify-end">
                        <button type="button"
                            class="rounded-lg bg-green-700 px-6 py-2.5 text-sm font-semibold text-white transition hover:bg-green-800 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2"
                            @click="saveChangedPassword">
                            Save
                        </button>
                    </div>

                    <p v-if="message" class="mt-4 text-sm text-green-700">
                        {{ message }}
                    </p>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup>
import { LoadingModal, MessageModal } from '@/functions/swal'
import { chnagePassword, updateUser } from '@/services/auth'
import { userStore } from '@/stores/user'
import { computed, reactive, ref, watch } from 'vue'
import { useRoute, useRouter } from 'vue-router'
const store = userStore()
const router = useRouter()
const profileImage = ref(`http://localhost:8000/storage/${store.$state.profile_image}`)
const message = ref('')
const originForm = reactive({
    name: store.$state.name,
    role: store.$state.is_admin ? 'admin' : 'staff',
    email: store.$state.email,
})
const form = reactive({
    name: store.$state.name,
    role: store.$state.is_admin ? 'admin' : 'staff',
    email: store.$state.email,
})
const isFormChaged = computed(() => {
    return (
        form.name !== originForm.name ||
        form.role !== originForm.role ||
        form.email !== originForm.email
    )
})

const formErr = reactive({
    name: '',
    email: ''
})
const password = reactive({
    old_password: '',
    new_password: '',
    new_password_confirmation: '',
})
const passwordErr = reactive({
    old_password: "",
    new_password: ''
})

function handleImageUpload(event) {
    const file = event.target.files?.[0]
    if (!file) return
    profileImage.value = URL.createObjectURL(file)
}

async function saveChangedPassword() {
    if (!password.old_password) return
    try {
        LoadingModal('Changing you password ...')
        const response = await chnagePassword(password)
        MessageModal(
            { icon: "success", title: "Success", text: response.data.message },
            () => {
                router.push({ name: 'logout' })
            }
        )
    } catch (err) {
        const { response } = err
        const { data, status } = response
        if (status == 422) {
            Object.keys(passwordErr).forEach((key) => {
                passwordErr[key] = data.errors[key] ? data.errors[key][0] : ''
            })
        }
        return MessageModal(
            { icon: "fail", title: 'Failed', text: data.message },
        )
    }

    message.value = 'Profile saved successfully.'
}
async function saveChangedUserInfo() {
    try {
        LoadingModal('Changing your info ...')
        const response = await updateUser(form)
        const { data } = response
        store.setState(data.user)
        return MessageModal( // can use return or just MessageModal cause this is the last statement of the code
            { icon: "success", title: "Success", text: response.data.message },
        )
    } catch (err) {
        const { response } = err
        const { data, status } = response
        if (status == 422) {
            Object.keys(formErr).forEach((key) => {
                formErr[key] = data.errors[key] ? data.errors[key][0] : ''
            })
        }
        return MessageModal(
            { icon: "fail", title: 'Failed', text: data.message }
        )
    }
}
</script>