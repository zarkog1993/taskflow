import { defineStore } from "pinia"
import api from "../services/api"

// Pomoćna funkcija za bezbedno parsiranje iz local storage-a
const getInitialUser = () => {
    const item = localStorage.getItem("user")
    if (!item || item === "undefined") return null
    try {
        return JSON.parse(item)
    } catch (e) {
        return null
    }
}

export const useAuthStore = defineStore("auth", {
    state: () => ({
        user: getInitialUser(),
        token: localStorage.getItem("token") || null,
        loading: false,
        error: null
    }),

    getters: {
        isAuthenticated: (state) => !!state.token,
    },

    actions: {
        async login(credentials) {
            this.loading = true
            this.error = null
            try {
                const response = await api.post("/login", credentials)

                // Izvlačenje podataka na osnovu tvog API odziva
                const resData = response.data.data || response.data
                const tokenData = resData.access_token || resData.token
                const userData = resData.user

                this.token = tokenData
                this.user = userData

                if (this.token) {
                    localStorage.setItem("token", this.token)
                }
                if (this.user) {
                    localStorage.setItem("user", JSON.stringify(this.user))
                }

                return true
            } catch (err) {
                this.error = err.response?.data?.message || "Neuspešna prijava."
                return false
            } finally {
                this.loading = false
            }
        },

        async logout() {
            try {
                await api.post("/logout")
            } catch (e) {
                // Ignorišemo grešku ako je token istekao
            } finally {
                this.user = null
                this.token = null
                localStorage.removeItem("token")
                localStorage.removeItem("user")
            }
        }
    }
})
