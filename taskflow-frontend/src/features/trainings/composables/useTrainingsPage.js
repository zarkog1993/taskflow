// Composable koji objedinjuje podatke i akcije za stranicu trening sesija:
// navigaciju kroz mesece, kreiranje treninga, evidenciju prisustva i brisanje.
import { ref, reactive, computed, onMounted } from 'vue'
import { useTrainingStore } from '../../../stores/training'
import { useUserStore } from '../../../stores/user'
import { fetchTeams } from '../../../services/teamsService'
import { getAttendedCount } from '../utils/trainingFormatters'

export function useTrainingsPage() {
    const trainingStore = useTrainingStore()
    const userStore = useUserStore()

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

    const averageMonthlyAttendance = computed(() => {
        if (!monthlySessions.value.length || !userStore.users.length) return 0
        let totalAttended = 0
        const totalPossible = monthlySessions.value.length * userStore.users.length

        monthlySessions.value.forEach(s => {
            totalAttended += getAttendedCount(s)
        })

        return Math.round((totalAttended / totalPossible) * 100) || 0
    })

    const changeMonth = (step) => {
        currentDate.value = new Date(currentYear.value, currentMonth.value + step, 1)
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

    // Evidencija prisustva
    const showAttendanceModal = ref(false)
    const activeSession = ref(null)
    const selectedAttendeeIds = ref([])

    const openAttendanceModal = (session) => {
        activeSession.value = session
        const attendeesList = session.users || session.attendees || []
        selectedAttendeeIds.value = attendeesList.map(a => a.id)
        showAttendanceModal.value = true
    }

    const togglePlayerAttendance = (userId) => {
        const idx = selectedAttendeeIds.value.indexOf(userId)
        if (idx > -1) {
            selectedAttendeeIds.value.splice(idx, 1)
        } else {
            selectedAttendeeIds.value.push(userId)
        }
    }

    const selectAllPlayers = () => {
        selectedAttendeeIds.value = userStore.users.map(u => u.id)
    }

    const clearAllPlayers = () => {
        selectedAttendeeIds.value = []
    }

    const handleSaveAttendance = async () => {
        if (!activeSession.value) return
        const success = await trainingStore.saveAttendance(activeSession.value.id, selectedAttendeeIds.value)
        if (success) {
            showAttendanceModal.value = false
        }
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
        userStore.fetchUsers()
        fetchTeams().then((list) => {
            teams.value = list
            if (teams.value.length) {
                newSession.team_id = teams.value[0].id
            }
        })
    })

    return {
        userStore,
        teams,
        newSession,
        formDate,
        formHours,
        formMinutes,
        showCreateModal,
        currentYear,
        currentMonthName,
        monthlySessions,
        nextUpcomingSession,
        averageMonthlyAttendance,
        changeMonth,
        handleCreateSession,
        showAttendanceModal,
        activeSession,
        selectedAttendeeIds,
        openAttendanceModal,
        togglePlayerAttendance,
        selectAllPlayers,
        clearAllPlayers,
        handleSaveAttendance,
        sessionToDelete,
        isDeleting,
        handleDeleteSession,
        confirmDeleteSession
    }
}
