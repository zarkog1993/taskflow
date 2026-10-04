// Composable koji objedinjuje podatke i akcije za stranicu utakmica:
// listu mečeva, tabove (predstojeće/odigrane), kreiranje utakmice, zapisnik i brisanje.
import { ref, reactive, computed, onMounted } from 'vue'
import { fetchTeams } from '../../../services/teamsService'
import { fetchMatches as fetchMatchesRequest, createMatch as createMatchRequest, deleteMatch as deleteMatchRequest } from '../../../services/matchesService'

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

    // Filter po mesecu ('all' = svi meseci). Ključ je "YYYY-MM".
    const selectedMonth = ref('all')

    const monthKey = (dateStr) => {
        if (!dateStr) return null
        const date = new Date(dateStr)
        if (Number.isNaN(date.getTime())) return null
        return `${date.getFullYear()}-${String(date.getMonth() + 1).padStart(2, '0')}`
    }

    const monthLabel = (dateStr) => {
        const date = new Date(dateStr)
        const label = date.toLocaleString('sr-Latn-RS', { month: 'long', year: 'numeric' })
        return label.charAt(0).toUpperCase() + label.slice(1)
    }

    const byStatus = (status) => matches.value.filter((m) => m.status === status)

    const inSelectedMonth = (list) =>
        selectedMonth.value === 'all'
            ? list
            : list.filter((m) => monthKey(m.scheduled_at) === selectedMonth.value)

    // Opcije meseca se grade iz svih utakmica, najnoviji mesec prvi.
    const monthOptions = computed(() => {
        const seen = new Map()

        matches.value.forEach((match) => {
            const key = monthKey(match.scheduled_at)
            if (!key || seen.has(key)) return
            seen.set(key, { value: key, label: monthLabel(match.scheduled_at) })
        })

        return [...seen.values()].sort((a, b) => b.value.localeCompare(a.value))
    })

    const upcomingMatches = computed(() => inSelectedMonth(byStatus('scheduled')))
    const completedMatches = computed(() => inSelectedMonth(byStatus('completed')))
    const filteredMatches = computed(() => activeTab.value === 'upcoming' ? upcomingMatches.value : completedMatches.value)

    const selectedMonthLabel = computed(
        () => monthOptions.value.find((o) => o.value === selectedMonth.value)?.label ?? ''
    )

    const resetMonthFilter = () => {
        selectedMonth.value = 'all'
    }

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
        upcomingMatches,
        completedMatches,
        filteredMatches,
        selectedMonth,
        monthOptions,
        selectedMonthLabel,
        resetMonthFilter,
        matchToDelete,
        isDeleting,
        handleDeleteMatch,
        confirmDeleteMatch
    }
}
