// Composable koji upravlja učitavanjem događaja (treninzi/utakmice) i ekipa,
// kreiranjem novog događaja (uključujući selekciju igrača) i prikazom detalja događaja.
import { ref, computed } from 'vue'
import api from '../../../services/api'
import { normalizeTraining, normalizeMatch } from '../utils/calendarFormatters'

export function useCalendarEvents() {
    const events = ref([])
    const teams = ref([])
    const availablePlayers = ref([])
    const showCreateModal = ref(false)
    const selectedEvent = ref(null)

    const eventForm = ref({
        eventType: 'match',
        team_id: '',
        title: '',
        opponent: '',
        is_home: true,
        scheduled_at: '',
        location: ''
    })

    const fetchAllData = async () => {
        try {
            const [trainingsRes, matchesRes, teamsRes] = await Promise.all([
                api.get('/training-sessions'),
                api.get('/matches'),
                api.get('/teams')
            ])

            teams.value = teamsRes.data.data

            // Mapiramo i čuvamo kompletne objekte uključujući users i team relacije
            const formattedTrainings = (trainingsRes.data.data || []).map(normalizeTraining)
            const formattedMatches = (matchesRes.data.data || []).map(normalizeMatch)

            events.value = [...formattedTrainings, ...formattedMatches]
        } catch (err) {
            console.error('Greška pri učitavanju kalendara:', err)
        }
    }

    const loadPlayersForTeam = (teamId) => {
        if (!teamId) {
            availablePlayers.value = []
            return
        }

        const selectedTeam = teams.value.find(
            (t) => t.id === Number(teamId) || t.id === teamId
        )
        const teamUsers = selectedTeam?.users || selectedTeam?.players || []

        availablePlayers.value = teamUsers.map((user) => ({
            ...user,
            selected: true
        }))
    }

    const onTeamChange = () => {
        loadPlayersForTeam(eventForm.value.team_id)
    }

    const openCreateModal = () => {
        const defaultTeamId = teams.value[0]?.id || ''
        eventForm.value = {
            eventType: 'match',
            team_id: defaultTeamId,
            title: '',
            opponent: '',
            is_home: true,
            scheduled_at: '',
            location: ''
        }

        loadPlayersForTeam(defaultTeamId)
        showCreateModal.value = true
    }

    const selectedPlayersCount = computed(
        () => availablePlayers.value.filter((p) => p.selected).length
    )
    const allSelected = computed(
        () =>
            availablePlayers.value.length > 0 &&
            availablePlayers.value.every((p) => p.selected)
    )

    const toggleSelectAll = () => {
        const targetState = !allSelected.value
        availablePlayers.value.forEach((p) => (p.selected = targetState))
    }

    const handleCreateEvent = async () => {
        try {
            const selectedUserIds = availablePlayers.value
                .filter((p) => p.selected)
                .map((p) => p.id)

            if (eventForm.value.eventType === 'training') {
                await api.post('/training-sessions', {
                    team_id: eventForm.value.team_id,
                    title: eventForm.value.title,
                    scheduled_at: eventForm.value.scheduled_at,
                    location: eventForm.value.location,
                    attendees: selectedUserIds
                })
            } else {
                const matchRes = await api.post('/matches', {
                    team_id: eventForm.value.team_id,
                    opponent: eventForm.value.opponent,
                    is_home: eventForm.value.is_home,
                    scheduled_at: eventForm.value.scheduled_at,
                    location: eventForm.value.location
                })

                const matchId = matchRes.data.data.id

                const matchPlayers = availablePlayers.value.map((p) => ({
                    id: p.id,
                    attended: p.selected,
                    goals: 0,
                    assists: 0
                }))

                await api.put(`/matches/${matchId}/stats`, {
                    status: 'scheduled',
                    home_score: null,
                    away_score: null,
                    players: matchPlayers
                })
            }

            showCreateModal.value = false
            await fetchAllData()
        } catch (err) {
            alert(err.response?.data?.message || 'Greška pri kreiranju događaja')
        }
    }

    const openEventDetails = (event) => {
        selectedEvent.value = event
    }

    return {
        events,
        teams,
        availablePlayers,
        showCreateModal,
        selectedEvent,
        eventForm,
        fetchAllData,
        onTeamChange,
        openCreateModal,
        selectedPlayersCount,
        allSelected,
        toggleSelectAll,
        handleCreateEvent,
        openEventDetails
    }
}
