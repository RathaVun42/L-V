import { createMenus } from "@/models/MenuModel"
import { api } from "./auth"

export async function getMenus(params = {}) {
    const response = await api.get('/admin/menus', {
        params,
    })

    return createMenus(response.data.data)
}

export async function createMenu(data) {
    return api.post('/admin/menus', data)
}

export async function updateMenu(id, data) {
    return api.put(`/admin/menus/${id}`, data)
}