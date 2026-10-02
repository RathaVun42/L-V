import { api } from './auth'
import { createProducts } from '@/models/ProductModel'

function buildProductFormData(data) {
    const formData = new FormData()

    formData.append('name', data.name)
    formData.append('description', data.description ?? '')
    formData.append('category_id', data.category_id)
    formData.append('is_active', data.is_active ? '1' : '0')

    if (data.image instanceof File) {
        formData.append('image', data.image)
    }

    return formData
}

export async function getProducts(params = {}) {
    const response = await api.get('/admin/products', {
        params,
    })

    return createProducts(response.data.data)
}

export async function createProduct(data) {
    const formData = buildProductFormData(data)

    return api.post('/admin/products', formData)
}

export async function updateProduct(id, data) {
    const formData = buildProductFormData(data)

    formData.append('_method', 'PUT')

    return api.post(`/admin/products/${id}`, formData)
}

export async function deleteProduct(id) {
    return api.delete(`/admin/products/${id}`)
}