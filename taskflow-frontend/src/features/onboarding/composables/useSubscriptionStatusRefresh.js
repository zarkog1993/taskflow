// Composable koji pri montiranju osvežava status pretplate trenutno prijavljenog korisnika
// i preusmerava na dashboard čim je pretplata odobrena/aktivna.
import { onMounted } from 'vue'
import { useRouter } from 'vue-router'
import api from '../../../services/api'
import { useAuthStore } from '../../../stores/auth'

export function useSubscriptionStatusRefresh() {
    const router = useRouter()
    const authStore = useAuthStore()

    onMounted(async () => {
        try {
            const response = await api.get('/me')
            authStore.user = response.data
            localStorage.setItem('user', JSON.stringify(response.data))
            if (['approved', 'active'].includes(response.data.subscription_status)) {
                router.replace('/')
            }
        } catch (error) {
            console.error('Unable to refresh subscription status:', error)
        }
    })
}
