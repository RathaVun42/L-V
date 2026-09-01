import User from "@/models/user" ;
import axios from "axios";
const apiUrl = import.meta.env.VITE_API_URL
const resetPasswordCallbackUrl = import.meta.env.VITE_RESET_PASSWORD_URL

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

    return await axios.post(`${apiUrl}/register`, formData)
}
export async function login(user = {}) {
    console.log(apiUrl)
    const res = await axios.post(
        `${apiUrl}/login`,
        new User(user),
        {
            headers:{
                "Content-Type": 'application/json'
            }
        }
    )
    return res
}
export async function logout(token) {
    return await axios.post(
        `${apiUrl}/logout`,
        null, {
            headers: {
                Authorization: `Bearer ${token}`
            }
        }
    )
}

export async function sendResetPasswordEmail(email) {
    return await axios.post(
        `${apiUrl}/sent/reset-password-email`,
        {
            email,
            callback_url: resetPasswordCallbackUrl
        }
    )
}
