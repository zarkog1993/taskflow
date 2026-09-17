import { defineStore } from 'pinia'
import api from '../services/api'

export const useTeamStore = defineStore('team', {
    state: () => ({
        teams: [],
        loading: false
    }),

    actions: {
        async fetchTeams() {
            this.loading = true
            try {
                const res = await api.get('/teams')
                // Normalizujemo podatke: preslikavamo 'users' u 'members' ako backend vrati 'users'
                this.teams = (res.data.data || []).map(team => ({
                    ...team,
                    members: team.members || team.users || []
                }))
            } catch (err) {
                console.error('Greška pri učitavanju ekipa:', err)
            } finally {
                this.loading = false
            }
        },

        async assignMembers(teamId, userIds) {
            try {
                const res = await api.post(`/teams/${teamId}/members`, {
                    user_ids: userIds
                })

                const updatedTeam = res.data.data

                // Osvežavamo tim u lokalom Pinia stanju sa 'members' ključem
                const index = this.teams.findIndex(t => t.id === teamId)
                if (index !== -1) {
                    this.teams[index] = {
                        ...updatedTeam,
                        members: updatedTeam.members || updatedTeam.users || []
                    }
                }

                return true
            } catch (err) {
                alert(err.response?.data?.message || 'Greška pri čuvanju sastava')
                return false
            }
        }
    }
})
