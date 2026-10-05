// Composable koji objedinjuje podatke i izvedene metrike za stranicu kontrolne table:
// učitava ekipe/treninge i izračunava KPI, sledeću aktivnost i najbolje igrače.
import { computed, onMounted, ref } from 'vue'
import { useAuthStore } from '../../../stores/auth'
import { useTeamStore } from '../../../stores/team'
import { useTrainingStore } from '../../../stores/training'
import { fetchMatches } from '../../../services/matchesService'

export function useDashboardMetrics() {
    const authStore = useAuthStore()
    const teamStore = useTeamStore()
    const trainingStore = useTrainingStore()
    const matches = ref([])
    const matchesLoading = ref(true)
    const matchesLoadFailed = ref(false)

    const canAccessMatches = computed(() => {
        const user = authStore.user
        return user?.subscription_features?.includes('matches')
            || user?.is_admin === 1
            || user?.is_admin === '1'
            || user?.is_admin === true
            || user?.roles?.some(role => role.slug === 'super-admin' || role.name === 'Super Admin')
    })

    onMounted(() => {
        teamStore.fetchTeams()
        trainingStore.fetchSessions()
        if (canAccessMatches.value) {
            fetchDashboardMatches()
        } else {
            matchesLoading.value = false
        }
    })

    const fetchDashboardMatches = async () => {
        try {
            const response = await fetchMatches()
            matches.value = response.data.data || []
        } catch (error) {
            matchesLoadFailed.value = true
            console.error('Greška pri učitavanju utakmica za kontrolnu tablu:', error)
        } finally {
            matchesLoading.value = false
        }
    }

    const players = computed(() => teamStore.teams.flatMap(team => team.players || []))

    const upcomingSessionsCount = computed(() => {
        const now = Date.now()
        return trainingStore.sessions.filter(session =>
            session.status === 'planned' && new Date(session.scheduled_at).getTime() >= now
        ).length
    })

    const upcomingMatches = computed(() => matches.value
        .filter(match => match.status === 'scheduled' && new Date(match.scheduled_at).getTime() >= Date.now())
        .sort((a, b) => new Date(a.scheduled_at) - new Date(b.scheduled_at)))

    const latestCompletedMatch = computed(() => matches.value
        .filter(match => match.status === 'completed')
        .sort((a, b) => new Date(b.scheduled_at) - new Date(a.scheduled_at))[0] || null)

    const featuredMatch = computed(() => upcomingMatches.value[0] || latestCompletedMatch.value)
    const isUpcomingMatch = computed(() => upcomingMatches.value.length > 0)

    const nextSession = computed(() => {
        return trainingStore.sessions
            .filter(session =>
                session.status === 'planned' && new Date(session.scheduled_at).getTime() >= Date.now()
            )
            .sort((a, b) => new Date(a.scheduled_at) - new Date(b.scheduled_at))[0] || null
    })

    const topPlayers = computed(() => {
        return [...players.value]
            .sort((a, b) => (b.goals || 0) - (a.goals || 0))
            .slice(0, 5)
    })

    const formatDate = (dateStr) => {
        if (!dateStr) return ''
        return new Date(dateStr).toLocaleString('sr-RS', { day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit' })
    }

    return {
        authStore,
        teamStore,
        trainingStore,
        players,
        upcomingSessionsCount,
        featuredMatch,
        isUpcomingMatch,
        canAccessMatches,
        matchesLoading,
        matchesLoadFailed,
        nextSession,
        topPlayers,
        formatDate
    }
}
