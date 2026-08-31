import { defineStore } from "pinia"
import api from "../src/api"

export const useAuthStore = defineStore("auth", {
    state: () => ({
        user: JSON.parse(localStorage.getItem("user")) || null,
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
                this.token = response.data.token
                this.user = response.data.user

                localStorage.setItem("token", this.token)
                localStorage.setItem("user", JSON.stringify(this.user))
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
                // Ignorišemo grešku ako je token već nevažeći
            } finally {
                this.user = null
                this.token = null
                localStorage.removeItem("token")
                localStorage.removeItem("user")
            }
        }
    }
})
