import './assets/main.css'
import { createApp } from 'vue'
import { createPinia } from 'pinia'
import './assets/main.css'
import piniaPluginPersistedstate from 'pinia-plugin-persistedstate'
import App from './App.vue'
import router from './router'
import { userStore } from './stores/user'
import { api, verifyToken } from './services/auth'

const app = createApp(App)
const pinia = createPinia()
async function initializeAuth() {
    const store = userStore()
    const token = store.getSanctumToken()
    if(!token) return
    try{
        const response = await verifyToken()
        const {data} = response
        store.setState(data.user)
    }catch(err){
        store.reset()
    }
}
app.config.errorHandler = (err, instance) => {
    console.log(err.message)
    console.log(instance)
}// this will catch any error whenever app is rendering

// Set up Axios interceptor to add Authorization header dynamically
// Only when the token is available and not already set in the request
// this interceptor will run before any http request and add authorization header to that http request
api.interceptors.request.use((config) => {
    const store = userStore()
    const token = store.getSanctumToken();
    if (token && !config.headers.Authorization) {
        config.headers.Authorization = `Bearer ${token}`;
    }
    return config;
});
api.interceptors.response.use(
    response => response, //if response is success, return response to whoever made this request
    error => { //if the response is not success, do some logic here
        const store = userStore()
        if (error.response?.status === 401) {
            store.removeSanctumToken()
            store.resetState()
            router.replace({
                name: 'login'
            })
        }
        return Promise.reject(error) // return the error the the catch block of whoever made this request
    }
)
pinia.use(piniaPluginPersistedstate)
app.use(pinia)
app.use(router)
await initializeAuth() //must be run before app mount
app.mount('#app')
