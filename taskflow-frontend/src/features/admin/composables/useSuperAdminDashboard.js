// Composable koji objedinjuje podatke i akcije za super admin kontrolnu tablu:
// metrike platforme, listu klubova/korisnika/pretplata i odobravanje pretplata.
import { ref, onMounted } from 'vue'
import { fetchSuperAdminDashboard, approveSubscriptionRequest } from '../services/superAdminService'

export function useSuperAdminDashboard() {
    const activeTab = ref('clubs')
    const loading = ref(true)

    const stats = ref({})
    const clubs = ref([])
    const users = ref([])
    const subscriptions = ref([])

    const fetchDashboardData = async () => {
        try {
            const res = await fetchSuperAdminDashboard()
            stats.value = res.data.stats || {}
            clubs.value = res.data.clubs || []
            users.value = res.data.users || []
            subscriptions.value = res.data.subscriptions || []
        } catch (err) {
            console.error('Greška pri učitavanju super admin podataka:', err)
        } finally {
            loading.value = false
        }
    }

    const approveSubscription = async (id) => {
        try {
            await approveSubscriptionRequest(id)
            await fetchDashboardData()
        } catch (err) {
            console.error('Greška pri odobravanju pretplate:', err)
            window.alert(err.response?.data?.message || 'Pretplata nije mogla biti odobrena.')
        }
    }

    onMounted(fetchDashboardData)

    return {
        activeTab,
        loading,
        stats,
        clubs,
        users,
        subscriptions,
        approveSubscription
    }
}
