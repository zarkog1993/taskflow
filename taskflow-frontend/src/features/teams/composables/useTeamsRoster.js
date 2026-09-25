// Composable koji objedinjuje podatke i akcije za stranicu upravljanja ekipama.
import { ref, reactive, computed } from 'vue'
import { fetchTeams, createTeam } from '../../../services/teamsService'
import { fetchPlayers, assignPlayerToTeam as assignPlayerToTeamApi, removePlayerFromTeam as removePlayerFromTeamApi } from '../../../services/playersService'

export function useTeamsRoster() {
    const teams = ref([])
    const allPlayers = ref([])
    const isSubmitting = ref(false)

    const newTeam = reactive({
        name: '',
        age_group: 'senior'
    })

    // Dohvatanje timova i svih igrača sa backenda
    const fetchData = async () => {
        try {
            const [teamsData, playersData] = await Promise.all([
                fetchTeams(),
                fetchPlayers()
            ])
            teams.value = teamsData
            allPlayers.value = playersData
        } catch (err) {
            console.error('Greška pri učitavanju timova i igrača:', err)
        }
    }

    // Dobijanje igračkog kadra za određeni tim (radi i ako backend vraća $team->players ili spaja preko team_id)
    const getTeamPlayers = (team) => {
        if (team.players && team.players.length) return team.players
        return allPlayers.value.filter(p => p.team_id === team.id)
    }

    // Slobodni igrači bez ekipe
    const unassignedPlayers = computed(() => {
        return allPlayers.value.filter(p => !p.team_id)
    })

    // Ukupno i slobodni igrači
    const totalPlayersCount = computed(() => allPlayers.value.length)
    const freePlayersCount = computed(() => unassignedPlayers.value.length)

    // Kreiranje nove ekipe
    const handleCreateTeam = async ({ onSuccess } = {}) => {
        isSubmitting.value = true
        try {
            await createTeam(newTeam)
            if (onSuccess) onSuccess()
            newTeam.name = ''
            newTeam.age_group = 'senior'
            await fetchData()
        } catch (err) {
            alert(err.response?.data?.message || 'Greška pri kreiranju ekipe')
        } finally {
            isSubmitting.value = false
        }
    }

    // Dodavanje igrača u ekipu
    const assignPlayerToTeam = async (playerId, teamId, { onSuccess } = {}) => {
        try {
            await assignPlayerToTeamApi(playerId, teamId)
            if (onSuccess) onSuccess()
            await fetchData()
        } catch (err) {
            alert(err.response?.data?.message || 'Greška pri dodeljivanju igrača')
        }
    }

    // Uklanjanje igrača iz ekipe
    const removePlayerFromTeam = async (playerId) => {
        try {
            await removePlayerFromTeamApi(playerId)
            await fetchData()
        } catch (err) {
            alert(err.response?.data?.message || 'Greška pri uklanjanju igrača')
        }
    }

    return {
        teams,
        allPlayers,
        isSubmitting,
        newTeam,
        fetchData,
        getTeamPlayers,
        unassignedPlayers,
        totalPlayersCount,
        freePlayersCount,
        handleCreateTeam,
        assignPlayerToTeam,
        removePlayerFromTeam
    }
}
