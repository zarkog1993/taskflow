// src/composables/useMatchStats.js
import { ref } from 'vue'
import api from '../services/api'

export function useMatchStats(onSuccessCallback) {
    const selectedMatch = ref(null)
    const modalTab = ref("info")
    const statsForm = ref({
        status: "scheduled",
        home_score: 0,
        away_score: 0,
        players: [],
    })

    const openStatsModal = (match) => {
        selectedMatch.value = match
        modalTab.value = "info"

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
    }

    const saveMatchStats = async () => {
        try {
            await api.put(`/matches/${selectedMatch.value.id}/stats`, statsForm.value)
            selectedMatch.value = null
            if (onSuccessCallback) await onSuccessCallback()
        } catch (err) {
            alert(err.response?.data?.message || "Greška pri čuvanju zapisnika")
        }
    }

    return {
        selectedMatch,
        modalTab,
        statsForm,
        openStatsModal,
        saveMatchStats
    }
}
