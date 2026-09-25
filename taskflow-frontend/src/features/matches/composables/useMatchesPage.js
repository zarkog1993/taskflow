// Composable koji objedinjuje podatke i akcije za stranicu utakmica:
// listu mečeva, tabove (predstojeće/odigrane), kreiranje utakmice, zapisnik i brisanje.
import { ref, reactive, computed, onMounted } from 'vue'
import { fetchTeams } from '../../../services/teamsService'
import { fetchMatches as fetchMatchesRequest, createMatch as createMatchRequest, deleteMatch as deleteMatchRequest } from '../../../services/matchesService'
import { useMatchStats } from '../../../composables/useMatchStats'

export function useMatchesPage() {
    const matches = ref([])
    const teams = ref([])
    const activeTab = ref('upcoming')
    const showCreateModal = ref(false)
    const newMatch = reactive({
        team_id: '',
        opponent: '',
        is_home: true,
        scheduled_at: '',
        location: ''
    })

    const fetchMatches = async () => {
        try {
            const res = await fetchMatchesRequest()
            matches.value = res.data.data
        } catch (err) {
            console.error('Greška pri učitavanju utakmica:', err)
        }
    }

    const fetchTeamsList = async () => {
        teams.value = await fetchTeams()
        if (!newMatch.team_id && teams.value.length) {
            newMatch.team_id = teams.value[0].id
        }
    }

    const openCreateModal = async () => {
        if (!teams.value.length) {
            await fetchTeamsList()
        }
        showCreateModal.value = true
    }

    const createMatch = async () => {
        try {
            await createMatchRequest(newMatch)
            showCreateModal.value = false
            newMatch.opponent = ''
            newMatch.scheduled_at = ''
            newMatch.location = ''
            await fetchMatches()
        } catch (err) {
            window.alert(err.response?.data?.message || 'Utakmica nije mogla biti zakazana.')
        }
    }

    const { selectedMatch, modalTab, statsForm, openStatsModal, saveMatchStats } = useMatchStats(fetchMatches)

    const upcomingMatches = computed(() => matches.value.filter((m) => m.status === 'scheduled'))
    const completedMatches = computed(() => matches.value.filter((m) => m.status === 'completed'))
    const filteredMatches = computed(() => activeTab.value === 'upcoming' ? upcomingMatches.value : completedMatches.value)

    const matchToDelete = ref(null)
    const isDeleting = ref(false)

    const handleDeleteMatch = (match) => {
        matchToDelete.value = match
    }

    const confirmDeleteMatch = async () => {
        if (!matchToDelete.value) return

        isDeleting.value = true
        try {
            await deleteMatchRequest(matchToDelete.value.id)
            // Osvežavamo listu mečeva nakon brisanja
            await fetchMatches()
            matchToDelete.value = null
        } catch (err) {
            console.error('Greška pri brisanju meča:', err)
            alert('Došlo je do greške prilikom brisanja.')
        } finally {
            isDeleting.value = false
        }
    }

    onMounted(() => {
        fetchMatches()
        fetchTeamsList()
    })

    return {
        matches,
        teams,
        activeTab,
        showCreateModal,
        newMatch,
        openCreateModal,
        createMatch,
        selectedMatch,
        modalTab,
        statsForm,
        openStatsModal,
        saveMatchStats,
        upcomingMatches,
        completedMatches,
        filteredMatches,
        matchToDelete,
        isDeleting,
        handleDeleteMatch,
        confirmDeleteMatch
    }
}
