import { api } from "./auth";
export async function getUsers(param={}) {
    const response = await api.get('/admin/get/users', {
        params: param
    })
    return response.data
}