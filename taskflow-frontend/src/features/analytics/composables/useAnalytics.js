import { ref, computed, onMounted, watch } from 'vue'
import api from '../../../services/api'

const EMPTY_SUMMARY = {
    total_matches: 0,
    total_goals: 0,
    total_assists: 0,
    goals_per_match: 0,
    total_trainings: 0,
    avg_attended_players: 0,
    avg_attendance_rate: 0
}

const loading = ref(true)
const selectedTeamId = ref('all')
const searchQuery = ref('')
const dateFrom = ref('')
const dateTo = ref('')

const teams = ref([])
const players = ref([])
const summary = ref({ ...EMPTY_SUMMARY })

// Poređenje bez obzira na dijakritike: "milos" i "djordje" pronalaze
// "Miloš" i "Đorđe". Sekvenca "dj" se izjednačava sa "đ".
const normalize = (value) =>
    (value ?? '')
        .toString()
        .normalize('NFD')
        .replace(/[\u0300-\u036f]/g, '')
        .replace(/đ/gi, (match) => (match === 'đ' ? 'dj' : 'Dj'))
        .toLowerCase()
        .replace(/dj/g, 'd')
        .trim()

export function useAnalytics() {
    const fetchAnalytics = async () => {
        loading.value = true
        try {
            // Filteri se šalju backendu da bi i zbirne metrike (npr. prosečan
            // odaziv) bile izračunate za izabranu selekciju i period.
            const params = {}
            if (selectedTeamId.value && selectedTeamId.value !== 'all') {
                params.team_id = selectedTeamId.value
            }
            if (dateFrom.value) params.date_from = dateFrom.value
            if (dateTo.value) params.date_to = dateTo.value

            const res = await api.get('/analytics', { params })
            teams.value = res.data.teams || []
            players.value = res.data.players || []
            summary.value = { ...EMPTY_SUMMARY, ...(res.data.summary || {}) }
        } catch (err) {
            console.error('Greška pri učitavanju analitike:', err)
        } finally {
            loading.value = false
        }
    }

    onMounted(fetchAnalytics)

    watch([selectedTeamId, dateFrom, dateTo], fetchAnalytics)

    const filteredPlayers = computed(() => {
        let result = players.value

        if (selectedTeamId.value !== 'all') {
            result = result.filter(p => p.team_id == selectedTeamId.value)
        }

        const term = normalize(searchQuery.value)
        if (term) {
            // Pretraga po imenu, poziciji, broju dresa i nazivu ekipe.
            result = result.filter(p =>
                normalize(p.name).includes(term)
                || normalize(p.primary_position).includes(term)
                || normalize(p.team_name).includes(term)
                || String(p.jersey_number ?? '').includes(term)
            )
        }

        return result
    })

    const resetFilters = () => {
        searchQuery.value = ''
        selectedTeamId.value = 'all'
    }

    return {
        loading,
        selectedTeamId,
        searchQuery,
        dateFrom,
        dateTo,
        teams,
        players,
        summary,
        filteredPlayers,
        resetFilters,
        fetchAnalytics
    }
}
