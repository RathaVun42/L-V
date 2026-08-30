import './assets/main.css'

import { createApp } from 'vue'
import { createPinia } from 'pinia'
import './assets/main.css'
import piniaPluginPersistedstate from 'pinia-plugin-persistedstate'
import App from './App.vue'
import router from './router'
const app = createApp(App)
const pinia = createPinia()
app.config.errorHandler = (err, instance) => {
    console.log(err.message)
    console.log(instance)
}// this will catch any error whenever app is rendering
pinia.use(piniaPluginPersistedstate)
app.use(pinia)
app.use(router)
app.mount('#app')
