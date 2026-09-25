// Composable koji učitava profil igrača i upravlja formom za izmenu podataka.
import { ref, reactive } from 'vue'
import { fetchPlayer, updatePlayerMultipart } from '../../../services/playersService'

export function usePlayerProfile(playerId) {
    const player = ref(null)
    const showEditModal = ref(false)
    const isSubmitting = ref(false)

    const editForm = reactive({
        name: '',
        email: '',
        primary_position: 'CM',
        seniority: 'Seniori',
        height: null,
        weight: null,
        date_of_birth: '',
        preferred_foot: 'right',
        jersey_number: null,
        coach_notes: ''
    })

    const fetchPlayerProfile = async () => {
        try {
            player.value = await fetchPlayer(playerId)
        } catch (err) {
            console.error('Greška pri učitavanju profila igrača:', err)
        }
    }

    const getStat = (field) => {
        if (!player.value) return 0
        if (player.value.stats && player.value.stats[field] !== undefined) {
            return player.value.stats[field]
        }
        return player.value[field] || 0
    }

    const openEditModal = () => {
        if (!player.value) return
        editForm.name = player.value.name || ''
        editForm.email = player.value.email || ''
        editForm.primary_position = player.value.primary_position || 'CM'
        editForm.seniority = player.value.seniority || 'Seniori'
        editForm.height = player.value.height || null
        editForm.weight = player.value.weight || null
        editForm.date_of_birth = player.value.date_of_birth || ''
        editForm.preferred_foot = player.value.preferred_foot || 'right'
        editForm.jersey_number = player.value.jersey_number || null
        editForm.coach_notes = player.value.coach_notes || ''
        showEditModal.value = true
    }

    const handleUpdatePlayer = async (selectedPhoto, { onSuccess } = {}) => {
        isSubmitting.value = true

        try {
            const formData = new FormData()
            // Laravel Multipart POST sa simularnim PUT pretvaranjem (_method)
            formData.append('_method', 'PUT')
            formData.append('name', editForm.name)
            formData.append('primary_position', editForm.primary_position)
            formData.append('seniority', editForm.seniority)

            if (editForm.email) formData.append('email', editForm.email)
            if (editForm.jersey_number) formData.append('jersey_number', editForm.jersey_number)
            if (editForm.height) formData.append('height', editForm.height)
            if (editForm.weight) formData.append('weight', editForm.weight)
            if (editForm.date_of_birth) formData.append('date_of_birth', editForm.date_of_birth)
            if (editForm.preferred_foot) formData.append('preferred_foot', editForm.preferred_foot)
            if (editForm.coach_notes) formData.append('coach_notes', editForm.coach_notes)
            if (selectedPhoto) formData.append('photo', selectedPhoto)

            await updatePlayerMultipart(player.value.id, formData)

            if (onSuccess) onSuccess()
            await fetchPlayerProfile()
        } catch (err) {
            console.error('Greška pri ažuriranju igrača:', err)
            alert(err.response?.data?.message || 'Došlo je do greške prilikom ažuriranja igrača.')
        } finally {
            isSubmitting.value = false
        }
    }

    const formatFoot = (foot) => {
        if (foot === 'right') return 'Desna'
        if (foot === 'left') return 'Leva'
        if (foot === 'both') return 'Obe'
        return 'Nije podešeno'
    }

    return {
        player,
        showEditModal,
        isSubmitting,
        editForm,
        fetchPlayerProfile,
        getStat,
        openEditModal,
        handleUpdatePlayer,
        formatFoot
    }
}
