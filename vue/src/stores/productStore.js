import { defineStore } from 'pinia'
import {
    getProducts,
    createProduct,
    updateProduct,
    deleteProduct,
} from '@/services/product'

export const useProductStore = defineStore('product', {
    state: () => ({
        products: [],
        loading: false,
        error: null,
    }),

    actions: {
        async fetchProducts(params = {}) {
            this.loading = true
            this.error = null

            try {
                this.products = await getProducts(params)
            } catch (error) {
                this.error = error
                throw error
            } finally {
                this.loading = false
            }
        },

        async addProduct(data) {
            const response = await createProduct(data)

            await this.fetchProducts()

            return response
        },

        async editProduct(id, data) {
            const response = await updateProduct(id, data)

            await this.fetchProducts()

            return response
        },

        async removeProduct(id) {
            const response = await deleteProduct(id)

            this.products = this.products.filter(
                product => product.id !== id
            )

            return response
        },
    },
})