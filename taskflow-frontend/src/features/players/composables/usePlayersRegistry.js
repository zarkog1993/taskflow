// Composable koji objedinjuje podatke i akcije za registar igrača (lista, pretraga, kreiranje, brisanje).
import { ref, reactive, computed } from 'vue'
import { fetchTeams } from '../../../services/teamsService'
import { fetchPlayers, createPlayer, deletePlayer } from '../../../services/playersService'

export function usePlayersRegistry() {
    const players = ref([])
    const teams = ref([])
    const searchQuery = ref('')
    const selectedSeniority = ref('all')
    const isSubmitting = ref(false)
    const isDeleting = ref(false)

    const newPlayer = reactive({
        name: '',
        primary_position: 'CM',
        seniority: 'Seniori',
        height: null,
        weight: null,
        date_of_birth: '',
        preferred_foot: 'right',
        jersey_number: null,
        coach_notes: '',
        team_id: ''
    })

    const resetForm = () => {
        newPlayer.name = ''
        newPlayer.primary_position = 'CM'
        newPlayer.seniority = 'Seniori'
        newPlayer.height = null
        newPlayer.weight = null
        newPlayer.date_of_birth = ''
        newPlayer.preferred_foot = 'right'
        newPlayer.jersey_number = null
        newPlayer.coach_notes = ''
        newPlayer.team_id = teams.value[0]?.id || ''
    }

    const fetchData = async () => {
        try {
            const [playersData, teamsData] = await Promise.all([
                fetchPlayers(),
                fetchTeams()
            ])
            players.value = playersData
            teams.value = teamsData
            if (!newPlayer.team_id && teams.value.length) {
                newPlayer.team_id = teams.value[0].id
            }
        } catch (err) {
            console.error('Greška pri dohvatanju igrača:', err)
        }
    }

    const filteredPlayers = computed(() => {
        return players.value.filter((p) => {
            const q = searchQuery.value.toLowerCase()
            const matchesSearch = !q || p.name.toLowerCase().includes(q) || p.primary_position.toLowerCase().includes(q)
            const matchesSeniority = selectedSeniority.value === 'all' || p.seniority.toLowerCase() === selectedSeniority.value.toLowerCase()
            return matchesSearch && matchesSeniority
        })
    })

    // Kreira novog igrača slanjem multipart forme (radi upload-a slike)
    const handleCreatePlayer = async (selectedPhoto, { onSuccess } = {}) => {
        isSubmitting.value = true

        try {
            const formData = new FormData()
            formData.append('name', newPlayer.name)
            formData.append('primary_position', newPlayer.primary_position)
            formData.append('seniority', newPlayer.seniority)
            formData.append('team_id', newPlayer.team_id)

            if (newPlayer.jersey_number) formData.append('jersey_number', newPlayer.jersey_number)
            if (newPlayer.height) formData.append('height', newPlayer.height)
            if (newPlayer.weight) formData.append('weight', newPlayer.weight)
            if (newPlayer.date_of_birth) formData.append('date_of_birth', newPlayer.date_of_birth)
            if (newPlayer.preferred_foot) formData.append('preferred_foot', newPlayer.preferred_foot)
            if (newPlayer.coach_notes) formData.append('coach_notes', newPlayer.coach_notes)
            if (selectedPhoto) formData.append('photo', selectedPhoto)

            await createPlayer(formData)

            if (onSuccess) onSuccess()
            resetForm()
            await fetchData()
        } catch (err) {
            console.error('Greška pri kreiranju igrača:', err)
            alert(err.response?.data?.message || 'Došlo je do greške prilikom kreiranja igrača.')
        } finally {
            isSubmitting.value = false
        }
    }

    const handleDeletePlayer = async (player, { onSuccess } = {}) => {
        if (!player) return
        isDeleting.value = true
        try {
            await deletePlayer(player.id)
            if (onSuccess) onSuccess()
            await fetchData()
        } catch (err) {
            alert(err.response?.data?.message || 'Greška pri brisanju igrača')
        } finally {
            isDeleting.value = false
        }
    }

    return {
        players,
        teams,
        searchQuery,
        selectedSeniority,
        isSubmitting,
        isDeleting,
        newPlayer,
        resetForm,
        fetchData,
        filteredPlayers,
        handleCreatePlayer,
        handleDeletePlayer
    }
}
