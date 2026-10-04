// Composable koji objedinjuje stanje taktičke table: učitavanje ekipa/igrača,
// filtriranje po ekipi, drag & drop pozicioniranje, izbor igrača za poziciju i formacije.
import { ref, computed, onMounted } from 'vue'
import api from '../../../services/api'
import { createDefaultFieldSpots, FORMATION_LAYOUTS } from '../constants/formations'

export function useTacticsBoard() {
    const selectedFormation = ref('4-3-3')
    const draggedSpot = ref(null)

    const teams = ref([])
    const teamPlayers = ref([])
    const activeSpotForSelection = ref(null)
    const selectedTeamFilter = ref('')
    const searchQuery = ref('')
    const errorMessage = ref('')
    const saveMessage = ref('')
    const isLoading = ref(false)
    const isSaving = ref(false)
    let teamLoadVersion = 0

    const fieldSpots = ref(createDefaultFieldSpots())

    const fetchData = async () => {
        isLoading.value = true
        try {
            const teamsRes = await api.get('/teams')
            teams.value = teamsRes.data.data || teamsRes.data || []

            if (teams.value.length > 0) {
                selectedTeamFilter.value = teams.value[0].id
                await loadTeamTactics(teams.value[0].id)
            }
        } catch (err) {
            errorMessage.value = 'Nije moguće učitati ekipe i taktiku.'
        } finally {
            isLoading.value = false
        }
    }

    const loadTeamTactics = async (teamId) => {
        const requestVersion = ++teamLoadVersion
        const selectedTeam = teams.value.find(team => Number(team.id) === Number(teamId))
        teamPlayers.value = selectedTeam?.players || []
        fieldSpots.value = createDefaultFieldSpots()
        selectedFormation.value = '4-3-3'
        errorMessage.value = ''
        saveMessage.value = ''

        if (!selectedTeam) {
            isLoading.value = false
            return
        }

        isLoading.value = true
        try {
            const response = await api.get(`/teams/${teamId}/tactics`)
            if (requestVersion !== teamLoadVersion) return

            const tactic = response.data.data
            if (!tactic) return

            selectedFormation.value = tactic.formation
            const savedPositions = new Map(
                tactic.positions.map(position => [Number(position.spot_id), position]),
            )
            fieldSpots.value = createDefaultFieldSpots().map(spot => {
                const savedPosition = savedPositions.get(spot.id)
                const player = teamPlayers.value.find(
                    teamPlayer => Number(teamPlayer.id) === Number(savedPosition?.player_id),
                ) || null

                return savedPosition
                    ? { ...spot, x: Number(savedPosition.x), y: Number(savedPosition.y), player }
                    : spot
            })
        } catch (err) {
            if (requestVersion === teamLoadVersion) {
                errorMessage.value = 'Nije moguće učitati sačuvanu taktiku.'
            }
        } finally {
            if (requestVersion === teamLoadVersion) {
                isLoading.value = false
            }
        }
    }

    const onTeamFilterChange = () => {
        loadTeamTactics(selectedTeamFilter.value)
    }

    const filteredUsers = computed(() => {
        return teamPlayers.value.filter(player => {
            return !searchQuery.value || player.name.toLowerCase().includes(searchQuery.value.toLowerCase())
        })
    })

    const openPlayerPicker = (spot) => {
        activeSpotForSelection.value = spot
        searchQuery.value = ''
    }

    const closePlayerPicker = () => {
        activeSpotForSelection.value = null
    }

    const assignPlayerToSpot = (player) => {
        if (!activeSpotForSelection.value) return
        fieldSpots.value.forEach(s => {
            if (s.player?.id === player.id) s.player = null
        })
        activeSpotForSelection.value.player = player
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

    const saveTactics = async () => {
        if (!selectedTeamFilter.value || isSaving.value) return

        isSaving.value = true
        errorMessage.value = ''
        saveMessage.value = ''
        try {
            await api.put(`/teams/${selectedTeamFilter.value}/tactics`, {
                formation: selectedFormation.value,
                positions: fieldSpots.value.map(spot => ({
                    spot_id: spot.id,
                    x: spot.x,
                    y: spot.y,
                    player_id: spot.player?.id ?? null,
                })),
            })
            saveMessage.value = 'Taktika je sačuvana.'
        } catch (err) {
            errorMessage.value = err.response?.data?.message || 'Nije moguće sačuvati taktiku.'
        } finally {
            isSaving.value = false
        }
    }

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
        saveTactics,
        errorMessage,
        saveMessage,
        isLoading,
        isSaving
    }
}
