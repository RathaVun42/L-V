import { defineStore } from 'pinia'
import {
    getMenus,
    createMenu,
    updateMenu,
} from '@/services/menus'

export const useMenuStore = defineStore('menu', {
    state: () => ({
        menus: [],
        loading: false,
        error: null,
    }),

    actions: {
        async fetchMenus(params = {}) {
            this.loading = true
            this.error = null

            try {
                this.menus = await getMenus(params)
            } catch (error) {
                this.error = error
                throw error
            } finally {
                this.loading = false
            }
        },

        async addMenu(data) {
            const response = await createMenu(data)

            await this.fetchMenus()

            return response
        },

        async editMenu(id, data) {
            const response = await updateMenu(id, data)

            await this.fetchMenus()

            return response
        },
    },
})