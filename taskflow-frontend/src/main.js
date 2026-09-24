import { createApp } from 'vue'
import { createPinia } from 'pinia'
import router from './router'
import './assets/main.css'
import App from './App.vue'
import axios from 'axios';
window.axios = axios;

const app = createApp(App)

app.use(createPinia())
app.use(router)
app.mount('#app')
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';
window.axios.defaults.withCredentials = true; // Obavezno za Sanctum sesije/kolacice

// Postavi tačan port tvoje Laravel aplikacije (npr. 8000)
window.axios.defaults.baseURL = 'http://localhost:8080';

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest'

// Automatsko presretanje svih zahtjeva i dodavanje Bearer tokena
window.axios.interceptors.request.use((config) => {
    const token = localStorage.getItem('token') // Provjeri ključ pod kojim čuvaš token nakon logina
    if (token) {
        config.headers.Authorization = `Bearer ${token}`
    }
    return config;
}, (error) => {
    return Promise.reject(error)
})
