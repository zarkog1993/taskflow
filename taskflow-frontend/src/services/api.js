import axios from 'axios'

const api = axios.create({
  baseURL: import.meta.env.VITE_API_BASE_URL || 'http://localhost:8080/api',
  headers: {
    'Accept': 'application/json',
    'Content-Type': 'application/json'
  },
  withCredentials: true
})

// Request Interceptor: automatski dodaje Sanctum token
api.interceptors.request.use((config) => {
    const token = localStorage.getItem("token")
    if (token) {
        config.headers.Authorization = `Bearer ${token}`
    }
    return config
})

// Response Interceptor: obrada 401 grešaka (istečen token)
api.interceptors.response.use(
    (response) => response,
    (error) => {
        if (error.response?.status === 401
            && error.config?.url !== "/login"
            && localStorage.getItem("token")) {
            localStorage.removeItem("token")
            localStorage.removeItem("user")
            window.location.href = "/"
        }
        return Promise.reject(error)
    }
)

export default api
