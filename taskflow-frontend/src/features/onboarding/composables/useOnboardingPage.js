// Composable koji objedinjuje stanje stranice za onboarding kluba: učitavanje kluba i
// dostupnih paketa preko tokena iz rute, izbor paketa i slanje odabira na odobrenje.
import { onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '../../../services/api'

export function useOnboardingPage() {
    const route = useRoute()
    const router = useRouter()
    const club = ref(null)
    const plans = ref([])
    const selectedPlan = ref('')
    const loading = ref(true)
    const submitting = ref(false)
    const message = ref('')
    const error = ref('')

    onMounted(async () => {
        try {
            const response = await api.get(`/onboarding/${route.params.token}`)
            club.value = response.data.club
            plans.value = response.data.plans || []
            selectedPlan.value = plans.value[0]?.slug || ''
        } catch (err) {
            error.value = err.response?.data?.message || 'This onboarding link is invalid or expired.'
        } finally {
            loading.value = false
        }
    })

    const submit = async () => {
        submitting.value = true
        error.value = ''
        try {
            await api.post(`/onboarding/${route.params.token}/select`, { plan_type: selectedPlan.value })
            message.value = 'Package selected. Your subscription is pending administrator approval.'
            setTimeout(() => router.push('/login'), 1800)
        } catch (err) {
            error.value = err.response?.data?.message || 'Unable to select this package.'
        } finally {
            submitting.value = false
        }
    }

    return {
        club,
        plans,
        selectedPlan,
        loading,
        submitting,
        message,
        error,
        submit
    }
}
