import User from "@/models/user" ;
import axios from "axios";
const apiUrl = import.meta.env.API_URL

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

    return await axios.post('http://localhost:8000/api/register', formData)
}
export async function login(user = {}) {
    const res = await axios.post(
        `http://localhost:8000/api/login`,
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
        `http://localhost:8000/api/logout`,
        null, {
            headers: {
                Authorization: `Bearer ${token}`
            }
        }
    )
}