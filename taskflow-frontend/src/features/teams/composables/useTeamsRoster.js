// Composable koji objedinjuje podatke i akcije za stranicu upravljanja ekipama.
import { ref, reactive, computed } from 'vue'
import { useAuthStore } from '../../../stores/auth'
import { fetchTeams, createTeam } from '../../../services/teamsService'
import { fetchPlayers, assignPlayerToTeam as assignPlayerToTeamApi, removePlayerFromTeam as removePlayerFromTeamApi } from '../../../services/playersService'
import { getAgeGroupForAge, getAgeGroupLimit, getPlayerAge } from '../../players/utils/playerAge'

export function useTeamsRoster() {
    const authStore = useAuthStore()
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
            let [teamsData, playersData] = await Promise.all([
                fetchTeams(),
                fetchPlayers()
            ])

            const user = authStore.user
            const canPromotePlayers = user?.is_admin || user?.roles?.some(role =>
                ['admin', 'super-admin', 'club-admin'].includes(role.slug)
            )

            if (canPromotePlayers) {
                const playerCounts = new Map()
                for (const player of playersData) {
                    if (player.team_id) {
                        const teamId = Number(player.team_id)
                        playerCounts.set(teamId, (playerCounts.get(teamId) || 0) + 1)
                    }
                }

                let didPromotePlayer = false
                for (const player of playersData) {
                    const currentTeam = teamsData.find(team => Number(team.id) === Number(player.team_id))
                    const age = getPlayerAge(player)
                    const ageLimit = getAgeGroupLimit(currentTeam?.age_group)
                    if (!currentTeam || age === null || ageLimit === null || age <= ageLimit) continue

                    const destinationAgeGroup = getAgeGroupForAge(age)
                    const destinationTeams = teamsData
                        .filter(team => String(team.age_group).toLowerCase() === destinationAgeGroup
                            && Number(team.club_id) === Number(currentTeam.club_id))
                        .sort((first, second) =>
                            (playerCounts.get(Number(first.id)) || 0) - (playerCounts.get(Number(second.id)) || 0)
                            || Number(first.id) - Number(second.id)
                        )
                    const destinationTeam = destinationTeams[0]

                    if (!destinationTeam) continue

                    try {
                        await assignPlayerToTeamApi(player.id, destinationTeam.id)
                        playerCounts.set(Number(currentTeam.id), Math.max(0, (playerCounts.get(Number(currentTeam.id)) || 1) - 1))
                        playerCounts.set(Number(destinationTeam.id), (playerCounts.get(Number(destinationTeam.id)) || 0) + 1)
                        didPromotePlayer = true
                    } catch (error) {
                        console.error(`Automatsko premeštanje igrača ${player.id} nije uspelo:`, error)
                    }
                }

                if (didPromotePlayer) {
                    [teamsData, playersData] = await Promise.all([fetchTeams(), fetchPlayers()])
                }
            }

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
