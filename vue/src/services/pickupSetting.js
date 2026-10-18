import { api } from "./auth";

export async function getPickupSettings() {
    const response = await api.get('/admin/pickup-settings')
    return response.data.data
}

export async function updatePickupSettings(data) {
    const response = await api.put('/admin/pickup-settings', data)
    return response.data.data
}