// Composable koji učitava detalje ekipe sa sastavom igrača i upravlja izmenom učinka igrača.
import { ref, reactive, computed } from 'vue'
import { fetchTeam } from '../../../services/teamsService'
import { fetchPlayers, updatePlayer } from '../../../services/playersService'

export function useTeamDetail(teamId) {
    const team = ref(null)
    const loading = ref(true)
    const showEditModal = ref(false)
    const selectedPlayer = ref(null)

    const statsForm = reactive({
        trainings_attended: 0,
        matches_played: 0,
        goals: 0,
        assists: 0,
        seniority: 'Seniori',
        preferred_foot: 'right'
    })

    // Prihvata listu igrača za tim nezavisno od toga da li stiže kao team.players ili team.members
    const teamPlayers = computed(() => {
        if (!team.value) return []
        return team.value.players || team.value.members || []
    })

    // Pomoćna funkcija za siguran pristup statistici bez obzira da li je u stats objektu ili na samom igraču
    const getStat = (player, field) => {
        if (player.stats && player.stats[field] !== undefined) {
            return player.stats[field]
        }
        return player[field] || 0
    }

    const loadTeamData = async () => {
        loading.value = true
        try {
            const [foundTeam, allPlayers] = await Promise.all([
                fetchTeam(teamId),
                fetchPlayers()
            ])

            const targetTeamId = Number(teamId)

            if (foundTeam) {
                // Povezujemo igrače proverom Number(p.team_id) ili proveravamo da li je backend već poslao t.players
                const assignedPlayers = allPlayers.filter(p => Number(p.team_id) === targetTeamId)

                foundTeam.players = (foundTeam.players && foundTeam.players.length)
                    ? foundTeam.players
                    : assignedPlayers

                team.value = foundTeam
            }
        } catch (err) {
            console.error('Greška pri učitavanju tima:', err)
        } finally {
            loading.value = false
        }
    }

    const openEditModal = (player) => {
        selectedPlayer.value = player
        statsForm.trainings_attended = getStat(player, 'trainings_attended')
        statsForm.matches_played = getStat(player, 'matches_played')
        statsForm.goals = getStat(player, 'goals')
        statsForm.assists = getStat(player, 'assists')
        statsForm.seniority = player.seniority || 'Seniori'
        statsForm.preferred_foot = player.preferred_foot || 'right'
        showEditModal.value = true
    }

    const handleSaveStats = async () => {
        if (!selectedPlayer.value) return

        try {
            const payload = {
                matches_played: statsForm.matches_played,
                trainings_attended: statsForm.trainings_attended,
                goals: statsForm.goals,
                assists: statsForm.assists,
                seniority: statsForm.seniority,
                preferred_foot: statsForm.preferred_foot
            }

            await updatePlayer(selectedPlayer.value.id, payload)
            showEditModal.value = false
            await loadTeamData() // Ponovo učitavamo podatke sa svežom statistikom
        } catch (err) {
            console.error('Greška pri čuvanju statistike:', err)
            alert(err.response?.data?.message || 'Greška pri čuvanju statistike')
        }
    }

    return {
        team,
        loading,
        showEditModal,
        selectedPlayer,
        statsForm,
        teamPlayers,
        getStat,
        loadTeamData,
        openEditModal,
        handleSaveStats
    }
}
