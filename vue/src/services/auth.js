import User from "@/models/user" ;
import axios from "axios";
const apiUrl = import.meta.env.API_URL

export async function register(user = {}){
    const res = await axios.post(
        `http://localhost:8000/api/register`,
        new User(user),
        {
            headers:{
                "Content-Type": 'application/json'
            }
        }
    )
    return res
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