import { api } from "./auth";
export async function getRoles(params = {}) {
    const response = await api.get('/admin/roles', {params : params})
    return response.data.roles
}