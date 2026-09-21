// src/composables/useMatchStats.js
import { ref, computed } from 'vue'
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
        const teamUsers = match.team?.users || match.team?.players || []

        statsForm.value = {
            status: match.status || "scheduled",
            home_score: match.home_score ?? 0,
            away_score: match.away_score ?? 0,
            players: teamUsers.map((user) => {
                const pivotData = match.players?.find((p) => p.id === user.id)?.pivot
                return {
                    id: user.id,
                    name: user.name,
                    jersey_number: user.player_profile?.jersey_number,
                    position: user.player_profile?.primary_position,
                    attended: pivotData ? Boolean(pivotData.attended) : false,
                    goals: pivotData ? pivotData.goals : 0,
                    assists: pivotData ? pivotData.assists : 0,
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