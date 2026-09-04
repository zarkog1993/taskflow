import { defineStore } from 'pinia'
import api from '../services/api'

export const useTrainingStore = defineStore('training', {
    state: () => ({
        sessions: [],
        loading: false,
        error: null
    }),

    getters: {
        plannedSessions: (state) => state.sessions.filter(s => s.status === 'planned'),
        inProgressSessions: (state) => state.sessions.filter(s => s.status === 'in_progress'),
        completedSessions: (state) => state.sessions.filter(s => s.status === 'completed'),
    },

    actions: {
        async fetchSessions() {
            this.loading = true
            this.error = null
            try {
                const response = await api.get('/training-sessions')
                this.sessions = response.data.data || response.data
            } catch (err) {
                this.error = err.response?.data?.message || 'Greška pri učitavanju treninga.'
            } finally {
                this.loading = false
            }
        },

        async createSession(sessionData) {
            this.error = null
            try {
                const response = await api.post('/training-sessions', sessionData)
                const created = response.data.data || response.data
                this.sessions.unshift(created)
                return true
            } catch (err) {
                this.error = err.response?.data?.message || 'Greška pri kreiranju treninga.'
                return false
            }
        },

        async updateSessionStatus(sessionId, newStatus) {
            const session = this.sessions.find(s => s.id === sessionId)
            if (!session) return

            const oldStatus = session.status
            session.status = newStatus

            try {
                await api.put(`/training-sessions/${sessionId}/status`, { status: newStatus })
            } catch (err) {
                session.status = oldStatus
                this.error = err.response?.data?.message || 'Greška pri izmeni statusa.'
            }
        }
    }
})
