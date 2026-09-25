// Composable koji objedinjuje podatke i izvedene metrike za stranicu kontrolne table:
// učitava ekipe/korisnike/treninge i izračunava KPI, sledeću aktivnost i top igrače.
import { computed, onMounted } from 'vue'
import { useAuthStore } from '../../../stores/auth'
import { useTeamStore } from '../../../stores/team'
import { useUserStore } from '../../../stores/user'
import { useTrainingStore } from '../../../stores/training'

export function useDashboardMetrics() {
    const authStore = useAuthStore()
    const teamStore = useTeamStore()
    const userStore = useUserStore()
    const trainingStore = useTrainingStore()

    onMounted(() => {
        teamStore.fetchTeams()
        userStore.fetchUsers()
        trainingStore.fetchSessions()
    })

    const upcomingSessionsCount = computed(() => {
        return trainingStore.sessions.filter(s => s.status === 'planned').length
    })

    const totalGoalsCount = computed(() => {
        return userStore.users.reduce((sum, u) => sum + (u.player_profile?.goals || 0), 0)
    })

    const nextSession = computed(() => {
        return trainingStore.sessions.find(s => s.status === 'planned') || null
    })

    const topPlayers = computed(() => {
        return [...userStore.users]
            .sort((a, b) => (b.player_profile?.goals || 0) - (a.player_profile?.goals || 0))
            .slice(0, 5)
    })

    const formatDate = (dateStr) => {
        if (!dateStr) return ''
        return new Date(dateStr).toLocaleString('sr-RS', { day: 'numeric', month: 'short', hour: '2-digit', minute: '2-digit' })
    }

    return {
        authStore,
        teamStore,
        userStore,
        upcomingSessionsCount,
        totalGoalsCount,
        nextSession,
        topPlayers,
        formatDate
    }
}
