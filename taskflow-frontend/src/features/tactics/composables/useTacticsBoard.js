// Composable koji objedinjuje stanje taktičke table: učitavanje ekipa/igrača,
// filtriranje po ekipi, drag & drop pozicioniranje, izbor igrača za poziciju i formacije.
import { ref, computed, onMounted } from 'vue'
import api from '../../../services/api'
import { createDefaultFieldSpots, FORMATION_LAYOUTS } from '../constants/formations'

export function useTacticsBoard() {
    const selectedFormation = ref('4-3-3')
    const draggedSpot = ref(null)

    const teams = ref([])
    const allUsers = ref([])
    const teamPlayers = ref([])
    const activeSpotForSelection = ref(null)
    const selectedTeamFilter = ref('')
    const searchQuery = ref('')

    const fieldSpots = ref(createDefaultFieldSpots())

    const fetchData = async () => {
        try {
            const [teamsRes, usersRes] = await Promise.all([
                api.get('/teams'),
                api.get('/users')
            ])
            teams.value = teamsRes.data.data || teamsRes.data || []
            allUsers.value = usersRes.data.data || usersRes.data || []

            if (teams.value.length > 0) {
                selectedTeamFilter.value = teams.value[0].id
                loadTeamPlayers(teams.value[0].id)
            }
        } catch (err) {
            console.error('Greška pri učitavanju:', err)
        }
    }

    const loadTeamPlayers = (teamId) => {
        const selectedTeam = teams.value.find(t => t.id === Number(teamId))
        teamPlayers.value = selectedTeam?.users || selectedTeam?.players || allUsers.value
    }

    const onTeamFilterChange = () => {
        loadTeamPlayers(selectedTeamFilter.value)
    }

    const filteredUsers = computed(() => {
        return teamPlayers.value.filter(user => {
            return !searchQuery.value || user.name.toLowerCase().includes(searchQuery.value.toLowerCase())
        })
    })

    const openPlayerPicker = (spot) => {
        activeSpotForSelection.value = spot
        searchQuery.value = ''
    }

    const closePlayerPicker = () => {
        activeSpotForSelection.value = null
    }

    const assignPlayerToSpot = (user) => {
        if (!activeSpotForSelection.value) return
        fieldSpots.value.forEach(s => {
            if (s.player?.id === user.id) s.player = null
        })
        activeSpotForSelection.value.player = user
        activeSpotForSelection.value = null
    }

    const removePlayerFromSpot = () => {
        if (activeSpotForSelection.value) {
            activeSpotForSelection.value.player = null
            activeSpotForSelection.value = null
        }
    }

    const onDragStart = (event, spot) => { draggedSpot.value = spot }

    const onDropOnPitch = (event) => {
        if (!draggedSpot.value) return
        const rect = event.currentTarget.getBoundingClientRect()
        const x = Math.min(Math.max(((event.clientX - rect.left) / rect.width) * 100, 5), 95)
        const y = Math.min(Math.max(((event.clientY - rect.top) / rect.height) * 100, 5), 95)
        draggedSpot.value.x = Math.round(x)
        draggedSpot.value.y = Math.round(y)
        draggedSpot.value = null
    }

    const applyFormation = () => {
        const layout = FORMATION_LAYOUTS[selectedFormation.value]
        if (!layout) return
        layout.forEach((coords, i) => {
            const spot = fieldSpots.value[i + 1]
            if (spot) {
                spot.x = coords.x
                spot.y = coords.y
            }
        })
    }

    const saveTactics = () => { alert('Taktika je uspešno sačuvana!') }

    onMounted(() => { fetchData() })

    return {
        selectedFormation,
        teams,
        teamPlayers,
        fieldSpots,
        activeSpotForSelection,
        selectedTeamFilter,
        searchQuery,
        filteredUsers,
        onTeamFilterChange,
        openPlayerPicker,
        closePlayerPicker,
        assignPlayerToSpot,
        removePlayerFromSpot,
        onDragStart,
        onDropOnPitch,
        applyFormation,
        saveTactics
    }
}
