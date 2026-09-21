import { defineStore } from 'pinia'
import api from '../services/api'

export const useMatchStore = defineStore('matches', {
    state: () => ({
        matches: [],
        loading: false,
        error: null
    }),

    getters: {
        upcomingMatches: (state) => state.matches.filter(m => m.status === 'scheduled'),
        inProgressMatches: (state) => state.matches.filter(m => m.status === 'in_progress'),
        completedMatches: (state) => state.matches.filter(m => m.status === 'completed'),
    },

    actions: {
        async fetchMatches() {
            this.loading = true
            this.error = null
            try {
                const response = await api.get('/matches')
                this.matches = response.data.data || response.data
            } catch (err) {
                this.error = err.response?.data?.message || 'Greška pri učitavanju utakmica.'
            } finally {
                this.loading = false
            }
        },

        async createMatch(matchData) {
            this.error = null
            try {
                const response = await api.post('/matches', matchData)
                const created = response.data.data || response.data
                this.matches.unshift(created)
                return true
            } catch (err) {
                this.error = err.response?.data?.message || 'Greška pri kreiranju utakmice.'
                return false
            }
        },

        async updateMatchStatus(matchId, newStatus) {
            const match = this.matches.find(m => m.id === matchId)
            if (!match) return

            const oldStatus = match.status
            match.status = newStatus

            try {
                await api.put(`/matches/${matchId}/status`, { status: newStatus })
            } catch (err) {
                match.status = oldStatus
                this.error = err.response?.data?.message || 'Greška pri izmeni statusa.'
            }
        },

        async saveAttendance(matchId, playerIds) {
            try {
                const res = await api.post(`/matches/${matchId}/attendance`, { player_ids: playerIds })
                const match = this.matches.find(m => m.id === matchId)
                if (match) {
                    match.attendees = res.data.data.attendees
                }
                return true
            } catch (err) {
                alert('Greška pri čuvanju prisustva')
                return false
            }
        },

        async deleteMatch(matchId) {
            try {
                await api.delete(`/matches/${matchId}`);
                // Uklanjamo obrisani meč iz lokalnog state-a
                this.matches = this.matches.filter(m => m.id !== matchId);
                return true;
            } catch (err) {
                console.error('Greška pri brisanju meča:', err);
                alert('Došlo je do greške prilikom brisanja meča.');
                return false;
            }
        }
    }
})
