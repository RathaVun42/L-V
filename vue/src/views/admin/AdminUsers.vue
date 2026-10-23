
<script setup>
import { ref, onMounted } from 'vue'
import { getUsers } from '@/services/user'
import AppInput from '@/components/ui/AppInput.vue'
import AppSelect from '@/components/ui/AppSelect.vue'
import AppBadge from '@/components/ui/AppBadge.vue'
import AppLoading from '@/components/ui/AppLoading.vue'
import AppEmptyState from '@/components/ui/AppEmptyState.vue'
import AppButton from '@/components/ui/AppButton.vue'
import { getRoles } from '@/services/roles'

const users = ref([])
const loading = ref(false)
const error = ref('')
const errorRoles = ref('')
const search = ref('')
const selectedRole = ref('')
const pagination = ref(null)
const roleOptions = ref([])

// const roleOptions = [
//     { label: 'All roles', value: '' },
//     { label: 'Admin', value: 'admin' },
//     { label: 'Staff', value: 'staff' },
//     { label: 'Customer', value: 'customer' },
// ]
async function fetchRoles() {
    try{
        const roles = await getRoles()
        
        roleOptions.value = roles.map((role) => ({ label: role, value: role }))
        roleOptions.value.unshift({label: "All roles", value: ''})
        console.log(roleOptions.value)
    }catch(e){
        errorRoles.value = e.response?.data?.message || "Failed to load roles."
    }
}
async function fetchUsers(page = 1) {
    loading.value = true
    error.value = ''
    try {
        const response = await getUsers({
            search: search.value || undefined,
            role: selectedRole.value || undefined,
            page,
            per_page: 5,
        })

        users.value = response.data
        pagination.value = response.meta
    } catch (err) {
        error.value =
            err.response?.data?.message || 'Failed to load users.'
    } finally {
        loading.value = false
    }
}

function applyFilters() {
    fetchUsers(1)
}

onMounted(() => {
    fetchUsers()
    fetchRoles()
})
</script>

<template>
    <section class="space-y-6">
        <div>
            <h1 class="text-2xl font-bold text-gray-900">
                User Management
            </h1>
            <p class="mt-1 text-sm text-gray-500">
                Manage registered users, roles, and email verification.
            </p>
        </div>

        <div class="flex flex-col gap-3 sm:flex-row sm:justify-center">
            <div class="flex-1">
                <AppInput
                    v-model="search"
                    label="Search users"
                    placeholder="Search name or email..."
                    @keyup.enter="applyFilters"
                />
            </div>

            <div class="sm:w-48">
                <AppSelect
                    v-model="selectedRole"
                    label="Filter by role"
                    :options="roleOptions"
                />
            </div>

            <div class="flex justify-end sm:items-end">
                <AppButton @click="applyFilters">
                    Search
                </AppButton>
            </div>
        </div>

        <p
            v-if="error"
            class="rounded-lg bg-red-50 p-3 text-sm text-red-700"
        >
            {{ error }}
        </p>

        <AppLoading v-if="loading" />

        <AppEmptyState
            v-else-if="!error && users.length === 0"
            title="No users found"
            description="Try changing your search or role filter."
        />

        <div
            v-else-if="!loading && users.length"
            class="overflow-x-auto rounded-xl border border-gray-200 bg-white"
        >
            <table class="w-full text-left text-sm">
                <thead class="bg-gray-50 text-gray-600">
                    <tr>
                        <th class="px-5 py-3 font-medium">Name</th>
                        <th class="px-5 py-3 font-medium">Email</th>
                        <th class="px-5 py-3 font-medium">Role</th>
                        <th class="px-5 py-3 font-medium">Verification</th>
                    </tr>
                </thead>

                <tbody class="divide-y divide-gray-100">
                    <tr
                        v-for="user in users"
                        :key="user.id"
                        class="hover:bg-gray-50"
                    >
                        <td class="px-5 py-4 font-medium text-gray-900">
                            {{ user.name }}
                        </td>

                        <td class="px-5 py-4 text-gray-600">
                            {{ user.email }}
                        </td>

                        <td class="px-5 py-4">
                            <AppBadge
                                v-for="role in user.roles"
                                :key="role"
                            >
                                {{ role }}
                            </AppBadge>
                        </td>

                        <td class="px-5 py-4">
                            <AppBadge>
                                {{ user.email_verified_at
                                    ? 'Verified'
                                    : 'Unverified' }}
                            </AppBadge>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>

        <div
            v-if="pagination && pagination.last_page > 1"
            class="flex items-center justify-between"
        >
            <p class="text-sm text-gray-500">
                Page {{ pagination.current_page }}
                of {{ pagination.last_page }}
            </p>

            <div class="flex gap-2">
                <AppButton
                    :disabled="pagination.current_page <= 1 || loading"
                    variant="secondary"
                    @click="fetchUsers(pagination.current_page - 1)"
                >
                    Previous
                </AppButton>

                <AppButton
                    :disabled="pagination.current_page >= pagination.last_page || loading"
                    variant="secondary"
                    @click="fetchUsers(pagination.current_page + 1)"
                >
                    Next
                </AppButton>
            </div>
        </div>
    </section>
</template>
