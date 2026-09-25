import { ref, computed, onMounted } from 'vue'
import api from '../../../services/api'

const loading = ref(true)
const selectedTeamId = ref('all')

const teams = ref([])
const players = ref([])
const summary = ref({
    total_matches: 0,
    total_goals: 0,
    total_assists: 0,
    goals_per_match: 0,
    avg_attendance_rate: 0
})

export function useAnalytics() {
    const fetchAnalytics = async () => {
        loading.value = true
        try {
            const res = await api.get('/analytics')
            teams.value = res.data.teams || []
            players.value = res.data.players || []
            summary.value = res.data.summary || {
                total_matches: 0,
                total_goals: 0,
                total_assists: 0,
                goals_per_match: 0,
                avg_attendance_rate: 0
            }
        } catch (err) {
            console.error('Greška pri učitavanju analitike:', err)
        } finally {
            loading.value = false
        }
    }

    onMounted(fetchAnalytics)

    const filteredPlayers = computed(() => {
        if (selectedTeamId.value === 'all') return players.value
        return players.value.filter(p => p.team_id == selectedTeamId.value)
    })
    return {
        loading,
        selectedTeamId,
        teams,
        players,
        summary,
        filteredPlayers,
        fetchAnalytics
    }
}