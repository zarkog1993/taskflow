import { defineStore } from 'pinia'
import api from '../services/api'

export const useUserStore = defineStore('user', {
    state: () => ({
        users: [],
        loading: false,
        error: null
    }),

    actions: {
        async fetchUsers() {
            this.loading = true
            this.error = null
            try {
                const response = await api.get('/users')
                // Ako Laravel vraća paginirane podatke (Resource Collection)
                if (response.data.data && Array.isArray(response.data.data)) {
                    this.users = response.data.data
                } else if (response.data.data?.data && Array.isArray(response.data.data.data)) {
                    this.users = response.data.data.data
                } else {
                    this.users = response.data
                }
            } catch (err) {
                this.error = err.response?.data?.message || 'Greška pri učitavanju korisnika.'
            } finally {
                this.loading = false
            }
        }
    }
})