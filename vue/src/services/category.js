import { api } from "./auth"

function buildCategoryFormData(data) {
    const formData = new FormData()

    formData.append('name', data.name)
    formData.append('description', data.description ?? '')
    formData.append('is_active', data.is_active ? '1' : '0')

    if (data.image instanceof File) {
        formData.append('image', data.image)
    }

    return formData
}

export function getCategories(params = {}) {
    return api.get('/admin/categories', {
        params,
    })
}

export function createCategory(data) {
    const formData = buildCategoryFormData(data)

    return api.post('/admin/categories', formData)
}

export function updateCategory(id, data) {
    const formData = buildCategoryFormData(data)

    // Laravel method spoofing
    formData.append('_method', 'PUT')

    return api.post(`/admin/categories/${id}`, formData)
}

export function deleteCategory(id) {
    return api.delete(`/admin/categories/${id}`)
}