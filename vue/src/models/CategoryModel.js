const img_url = import.meta.env.VITE_IMG_URL
export function createCategory(data) {
    return {
        id: data.id,
        name: data.name,
        description: data.description ?? '',
        image: img_url + data.image ?? null,
        is_active: Boolean(data.is_active),
        created_at: data.created_at ?? null,
        updated_at: data.updated_at ?? null,
    }
}

export function createCategories(data) {
    return data.map(createCategory) // map() automatically passes each array item into the function.
}
// or
//
// export function createCategories(data) {
//     return data.map(category => {
//         return createCategory(category)
//     })
// }