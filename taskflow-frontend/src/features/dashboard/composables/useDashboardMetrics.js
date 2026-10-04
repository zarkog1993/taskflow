// Composable koji objedinjuje podatke i izvedene metrike za stranicu kontrolne table:
// učitava ekipe/treninge i izračunava KPI, sledeću aktivnost i najbolje igrače.
import { computed, onMounted } from 'vue'
import { useAuthStore } from '../../../stores/auth'
import { useTeamStore } from '../../../stores/team'
import { useTrainingStore } from '../../../stores/training'

export function useDashboardMetrics() {
    const authStore = useAuthStore()
    const teamStore = useTeamStore()
    const trainingStore = useTrainingStore()

    onMounted(() => {
        teamStore.fetchTeams()
        trainingStore.fetchSessions()
    })

    const players = computed(() => teamStore.teams.flatMap(team => team.players || []))

    const upcomingSessionsCount = computed(() => {
        const now = Date.now()
        return trainingStore.sessions.filter(session =>
            session.status === 'planned' && new Date(session.scheduled_at).getTime() >= now
        ).length
    })

    const totalGoalsCount = computed(() => {
        return players.value.reduce((sum, player) => sum + (player.goals || 0), 0)
    })

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
        players,
        upcomingSessionsCount,
        totalGoalsCount,
        nextSession,
        topPlayers,
        formatDate
    }
}
