// Composable koji objedinjuje podatke i akcije za super admin kontrolnu tablu:
// metrike platforme, listu klubova/korisnika/pretplata i odobravanje pretplata.
import { ref, onMounted } from 'vue'
import {
    approveSubscriptionRequest,
    cancelSubscriptionRequest,
    changeSubscriptionPlanRequest,
    deleteClubRequest,
    deleteUserRequest,
    fetchSuperAdminDashboard
} from '../services/superAdminService'

export function useSuperAdminDashboard() {
    const activeTab = ref('clubs')
    const loading = ref(true)

    const stats = ref({})
    const clubs = ref([])
    const users = ref([])
    const subscriptions = ref([])
    const plans = ref([])
    const confirmation = ref(null)
    const confirmationProcessing = ref(false)
    const confirmationError = ref('')

    const fetchDashboardData = async () => {
        try {
            const res = await fetchSuperAdminDashboard()
            stats.value = res.data.stats || {}
            clubs.value = res.data.clubs || []
            users.value = res.data.users || []
            subscriptions.value = res.data.subscriptions || []
            plans.value = res.data.plans || []
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

    const runAction = async (request, fallbackMessage) => {
        try {
            await request()
            await fetchDashboardData()
            return true
        } catch (err) {
            confirmationError.value = err.response?.data?.message || fallbackMessage
            return false
        }
    }

    const deleteUser = (user) => {
        confirmationError.value = ''
        confirmation.value = {
            title: 'Delete user',
            message: `Are you sure you want to delete "${user.name}"? This action cannot be undone.`,
            confirmLabel: 'Delete user',
            processingLabel: 'Deleting...',
            icon: '🗑️',
            request: () => deleteUserRequest(user.id),
            fallbackMessage: 'User could not be deleted.'
        }
    }

    const deleteClub = (club) => {
        confirmationError.value = ''
        confirmation.value = {
            title: 'Delete club',
            message: `Delete "${club.name}" and all of its club data? Associated users will remain without a club.`,
            confirmLabel: 'Delete club',
            processingLabel: 'Deleting...',
            icon: '🗑️',
            request: () => deleteClubRequest(club.id),
            fallbackMessage: 'Club could not be deleted.'
        }
    }

    const cancelSubscription = (subscription) => {
        confirmationError.value = ''
        confirmation.value = {
            title: 'Disable subscription',
            message: `Disable the ${subscription.plan_type} subscription for ${subscription.user?.name || 'this user'}? Club management access will be revoked.`,
            confirmLabel: 'Disable subscription',
            processingLabel: 'Disabling...',
            icon: '⚠️',
            request: () => cancelSubscriptionRequest(subscription.id),
            fallbackMessage: 'Subscription could not be disabled.'
        }
    }

    const closeConfirmation = () => {
        if (confirmationProcessing.value) return
        confirmation.value = null
        confirmationError.value = ''
    }

    const confirmAction = async () => {
        if (!confirmation.value || confirmationProcessing.value) return

        confirmationProcessing.value = true
        confirmationError.value = ''
        const action = confirmation.value
        const succeeded = await runAction(action.request, action.fallbackMessage)
        confirmationProcessing.value = false

        if (succeeded) {
            confirmation.value = null
        }
    }

    const changeSubscriptionPlan = async (subscription, planId) => {
        if (!planId) return

        try {
            await changeSubscriptionPlanRequest(subscription.id, Number(planId))
            await fetchDashboardData()
        } catch (err) {
            window.alert(err.response?.data?.message || 'Subscription plan could not be changed.')
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
        plans,
        confirmation,
        confirmationProcessing,
        confirmationError,
        approveSubscription,
        deleteUser,
        deleteClub,
        cancelSubscription,
        closeConfirmation,
        confirmAction,
        changeSubscriptionPlan
    }
}
