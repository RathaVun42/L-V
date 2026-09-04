import User from "@/models/user" ;
import axios from "axios";
const apiUrl = import.meta.env.VITE_API_URL
const resetPasswordCallbackUrl = import.meta.env.VITE_RESET_PASSWORD_URL
//axios custom instance
export const api = axios.create({
    baseURL: apiUrl
})

export const register = async (user) => {

    const formData = new FormData()
    formData.append('name', user.name)
    formData.append('email', user.email)
    formData.append('password', user.password)
    formData.append(
        'password_confirmation',
        user.password_confirmation
    )

    if (user.image) {
        formData.append('image', user.image)
    }

    return await api.post(`/register`, formData)
}
export async function login(user = {}) {
    console.log(apiUrl)
    const res = await api.post(
        `/login`,
        new User(user),
        {
            headers:{
                "Content-Type": 'application/json'
            }
        }
    )
    return res
}
export async function verifyToken() {
    return await api.get(
        `/verify/token`
    )
}
export async function logout(token) {
    return await api.post(
        `/logout`,
        null, {
            headers: {
                Authorization: `Bearer ${token}`
            }
        }
    )
}

export async function sendResetPasswordEmail(email) {
    return await api.post(
        `/sent/reset-password-email`,
        {
            email,
            callback_url: resetPasswordCallbackUrl
        }
    )
}
export async function chnagePassword(data) {
    return await api.put(
        `/change/password`,
        {
            old_password: data.old_password,
            new_password: data.new_password,
            new_password_confirmation: data.new_password_confirmation
        }
    )
    
}
export async function updateUser(user) {
    return await api.put(
        '/update/user',
        {
            name: user.name,
            email: user.email
        }
    )
}
