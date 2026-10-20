
import { defineStore } from 'pinia'

const img_url = import.meta.env.VITE_IMG_URL

export const userStore = defineStore('user', {
    state: () => ({
        id: null,
        name: null,
        email: null,
        profile_image: null,
        password_null: false,

        // Role and permission information
        role: null,
        roles: [],
        permissions: [],
        is_admin: false,
    }),

    getters: {
        isAuthenticated: (state) => !!state.id,

        hasRole: (state) => (role) =>
            state.roles.includes(role),

        hasPermission: (state) => (permission) =>
            state.permissions.includes(permission),
    },

    actions: {
        setState(user) {
            this.id = user.id
            this.name = user.name
            this.email = user.email

            this.profile_image = user.profile_image
                ? img_url + user.profile_image
                : null

            this.password_null = user.password_null ?? false

            // Support a single role or an array of roles.
            this.roles = Array.isArray(user.roles)
                ? user.roles
                : user.role
                    ? [user.role]
                    : []

            this.role = this.roles[0] ?? null
            this.is_admin = this.roles.includes('admin')

            // Laravel should return permission names as strings.
            this.permissions = Array.isArray(user.permissions)
                ? user.permissions
                : []
        },

        resetState() {
            this.id = null
            this.name = null
            this.email = null
            this.profile_image = null
            this.password_null = false

            this.role = null
            this.roles = []
            this.permissions = []
            this.is_admin = false
        },

        setSanctumToken(token) {
            localStorage.setItem('SANCTUM-TOKEN', token)
        },

        getSanctumToken() {
            return localStorage.getItem('SANCTUM-TOKEN')
        },

        removeSanctumToken() {
            localStorage.removeItem('SANCTUM-TOKEN')
        },

        reset() {
            this.resetState()
            this.removeSanctumToken()
        },
    },

    persist: true,
})
