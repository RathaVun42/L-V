import { defineStore } from "pinia";

export const userStore = defineStore('user', {
    state: () => ({
        id: null,
        name: null,
        email: null,
        profile_image: null,
        password_null: false,
        is_admin: false
    }),

    getters: {
        isAuthenticated: (state) => !!state.id
    },

    actions: {
        setState(user) {
            this.id = user.id;
            this.name = user.name;
            this.email = user.email;
            this.profile_image = user.profile_image;
            this.password_null = user.password_null;
            this.is_admin = user.role == 'admin'
        },

        resetState() {
            this.id = null;
            this.name = null;
            this.email = null;
            this.profile_image = null;
            this.password_null = false;
            this.is_admin = false;
        },

        setSanctumToken(token) {
            localStorage.setItem('SANCTUM-TOKEN', token);
        },

        getSanctumToken() {
            return localStorage.getItem('SANCTUM-TOKEN');
        },

        removeSanctumToken() {
            localStorage.removeItem('SANCTUM-TOKEN');
        },

        reset() {
            this.resetState();
            this.removeSanctumToken();
        },
    },

    persist: true,
});