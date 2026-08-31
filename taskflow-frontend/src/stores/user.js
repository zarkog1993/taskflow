import { defineStore } from 'pinia'
import api from '../services/api'

export const useUserStore = defineStore('user', {
    state: () => ({
        users: [],
        roles: [],
        loading: false,
        error: null
    }),

    actions: {
        async fetchUsers() {
            this.loading = true
            this.error = null
            try {
                const response = await api.get('/users')
                // Rukovanje i sa paginiranim i sa običnim Resource collection odzivom
                this.users = response.data.data || response.data
            } catch (err) {
                this.error = err.response?.data?.message || 'Greška pri učitavanju korisnika.'
            } finally {
                this.loading = false
            }
        },

        async fetchRoles() {
            try {
                const response = await api.get('/roles')
                this.roles = response.data.data || response.data
            } catch (err) {
                console.error('Greška pri učitavanju uloga:', err)
            }
        },

        async updateUserRoles(userId, roleIds) {
            try {
                await api.put(`/users/${userId}/roles`, { roles: roleIds })
                await this.fetchUsers()
                return true
            } catch (err) {
                this.error = err.response?.data?.message || 'Greška pri ažuriranju uloga.'
                return false
            }
        }
    }
})
