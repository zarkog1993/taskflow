import { defineStore } from 'pinia'
import api from '../services/api'

export const useTeamStore = defineStore('team', {
    state: () => ({
        teams: [],
        loading: false,
        error: null
    }),

    actions: {
        async fetchTeams() {
            this.loading = true
            this.error = null
            try {
                const response = await api.get('/teams')
                this.teams = response.data.data || response.data
            } catch (err) {
                this.error = err.response?.data?.message || 'Greška pri učitavanju timova.'
            } finally {
                this.loading = false
            }
        },

        async createTeam(teamData) {
            this.error = null
            try {
                const response = await api.post('/teams', teamData)
                const created = response.data.data || response.data
                this.teams.push(created)
                return true
            } catch (err) {
                this.error = err.response?.data?.message || 'Greška pri kreiranju tima.'
                return false
            }
        },

        async assignMembers(teamId, userIds) {
            this.error = null
            try {
                const response = await api.post(`/teams/${teamId}/members`, { user_ids: userIds })
                const updated = response.data.data || response.data
                const index = this.teams.findIndex(t => t.id === teamId)
                if (index !== -1) {
                    this.teams[index] = updated
                }
                return true
            } catch (err) {
                this.error = err.response?.data?.message || 'Greška pri dodeljivanju igrača.'
                return false
            }
        }
    }
})
