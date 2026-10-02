import { createCategory } from "./CategoryModel"
const img_url = import.meta.env.VITE_IMG_URL
export function createProduct(data) {
    return {
        id: data.id,
        category_id: data.category_id,
        name: data.name,
        description: data.description ?? '',
        image: img_url + data.image ?? null,
        is_active: Boolean(data.is_active),
        created_at: data.created_at ?? null,
        updated_at: data.updated_at ?? null,
        category: data.category ? createCategory(data.category) : null
    }
}

export function createProducts(data) {
    return data.map(createProduct)
}