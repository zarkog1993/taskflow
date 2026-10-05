// Composable koji objedinjuje podatke i akcije za stranicu trening sesija:
// navigaciju kroz mesece, kreiranje treninga, evidenciju prisustva i brisanje.
import { ref, reactive, computed, onMounted } from 'vue'
import { useTrainingStore } from '../../../stores/training'
import { useAuthStore } from '../../../stores/auth'
import { fetchTeams } from '../../../services/teamsService'
import { getInvitedPlayers, getRsvpCounts } from '../utils/trainingFormatters'

export function useTrainingsPage() {
    const trainingStore = useTrainingStore()
    const authStore = useAuthStore()

    const currentDate = ref(new Date())
    const teams = ref([])

    const newSession = reactive({
        title: '',
        type: 'training',
        scheduled_at: '',
        location: '',
        description: '',
        status: 'planned',
        team_id: ''
    })

    const formDate = ref(new Date().toISOString().split('T')[0])
    const formHours = ref('18')
    const formMinutes = ref('00')
    const showCreateModal = ref(false)
    const sessionToEdit = ref(null)
    const isSavingSession = ref(false)
    const editSessionForm = reactive({
        title: '',
        type: 'training',
        scheduled_at: '',
        location: '',
        description: '',
        status: 'planned',
        team_id: '',
        player_observations: {}
    })

    const editAttendees = computed(() => sessionToEdit.value
        ? getInvitedPlayers(sessionToEdit.value).filter(player => player.pivot?.status === 'accepted')
        : [])
    const canManageSessions = computed(() => {
        const user = authStore.user
        return Boolean(user?.is_admin || user?.roles?.some(role =>
            ['admin', 'super-admin', 'club-admin'].includes(role.slug)
        ))
    })

    const currentYear = computed(() => currentDate.value.getFullYear())
    const currentMonth = computed(() => currentDate.value.getMonth())

    const currentMonthName = computed(() => {
        return currentDate.value.toLocaleString('sr-RS', { month: 'long' })
    })

    const monthlySessions = computed(() => {
        return trainingStore.sessions
            .filter(s => {
                const d = new Date(s.scheduled_at)
                return d.getMonth() === currentMonth.value && d.getFullYear() === currentYear.value
            })
            .sort((a, b) => new Date(a.scheduled_at) - new Date(b.scheduled_at))
    })

    const nextUpcomingSession = computed(() => {
        const now = new Date()
        return trainingStore.sessions
            .filter(s => new Date(s.scheduled_at) >= now)
            .sort((a, b) => new Date(a.scheduled_at) - new Date(b.scheduled_at))[0]
    })

    // Odziv = potvrđeni dolasci u odnosu na broj poslatih pozivnica.
    const averageMonthlyAttendance = computed(() => {
        let accepted = 0
        let invited = 0

        monthlySessions.value.forEach(s => {
            const counts = getRsvpCounts(s)
            accepted += counts.accepted
            invited += counts.total
        })

        if (!invited) return 0

        return Math.round((accepted / invited) * 100)
    })

    const changeMonth = (step) => {
        currentDate.value = new Date(currentYear.value, currentMonth.value + step, 1)
    }

    const handleOpenCreateModal = () => {
        sessionToEdit.value = null
        const today = new Date()
        formDate.value = `${today.getFullYear()}-${String(today.getMonth() + 1).padStart(2, '0')}-${String(today.getDate()).padStart(2, '0')}`
        formHours.value = '18'
        formMinutes.value = '00'
        showCreateModal.value = true
    }

    const handleCreateSession = async () => {
        newSession.scheduled_at = `${formDate.value} ${formHours.value}:${formMinutes.value}:00`
        const success = await trainingStore.createSession(newSession)
        if (success) {
            showCreateModal.value = false
            newSession.title = ''
            newSession.location = ''
            newSession.description = ''
        }
    }

    const handleEditSession = (session) => {
        sessionToEdit.value = session
        const scheduledAt = new Date(session.scheduled_at)
        formDate.value = `${scheduledAt.getFullYear()}-${String(scheduledAt.getMonth() + 1).padStart(2, '0')}-${String(scheduledAt.getDate()).padStart(2, '0')}`
        formHours.value = String(scheduledAt.getHours()).padStart(2, '0')
        formMinutes.value = String(scheduledAt.getMinutes()).padStart(2, '0')
        Object.assign(editSessionForm, {
            title: session.title,
            type: session.type || 'training',
            scheduled_at: session.scheduled_at,
            location: session.location || '',
            description: session.description || '',
            status: session.status,
            team_id: session.team_id,
            player_observations: Object.fromEntries(editAttendees.value.map(player => [
                player.id,
                player.pivot?.training_observation || ''
            ]))
        })
    }

    const handleUpdateSession = async () => {
        if (!sessionToEdit.value) return

        editSessionForm.scheduled_at = `${formDate.value} ${formHours.value}:${formMinutes.value}:00`
        isSavingSession.value = true
        const success = await trainingStore.updateSession(sessionToEdit.value.id, editSessionForm)
        isSavingSession.value = false

        if (success) sessionToEdit.value = null
    }

    // Brisanje treninga
    const sessionToDelete = ref(null)
    const isDeleting = ref(false)

    const handleDeleteSession = (session) => {
        sessionToDelete.value = session
    }

    const confirmDeleteSession = async () => {
        if (!sessionToDelete.value) return

        isDeleting.value = true
        const success = await trainingStore.deleteSession(sessionToDelete.value.id)
        isDeleting.value = false

        if (success) {
            sessionToDelete.value = null
        }
    }

    onMounted(() => {
        trainingStore.fetchSessions()
        fetchTeams().then((list) => {
            teams.value = list
            if (teams.value.length) {
                newSession.team_id = teams.value[0].id
            }
        })
    })

    return {
        trainingStore,
        teams,
        newSession,
        formDate,
        formHours,
        formMinutes,
        showCreateModal,
        canManageSessions,
        sessionToEdit,
        editSessionForm,
        editAttendees,
        isSavingSession,
        currentYear,
        currentMonthName,
        monthlySessions,
        nextUpcomingSession,
        averageMonthlyAttendance,
        changeMonth,
        handleOpenCreateModal,
        handleCreateSession,
        handleEditSession,
        handleUpdateSession,
        sessionToDelete,
        isDeleting,
        handleDeleteSession,
        confirmDeleteSession
    }
}
