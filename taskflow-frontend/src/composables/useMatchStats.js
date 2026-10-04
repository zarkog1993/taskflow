// src/composables/useMatchStats.js
import { ref } from 'vue'
import api from '../services/api'

export function useMatchStats() {
    const selectedMatch = ref(null)
    const isSaving = ref(false)
    const saveError = ref('')
    const savedMessage = ref('')
    const savedForm = ref('')
    const statsForm = ref({
        status: "scheduled",
        home_score: 0,
        away_score: 0,
        players: [],
    })

    const initializeMatch = (match) => {
        selectedMatch.value = match
        saveError.value = ''
        savedMessage.value = ''

        // Sastav se gradi od stvarnog roster-a ekipe (tabela `players`).
        const roster = match.team?.players || []

        // Već sačuvan zapisnik (pivot match_day_player) i RSVP odgovori na pozivnicu.
        const lineupById = new Map((match.players || []).map((p) => [p.id, p.pivot]))
        const rsvpById = new Map((match.invited_players || []).map((p) => [p.id, p.pivot?.status]))

        statsForm.value = {
            status: match.status || "scheduled",
            home_score: match.home_score ?? 0,
            away_score: match.away_score ?? 0,
            players: roster.map((player) => {
                const pivot = lineupById.get(player.id)
                const rsvpStatus = rsvpById.get(player.id) ?? null

                return {
                    id: player.id,
                    name: player.name,
                    jersey_number: player.jersey_number,
                    position: player.primary_position,
                    rsvp_status: rsvpStatus,
                    // Ako zapisnik još nije sačuvan, igrači koji su potvrdili
                    // dolazak su unapred štiklirani u sastavu.
                    attended: pivot ? Boolean(pivot.attended) : rsvpStatus === 'accepted',
                    goals: pivot?.goals ?? 0,
                    assists: pivot?.assists ?? 0,
                }
            }),
        }
        savedForm.value = JSON.stringify(statsForm.value)
    }

    const saveMatchStats = async () => {
        if (isSaving.value) return
        isSaving.value = true
        saveError.value = ''
        savedMessage.value = ''
        try {
            const response = await api.put(`/matches/${selectedMatch.value.id}/stats`, statsForm.value)
            initializeMatch(response.data.data)
            savedMessage.value = response.data.message || 'Zapisnik je uspešno sačuvan.'
        } catch (err) {
            saveError.value = err.response?.data?.message || "Greška pri čuvanju zapisnika"
        } finally {
            isSaving.value = false
        }
    }

    return {
        selectedMatch,
        isSaving,
        saveError,
        savedMessage,
        savedForm,
        statsForm,
        initializeMatch,
        saveMatchStats
    }
}
