import { api } from "./auth";

export async function getPickupSlots(date) {
    const response = await api.get('/admin/pickup-slots', {
        params: {date}
    })

    return response.data.data
}