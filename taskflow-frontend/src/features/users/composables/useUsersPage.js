// Composable koji objedinjuje podatke i akcije za stranicu upravljanja korisnicima:
// listu korisnika/klubova, kreiranje novog korisnika i brisanje postojećeg.
import { ref, reactive, onMounted } from 'vue'
import { fetchUsers, fetchClubs, createUser, deleteUser as deleteUserRequest } from '../services/usersAdminService'

export function useUsersPage() {
    const users = ref([])
    const clubs = ref([])
    const loading = ref(true)
    const showCreateModal = ref(false)
    const isSubmitting = ref(false)

    const newUser = reactive({
        name: '',
        email: '',
        password: '',
        club_id: null,
        role: 'player'
    })

    const fetchData = async () => {
        try {
            const [usersData, clubsData] = await Promise.all([
                fetchUsers(),
                fetchClubs()
            ])
            users.value = usersData
            clubs.value = clubsData
        } catch (err) {
            console.error('Greška pri učitavanju podataka:', err)
        } finally {
            loading.value = false
        }
    }

    onMounted(fetchData)

    const closeModal = () => {
        showCreateModal.value = false
        newUser.name = ''
        newUser.email = ''
        newUser.password = ''
        newUser.club_id = null
        newUser.role = 'player'
    }

    const handleCreateUser = async () => {
        isSubmitting.value = true
        try {
            await createUser(newUser)
            closeModal()
            await fetchData()
        } catch (err) {
            alert(err.response?.data?.message || 'Greška pri kreiranju korisnika')
        } finally {
            isSubmitting.value = false
        }
    }

    const deleteUser = async (userId) => {
        if (!confirm('Da li ste sigurni da želite da obrišete ovog korisnika?')) return
        try {
            await deleteUserRequest(userId)
            await fetchData()
        } catch (err) {
            alert(err.response?.data?.message || 'Greška pri brisanju korisnika')
        }
    }

    return {
        users,
        clubs,
        loading,
        showCreateModal,
        isSubmitting,
        newUser,
        handleCreateUser,
        deleteUser,
        closeModal
    }
}
