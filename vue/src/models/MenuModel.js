import { createCategory } from './CategoryModel'
import { createProduct } from './ProductModel'

export function createMenu(data) {
    return {
        id: data.id,
        date: data.date,
        created_at: data.created_at ?? null,
        updated_at: data.updated_at ?? null,

        categories: data.categories
            ? data.categories.map(createCategory)
            : [],

        products: data.products
            ? data.products.map(createMenuProduct)
            : [],
    }
}

function createMenuProduct(data) {
    return {
        id: data.id,
        category_id: data.category_id,
        name: data.name,
        description: data.description ?? '',
        image: data.image ?? null,
        is_active: Boolean(data.is_active),

        price: Number(data.pivot?.price ?? 0),
        is_available: Boolean(data.pivot?.is_available),

        created_at: data.created_at ?? null,
        updated_at: data.updated_at ?? null,
    }
}

export function createMenus(data) {
    return data.map(createMenu)
}